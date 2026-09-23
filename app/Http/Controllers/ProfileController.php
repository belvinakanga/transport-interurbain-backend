<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     *
     * This method works for both:
     * - Laravel web profile
     * - Flutter API /api/profile
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();

        // Mettre à jour les informations validées.
        $user->fill($request->validated());

        // Si l'adresse e-mail a changé,
        // elle devra être vérifiée à nouveau.
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Enregistrer dans la base de données.
        $user->save();

        // Si la requête vient de Flutter/API,
        // retourner une réponse JSON.
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Profil mis à jour avec succès.',
                'user' => $user,
            ], 200);
        }

        // Conserver le fonctionnement du profil web Laravel.
        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}