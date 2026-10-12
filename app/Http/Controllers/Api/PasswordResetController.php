<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\CodeReinitialisationMotDePasse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Réinitialisation du mot de passe par code à 6 chiffres envoyé par e-mail.
 *
 * Le code est stocké haché dans `password_reset_tokens`, comme le ferait le
 * broker de Laravel : une fuite de la base ne permet pas de réinitialiser les
 * comptes.
 */
class PasswordResetController extends Controller
{
    /** Durée de validité d'un code, en minutes. */
    private const VALIDITE_MINUTES = 15;

    /** Nombre de demandes d'envoi autorisées par heure et par adresse. */
    private const ENVOIS_PAR_HEURE = 5;

    /** Nombre de tentatives de saisie autorisées par heure et par adresse. */
    private const TENTATIVES_PAR_HEURE = 10;

    /**
     * ÉTAPE 1 — Demander un code.
     *
     * Répond toujours de la même façon, que l'adresse existe ou non : sinon
     * l'endpoint devient un moyen de savoir qui a un compte chez Tokende.
     */
    public function demanderCode(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'L’adresse e-mail est obligatoire.',
            'email.email' => 'Cette adresse e-mail n’est pas valide.',
        ]);

        $email = mb_strtolower(trim($validated['email']));

        $cle = 'reinit-envoi:' . $email;

        if (RateLimiter::tooManyAttempts($cle, self::ENVOIS_PAR_HEURE)) {
            return response()->json([
                'message' =>
                    'Trop de demandes. Réessayez dans '
                    . ceil(RateLimiter::availableIn($cle) / 60)
                    . ' minutes.',
            ], 429);
        }

        RateLimiter::hit($cle, 3600);

        $user = User::where('email', $email)->first();

        if ($user) {
            $code = $this->genererCode();

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => Hash::make($code),
                    'created_at' => now(),
                ]
            );

            try {
                Mail::to($email)->send(
                    new CodeReinitialisationMotDePasse(
                        $user->name ?? 'client',
                        $code,
                        self::VALIDITE_MINUTES,
                    )
                );
            } catch (\Throwable $e) {
                // Ne jamais journaliser le code lui-même.
                Log::error('Envoi du code de réinitialisation impossible', [
                    'email' => $email,
                    'erreur' => $e->getMessage(),
                ]);

                return response()->json([
                    'message' =>
                        'Impossible d’envoyer l’e-mail pour le moment. '
                        . 'Réessayez plus tard.',
                ], 500);
            }
        }

        return response()->json([
            'message' =>
                'Si un compte existe pour cette adresse, un code de '
                . 'vérification vient de lui être envoyé.',

            'validite_minutes' => self::VALIDITE_MINUTES,
        ]);
    }

    /**
     * ÉTAPE 2 — Vérifier le code sans encore changer le mot de passe.
     *
     * Permet à l'application de valider la saisie avant d'afficher le
     * formulaire de nouveau mot de passe.
     */
    public function verifierCode(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string'],
        ], [
            'email.required' => 'L’adresse e-mail est obligatoire.',
            'email.email' => 'Cette adresse e-mail n’est pas valide.',
            'code.required' => 'Le code de vérification est obligatoire.',
        ]);

        $email = mb_strtolower(trim($validated['email']));

        $this->verifierLimiteTentatives($email);

        RateLimiter::hit('reinit-tentative:' . $email, 3600);

        $this->recupererEnregistrementValide($email, $validated['code']);

        return response()->json([
            'message' => 'Code vérifié.',
        ]);
    }

    /**
     * ÉTAPE 3 — Définir le nouveau mot de passe.
     */
    public function reinitialiser(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            // L'application est en français mais APP_LOCALE vaut « en » :
            // on fournit les messages ici plutôt que de changer la locale
            // globale du projet.
            'email.required' => 'L’adresse e-mail est obligatoire.',
            'email.email' => 'Cette adresse e-mail n’est pas valide.',
            'code.required' => 'Le code de vérification est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' =>
                'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' =>
                'Les deux mots de passe ne correspondent pas.',
        ]);

        $email = mb_strtolower(trim($validated['email']));

        $this->verifierLimiteTentatives($email);

        RateLimiter::hit('reinit-tentative:' . $email, 3600);

        $this->recupererEnregistrementValide($email, $validated['code']);

        $user = User::where('email', $email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Aucun compte ne correspond à cette adresse.'],
            ]);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        // Le code a servi : il ne doit plus pouvoir resservir.
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->delete();

        // Un mot de passe changé invalide les sessions ouvertes ailleurs.
        $user->tokens()->delete();

        RateLimiter::clear('reinit-tentative:' . $email);
        RateLimiter::clear('reinit-envoi:' . $email);

        return response()->json([
            'message' =>
                'Mot de passe mis à jour. Vous pouvez vous connecter.',
        ]);
    }

    // ----------------------------------------------------------------
    // Outils internes
    // ----------------------------------------------------------------

    /** Code à 6 chiffres tiré d'une source cryptographique. */
    private function genererCode(): string
    {
        return str_pad(
            (string) random_int(0, 999999),
            6,
            '0',
            STR_PAD_LEFT
        );
    }

    private function verifierLimiteTentatives(string $email): void
    {
        $cle = 'reinit-tentative:' . $email;

        if (RateLimiter::tooManyAttempts($cle, self::TENTATIVES_PAR_HEURE)) {
            throw ValidationException::withMessages([
                'code' => [
                    'Trop de tentatives. Réessayez dans '
                    . ceil(RateLimiter::availableIn($cle) / 60)
                    . ' minutes.',
                ],
            ]);
        }
    }

    /**
     * Retrouve l'enregistrement correspondant au code, ou lève une erreur de
     * validation. Un code absent, faux ou expiré renvoie le même message.
     */
    private function recupererEnregistrementValide(
        string $email,
        string $code,
    ): object {
        $enregistrement = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        $invalide = ValidationException::withMessages([
            'code' => ['Ce code est invalide ou a expiré.'],
        ]);

        if (!$enregistrement) {
            throw $invalide;
        }

        $expireLe = \Carbon\Carbon::parse($enregistrement->created_at)
            ->addMinutes(self::VALIDITE_MINUTES);

        if (now()->greaterThan($expireLe)) {
            DB::table('password_reset_tokens')
                ->where('email', $email)
                ->delete();

            throw $invalide;
        }

        if (!Hash::check(trim($code), $enregistrement->token)) {
            throw $invalide;
        }

        return $enregistrement;
    }
}
