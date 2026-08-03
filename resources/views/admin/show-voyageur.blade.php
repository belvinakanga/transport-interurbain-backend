<x-layouts.admin>

<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-xl shadow p-8">

        <h1 class="text-3xl font-bold mb-8">
            👤 Détails du voyageur
        </h1>

        <div class="space-y-6">

            <div>
                <strong>ID :</strong>
                {{ $voyageur->id }}
            </div>

            <div>
                <strong>Nom :</strong>
                {{ $voyageur->name }}
            </div>

            <div>
                <strong>Email :</strong>
                {{ $voyageur->email }}
            </div>

            <div>
                <strong>Rôle :</strong>
                {{ ucfirst($voyageur->role) }}
            </div>

            <div>
                <strong>Date d'inscription :</strong>
                {{ $voyageur->created_at->format('d/m/Y H:i') }}
            </div>

        </div>

        <div class="mt-10 flex gap-4">

            <a href="/admin/voyageurs"
               class="bg-gray-600 hover:bg-gray-700 text-white px-5 py-2 rounded-lg">
                ⬅ Retour
            </a>

            <button onclick="window.print()"
                    class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg">
                🖨 Imprimer
            </button>

        </div>

    </div>

</div>

</x-layouts.admin>