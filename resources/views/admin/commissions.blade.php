<x-layouts.admin>

    <div class="p-8">

        <h1 class="text-4xl font-bold mb-8">
            💰 Commissions Tokende
        </h1>

        <div class="bg-white rounded-2xl shadow overflow-hidden">

            <table class="w-full">

                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-5 text-left">
                            Agence
                        </th>

                        <th class="p-5 text-left">
                            Billets vendus
                        </th>

                        <th class="p-5 text-left">
                            Frais générés
                        </th>

                        <th class="p-5 text-left">
                            Part Tokende
                        </th>

                        <th class="p-5 text-left">
                            Part agence
                        </th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($commissions as $commission)

                        <tr class="border-b hover:bg-gray-50">

                            <td class="p-5 font-medium">
                                {{ $commission['agence']->nom_agence }}
                            </td>

                            <td class="p-5">
                                {{ $commission['nombre_billets'] }}
                            </td>

                            <td class="p-5">
                                {{ number_format($commission['frais_generes'], 0, ',', ' ') }}
                                FCFA
                            </td>

                            <td class="p-5 font-bold text-green-600">
                                {{ number_format($commission['part_tokende'], 0, ',', ' ') }}
                                FCFA
                            </td>

                            <td class="p-5">
                                {{ number_format($commission['part_agence'], 0, ',', ' ') }}
                                FCFA
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="5"
                                class="text-center text-gray-500 py-10"
                            >
                                Aucune commission disponible.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</x-layouts.admin>