<x-layouts.admin :header="'Détails du trajet'">

<div class="tk-page">

    {{-- En-tête --}}

    <div
        class="
            tk-page-head
            flex flex-col gap-4
            md:flex-row md:items-center
            md:justify-between
        "
    >

        <h1 class="tk-page-title">

            <span
                class="
                    flex h-11 w-11 shrink-0
                    items-center justify-center
                    rounded-lg bg-orange-50
                    text-lg text-brand
                "
            >
                <i class="fa-solid fa-eye"></i>
            </span>

            Détails du trajet

        </h1>

        <div class="flex flex-wrap gap-3">

            <a
                href="/admin/trajets/{{ $trajet->id }}/edit"
                class="tk-btn-accent shrink-0"
            >

                <i class="fa-solid fa-pen"></i>

                Modifier

            </a>

            <a
                href="/admin/trajets"
                class="tk-btn-ghost shrink-0"
            >

                <i class="fa-solid fa-arrow-left"></i>

                Retour

            </a>

        </div>

    </div>


    {{-- Informations --}}

    <div class="tk-card p-6">

        <div
            class="
                flex items-center justify-between
                gap-4 border-b border-slate-100
                py-3 last:border-b-0
                text-sm
            "
        >

            <span class="font-semibold text-navy">
                Agence
            </span>

            <span class="text-slate-600 text-right">
                {{ $trajet->agence->nom_agence ?? '-' }}
            </span>

        </div>


        <div
            class="
                flex items-center justify-between
                gap-4 border-b border-slate-100
                py-3 last:border-b-0
                text-sm
            "
        >

            <span class="font-semibold text-navy">
                Départ
            </span>

            <span class="text-slate-600 text-right">
                {{ $trajet->depart }}
            </span>

        </div>


        <div
            class="
                flex items-center justify-between
                gap-4 border-b border-slate-100
                py-3 last:border-b-0
                text-sm
            "
        >

            <span class="font-semibold text-navy">
                Arrivée
            </span>

            <span class="text-slate-600 text-right">
                {{ $trajet->arrivee }}
            </span>

        </div>


        <div
            class="
                flex items-center justify-between
                gap-4 border-b border-slate-100
                py-3 last:border-b-0
                text-sm
            "
        >

            <span class="font-semibold text-navy">
                Date de départ
            </span>

            <span class="text-slate-600 text-right">
                {{ $trajet->date_depart }}
            </span>

        </div>


        <div
            class="
                flex items-center justify-between
                gap-4 border-b border-slate-100
                py-3 last:border-b-0
                text-sm
            "
        >

            <span class="font-semibold text-navy">
                Heure de départ
            </span>

            <span class="text-slate-600 text-right">
                {{ $trajet->heure_depart }}
            </span>

        </div>


        <div
            class="
                flex items-center justify-between
                gap-4 border-b border-slate-100
                py-3 last:border-b-0
                text-sm
            "
        >

            <span class="font-semibold text-navy">
                Prix
            </span>

            <span class="text-slate-600 text-right">
                {{ number_format($trajet->prix,0,',',' ') }} FCFA
            </span>

        </div>


        <div
            class="
                flex items-center justify-between
                gap-4 border-b border-slate-100
                py-3 last:border-b-0
                text-sm
            "
        >

            <span class="font-semibold text-navy">
                Places disponibles
            </span>

            <span class="text-slate-600 text-right">
                {{ $trajet->places_disponibles }}
            </span>

        </div>


        <div
            class="
                flex items-center justify-between
                gap-4 border-b border-slate-100
                py-3 last:border-b-0
                text-sm
            "
        >

            <span class="font-semibold text-navy">
                Places totales
            </span>

            <span class="text-slate-600 text-right">
                {{ $trajet->places_totales }}
            </span>

        </div>

    </div>

</div>

</x-layouts.admin>
