<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Achat;

class AchatController extends Controller
{
    public function index()
    {
        return response()->json(Achat::all());
    }

    public function store(Request $request)
    {
        $achat = Achat::create([
            'user_id' => $request->user_id,
            'montant' => $request->montant,
            'description' => $request->description,
        ]);

        return response()->json([
            "message" => "Achat ajouté avec succès",
            "data" => $achat
        ]);
    }

    public function show($id)
    {
        return response()->json(Achat::findOrFail($id));
    }

    public function destroy($id)
    {
        Achat::destroy($id);

        return response()->json([
            "message" => "Achat supprimé avec succès"
        ]);
    }
}