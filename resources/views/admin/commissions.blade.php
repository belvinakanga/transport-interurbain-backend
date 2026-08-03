<x-layouts.admin>

    <div class="p-8">

        <h1 class="text-4xl font-bold mb-8">
            💰 Gestion des commissions
        </h1>

        <div class="bg-white rounded-2xl shadow overflow-hidden">

            <table class="w-full">

                <thead class="bg-gray-100">

                    <tr>
                        <th class="p-5 text-left">Agence</th>
                        <th class="p-5 text-left">Montant payé</th>
                        <th class="p-5 text-left">Commission (10%)</th>
                        <th class="p-5 text-left">Date</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($paiements as $paiement)

                        <tr class="border-b hover:bg-gray-50">

                            <td class="p-5">
                                {{ $paiement->reservation->trajet->agence->nom_agence ?? 'Agence inconnue' }}
                            </td>

                            <td class="p-5">
                                {{ number_format($paiement->montant,0,',',' ') }} FCFA
                            </td>

                            <td class="p-5 font-bold text-green-600">
                                {{ number_format($paiement->montant * 0.10,0,',',' ') }} FCFA
                            </td>

                            <td class="p-5">
                                {{ $paiement->created_at->format('d/m/Y') }}
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4" class="text-center text-gray-500 py-10">

                                Aucune commission disponible.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-layouts.admin>