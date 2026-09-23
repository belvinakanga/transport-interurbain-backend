@php
    $user = auth()->user();
    $isAgent = $user->role === 'agent';

    $totalPaye = $paiements
        ->where('statut', 'payé')
        ->sum('montant');

    $totalEnAttente = $paiements
        ->where('statut', 'en attente')
        ->sum('montant');

    $prochainPaiement = $paiements
        ->whereIn('statut', ['en attente', 'en retard'])
        ->sortBy('date_prevue')
        ->first();
@endphp


@if($isAgent)

    <x-layouts.agent
        :header="'Mes paiements'"
    >

        <div class="space-y-6">

            {{-- TITRE --}}

            <div>

                <h1
                    class="text-3xl font-bold"
                    style="color:#0A2A66;"
                >
                    💰 Mes paiements
                </h1>

                <p class="mt-2 text-gray-500">
                    Consultez les règlements de votre agence.
                </p>

            </div>


            {{-- AGENCE --}}

            <div
                class="rounded-2xl p-5"
                style="
                    background:#0A2A66;
                    color:white;
                "
            >

                <p class="text-sm text-blue-100">
                    Mon agence
                </p>

                <p class="text-2xl font-bold mt-1">
                    {{ $user->agence->nom_agence ?? 'Aucune agence' }}
                </p>

            </div>


            {{-- STATISTIQUES --}}

            <div
                class="grid grid-cols-1 md:grid-cols-3 gap-5"
            >

                {{-- TOTAL PAYÉ --}}

                <div
                    class="bg-white rounded-2xl p-6 shadow-sm border"
                >

                    <p class="text-sm text-gray-500">
                        Total payé
                    </p>

                    <p
                        class="text-3xl font-bold mt-2"
                        style="color:#16A34A;"
                    >
                        {{ number_format($totalPaye, 0, ',', ' ') }}
                        FCFA
                    </p>

                    <div class="mt-3 text-2xl">
                        ✅
                    </div>

                </div>


                {{-- EN ATTENTE --}}

                <div
                    class="bg-white rounded-2xl p-6 shadow-sm border"
                >

                    <p class="text-sm text-gray-500">
                        En attente
                    </p>

                    <p
                        class="text-3xl font-bold mt-2"
                        style="color:#FF6B00;"
                    >
                        {{ number_format($totalEnAttente, 0, ',', ' ') }}
                        FCFA
                    </p>

                    <div class="mt-3 text-2xl">
                        ⏳
                    </div>

                </div>


                {{-- PROCHAIN --}}

                <div
                    class="bg-white rounded-2xl p-6 shadow-sm border"
                >

                    <p class="text-sm text-gray-500">
                        Prochain paiement
                    </p>

                    @if($prochainPaiement)

                        <p
                            class="text-xl font-bold mt-2"
                            style="color:#0A2A66;"
                        >
                            {{ $prochainPaiement->date_prevue->format('d/m/Y') }}
                        </p>

                        <p class="text-sm text-gray-500 mt-1">

                            {{ number_format(
                                $prochainPaiement->montant,
                                0,
                                ',',
                                ' '
                            ) }}

                            FCFA

                        </p>

                    @else

                        <p
                            class="text-xl font-bold mt-2"
                            style="color:#0A2A66;"
                        >
                            Aucun
                        </p>

                    @endif

                    <div class="mt-3 text-2xl">
                        📅
                    </div>

                </div>

            </div>


            {{-- HISTORIQUE --}}

            <div
                class="
                    bg-white
                    rounded-2xl
                    shadow-sm
                    border
                    border-gray-100
                    overflow-hidden
                "
            >

                <div
                    class="
                        px-6
                        py-5
                        border-b
                        flex
                        items-center
                        justify-between
                    "
                    style="background:#F6F8FC;"
                >

                    <div>

                        <h2
                            class="text-xl font-bold"
                            style="color:#0A2A66;"
                        >
                            📋 Historique des paiements
                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Tous les règlements de votre agence.
                        </p>

                    </div>

                </div>


                <div class="overflow-x-auto">

                    <table class="min-w-full">

                        <thead
                            style="background:#EEF4FF;"
                        >

                            <tr>

                                <th class="p-4 text-left">
                                    Référence
                                </th>

                                <th class="p-4 text-center">
                                    Date prévue
                                </th>

                                <th class="p-4 text-center">
                                    Date paiement
                                </th>

                                <th class="p-4 text-right">
                                    Montant
                                </th>

                                <th class="p-4 text-center">
                                    Statut
                                </th>

                                <th class="p-4 text-left">
                                    Note
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($paiements as $paiement)

                                @php

                                    $statut = $paiement->statut;

                                    if (
                                        $statut === 'en attente'
                                        &&
                                        $paiement->date_prevue
                                        &&
                                        $paiement->date_prevue->isPast()
                                    ) {
                                        $statut = 'en retard';
                                    }

                                @endphp

                                <tr
                                    class="
                                        border-t
                                        border-gray-100
                                        hover:bg-gray-50
                                    "
                                >

                                    {{-- RÉFÉRENCE --}}

                                    <td class="p-4">

                                        <span
                                            class="
                                                font-mono
                                                text-sm
                                                font-semibold
                                            "
                                            style="color:#0A2A66;"
                                        >
                                            {{ $paiement->reference ?? '-' }}
                                        </span>

                                    </td>


                                    {{-- DATE PRÉVUE --}}

                                    <td class="p-4 text-center">

                                        {{ $paiement->date_prevue
                                            ? $paiement->date_prevue->format('d/m/Y')
                                            : '-'
                                        }}

                                    </td>


                                    {{-- DATE PAIEMENT --}}

                                    <td class="p-4 text-center">

                                        {{ $paiement->date_paiement
                                            ? $paiement->date_paiement->format('d/m/Y')
                                            : '-'
                                        }}

                                    </td>


                                    {{-- MONTANT --}}

                                    <td
                                        class="
                                            p-4
                                            text-right
                                            font-bold
                                        "
                                        style="color:#FF6B00;"
                                    >

                                        {{ number_format(
                                            $paiement->montant,
                                            0,
                                            ',',
                                            ' '
                                        ) }}

                                        FCFA

                                    </td>


                                    {{-- STATUT --}}

                                    <td class="p-4 text-center">

                                        @if($statut === 'payé')

                                            <span
                                                class="
                                                    px-3
                                                    py-1
                                                    rounded-full
                                                    text-xs
                                                    font-bold
                                                "
                                                style="
                                                    background:#DCFCE7;
                                                    color:#15803D;
                                                "
                                            >
                                                ✅ Payé
                                            </span>

                                        @elseif($statut === 'en retard')

                                            <span
                                                class="
                                                    px-3
                                                    py-1
                                                    rounded-full
                                                    text-xs
                                                    font-bold
                                                "
                                                style="
                                                    background:#FEE2E2;
                                                    color:#DC2626;
                                                "
                                            >
                                                🔴 En retard
                                            </span>

                                        @elseif($statut === 'annulé')

                                            <span
                                                class="
                                                    px-3
                                                    py-1
                                                    rounded-full
                                                    text-xs
                                                    font-bold
                                                "
                                                style="
                                                    background:#F3F4F6;
                                                    color:#6B7280;
                                                "
                                            >
                                                Annulé
                                            </span>

                                        @else

                                            <span
                                                class="
                                                    px-3
                                                    py-1
                                                    rounded-full
                                                    text-xs
                                                    font-bold
                                                "
                                                style="
                                                    background:#FFF3E8;
                                                    color:#FF6B00;
                                                "
                                            >
                                                ⏳ En attente
                                            </span>

                                        @endif

                                    </td>


                                    {{-- NOTE --}}

                                    <td class="p-4 text-sm text-gray-500">

                                        {{ $paiement->note ?? '-' }}

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="6"
                                        class="
                                            text-center
                                            py-12
                                            text-gray-500
                                        "
                                    >

                                        <div class="text-4xl mb-3">
                                            💰
                                        </div>

                                        Aucun paiement enregistré
                                        pour votre agence.

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- PAGINATION --}}

                @if($paiements->hasPages())

                    <div class="p-5 border-t">

                        {{ $paiements->links() }}

                    </div>

                @endif

            </div>

        </div>

    </x-layouts.agent>

