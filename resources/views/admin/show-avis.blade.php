<x-layouts.admin>

<div class="max-w-3xl mx-auto">

    <div class="bg-white rounded-xl shadow p-8">

        <h1 class="text-3xl font-bold mb-8">
            ⭐ Détails de l'avis
        </h1>

        <div class="space-y-6">

            <div>
                <h3 class="font-bold text-lg">👤 Voyageur</h3>
                <p>{{ $avis->user?->name ?? 'Utilisateur supprimé' }}</p>
            </div>

            <div>
                <h3 class="font-bold text-lg">⭐ Note</h3>

                <p class="text-yellow-500 text-2xl">
                    @for($i = 1; $i <= $avis->note; $i++)
                        ⭐
                    @endfor
                </p>
            </div>

            <div>
                <h3 class="font-bold text-lg">💬 Commentaire</h3>
                <p>{{ $avis->commentaire }}</p>
            </div>

            <div>
                <h3 class="font-bold text-lg">📅 Date</h3>
                <p>{{ $avis->created_at->format('d/m/Y') }}</p>
            </div>

        </div>

        <div class="mt-10 flex gap-4">

            <a href="{{ url('/admin/avis') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg">

                ⬅ Retour

            </a>

            <button
                onclick="window.print()"
                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg">

                🖨 Imprimer

            </button>

        </div>

    </div>

</div>

</x-layouts.admin>