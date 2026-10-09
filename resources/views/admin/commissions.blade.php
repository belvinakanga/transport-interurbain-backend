<x-layouts.admin :header="'Commissions Tokende'">

<div class="tk-page">

    {{-- En-tête --}}

    <div class="tk-page-head">

        <h1 class="tk-page-title">

            <span
                class="
                    flex h-11 w-11 shrink-0
                    items-center justify-center
                    rounded-lg bg-orange-50
                    text-lg text-brand
                "
            >
                <i class="fa-solid fa-money-bill-wave"></i>
            </span>

            Commissions Tokende

        </h1>

    </div>


    {{-- Tableau --}}

    <div class="tk-card overflow-hidden">

        <div class="overflow-x-auto">

            <table class="tk-table">

                <thead>

                    <tr>

                        <th class="text-left whitespace-nowrap">
                            Agence
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Billets vendus
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Frais générés
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Part Tokende
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Part agence
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($commissions as $commission)

                        <tr>

                            {{-- AGENCE --}}

                            <td class="font-medium whitespace-nowrap">

                                {{ $commission['agence']->nom_agence }}

                            </td>


                            {{-- BILLETS VENDUS --}}

                            <td class="whitespace-nowrap tabular-nums">

                                {{ $commission['nombre_billets'] }}

                            </td>


                            {{-- FRAIS GÉNÉRÉS --}}

                            <td class="whitespace-nowrap tabular-nums">

                                {{ number_format($commission['frais_generes'], 0, ',', ' ') }}
                                FCFA

                            </td>


                            {{-- PART TOKENDE --}}

                            <td class="whitespace-nowrap tabular-nums font-bold text-emerald-600">

                                {{ number_format($commission['part_tokende'], 0, ',', ' ') }}
                                FCFA

                            </td>


                            {{-- PART AGENCY --}}

                            <td class="whitespace-nowrap tabular-nums">

                                {{ number_format($commission['part_agence'], 0, ',', ' ') }}
                                FCFA

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="tk-empty"
                            >
                                Aucune commission disponible.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

</x-layouts.admin>