@else

    {{-- ADMIN --}}

    <x-layouts.admin
        :header="'Règlements des agences'"
    >

        <div class="space-y-6">

            <div>

                <h1
                    class="text-3xl font-bold"
                    style="color:#0A2A66;"
                >
                    💰 Règlements des agences
                </h1>

                <p class="text-gray-500 mt-2">
                    Gérez les règlements destinés aux agences.
                </p>

            </div>


            <div class="flex justify-end">

                <a
                    href="{{ route('paiements-agences.create') }}"
                    class="
                        px-5
                        py-3
                        rounded-xl
                        text-white
                        font-bold
                        shadow-md
                    "
                    style="background:#FF6B00;"
                >
                    ➕ Nouveau règlement
                </a>

            </div>


            {{-- FILTRES --}}

            <div
                class="bg-white rounded-2xl shadow-sm border p-6"
            >

                <form
                    method="GET"
                    action="{{ route('paiements-agences.index') }}"
                    class="
                        grid
                        grid-cols-1
                        md:grid-cols-4
                        gap-4
                    "
                >

                    <select
                        name="agence"
                        class="
                            border
                            rounded-xl
                            px-4
                            py-3
                        "
                    >

                        <option value="">
                            Toutes les agences
                        </option>

                        @foreach($listeAgences as $agence)

                            <option
                                value="{{ $agence->id }}"
                                {{ request('agence') == $agence->id ? 'selected' : '' }}
                            >
                                {{ $agence->nom_agence }}
                            </option>

                        @endforeach

                    </select>


                    <select
                        name="statut"
                        class="
                            border
                            rounded-xl
                            px-4
                            py-3
                        "
                    >

                        <option value="">
                            Tous les statuts
                        </option>

                        <option
                            value="en attente"
                            {{ request('statut') === 'en attente' ? 'selected' : '' }}
                        >
                            En attente
                        </option>

                        <option
                            value="payé"
                            {{ request('statut') === 'payé' ? 'selected' : '' }}
                        >
                            Payé
                        </option>

                        <option
                            value="en retard"
                            {{ request('statut') === 'en retard' ? 'selected' : '' }}
                        >
                            En retard
                        </option>

                        <option
                            value="annulé"
                            {{ request('statut') === 'annulé' ? 'selected' : '' }}
                        >
                            Annulé
                        </option>

                    </select>


                    <input
                        type="date"
                        name="date"
                        value="{{ request('date') }}"
                        class="
                            border
                            rounded-xl
                            px-4
                            py-3
                        "
                    >


                    <button
                        type="submit"
                        class="
                            rounded-xl
                            text-white
                            font-bold
                        "
                        style="background:#0A2A66;"
                    >
                        Filtrer
                    </button>

                </form>

            </div>


            {{-- TABLEAU ADMIN --}}

            <div
                class="
                    bg-white
                    rounded-2xl
                    shadow-sm
                    border
                    overflow-hidden
                "
            >

                <div class="overflow-x-auto">

                    <table class="min-w-full">

                        <thead style="background:#EEF4FF;">

                            <tr>

                                <th class="p-4 text-left">
                                    Agence
                                </th>

                                <th class="p-4 text-left">
                                    Référence
                                </th>

                                <th class="p-4 text-center">
                                    Date prévue
                                </th>

                                <th class="p-4 text-right">
                                    Montant
                                </th>

                                <th class="p-4 text-center">
                                    Statut
                                </th>

                                <th class="p-4 text-center">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($paiements as $paiement)

                                @php

                                    $statut = $paiement->statut;

                                    if (
                                        $statut === 'en attente'
                                        &&
                                        $paiement->date_prevue
                                        &&
                                        $paiement->date_prevue->isPast()
                                    ) {
                                        $statut = 'en retard';
                                    }

                                @endphp

                                <tr class="border-t border-gray-100">

                                    <td class="p-4 font-semibold">

                                        {{ $paiement->agence->nom_agence ?? '-' }}

                                    </td>

                                    <td class="p-4 font-mono text-sm">

                                        {{ $paiement->reference ?? '-' }}

                                    </td>

                                    <td class="p-4 text-center">

                                        {{ $paiement->date_prevue
                                            ? $paiement->date_prevue->format('d/m/Y')
                                            : '-'
                                        }}

                                    </td>

                                    <td
                                        class="
                                            p-4
                                            text-right
                                            font-bold
                                        "
                                        style="color:#FF6B00;"
                                    >

                                        {{ number_format(
                                            $paiement->montant,
                                            0,
                                            ',',
                                            ' '
                                        ) }}

                                        FCFA

                                    </td>

                                    <td class="p-4 text-center">

                                        @if($statut === 'payé')

                                            <span
                                                class="
                                                    px-3
                                                    py-1
                                                    rounded-full
                                                    bg-green-100
                                                    text-green-700
                                                    text-xs
                                                    font-bold
                                                "
                                            >
                                                ✅ Payé
                                            </span>

                                        @elseif($statut === 'en retard')

                                            <span
                                                class="
                                                    px-3
                                                    py-1
                                                    rounded-full
                                                    bg-red-100
                                                    text-red-700
                                                    text-xs
                                                    font-bold
                                                "
                                            >
                                                🔴 En retard
                                            </span>

                                        @else

                                            <span
                                                class="
                                                    px-3
                                                    py-1
                                                    rounded-full
                                                    bg-orange-100
                                                    text-orange-700
                                                    text-xs
                                                    font-bold
                                                "
                                            >
                                                ⏳ En attente
                                            </span>

                                        @endif

                                    </td>

                                    <td class="p-4 text-center">

                                        @if($paiement->statut !== 'payé')

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'paiements-agences.paid',
                                                    $paiement->id
                                                ) }}"
                                                style="display:inline;"
                                            >

                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="
                                                        px-3
                                                        py-2
                                                        rounded-lg
                                                        text-white
                                                        font-semibold
                                                    "
                                                    style="background:#16A34A;"
                                                >
                                                    ✓ Marquer payé
                                                </button>

                                            </form>

                                        @else

                                            <span class="text-gray-400">
                                                Terminé
                                            </span>

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="6"
                                        class="
                                            text-center
                                            py-10
                                            text-gray-500
                                        "
                                    >
                                        Aucun règlement enregistré.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                @if($paiements->hasPages())

                    <div class="p-5 border-t">

                        {{ $paiements->links() }}

                    </div>

                @endif

            </div>

        </div>

    </x-layouts.admin>

@endif