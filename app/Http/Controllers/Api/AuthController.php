<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * INSCRIPTION
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],
        ]);

        $user = User::create([
            'name' => $validated['name'],

            'email' => $validated['email'],

            'password' => Hash::make(
                $validated['password']
            ),
        ]);

        $token = $user->createToken(
            'flutter-app'
        )->plainTextToken;

        return response()->json([
            'message' =>
                'Compte créé avec succès.',

            'user' =>
                $user,

            'token' =>
                $token,
        ], 201);
    }


    /**
     * CONNEXION
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'string',
            ],
        ]);

        $user = User::where(
            'email',
            $validated['email']
        )->first();

        if (
            !$user ||
            !Hash::check(
                $validated['password'],
                $user->password
            )
        ) {
            throw ValidationException::withMessages([
                'email' => [
                    'Les identifiants sont incorrects.',
                ],
            ]);
        }

        // Supprimer les anciens tokens
        $user->tokens()->delete();

        // Créer un nouveau token
        $token = $user->createToken(
            'flutter-app'
        )->plainTextToken;

        return response()->json([
            'message' =>
                'Connexion réussie.',

            'user' =>
                $user,

            'token' =>
                $token,
        ]);
    }


    /**
     * UTILISATEUR CONNECTÉ
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' =>
                $request->user(),
        ]);
    }


    /**
     * DÉCONNEXION
     */
    public function logout(Request $request)
    {
        $request
            ->user()
            ->currentAccessToken()
            ->delete();

        return response()->json([
            'message' =>
                'Déconnexion réussie.',
        ]);
    }
}