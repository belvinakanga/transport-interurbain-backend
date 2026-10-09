<x-layouts.admin :header="'Détails de l\'agence'">

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

            Détails de l'agence

        </h1>

        <a
            href="/admin/agences"
            class="tk-btn-ghost shrink-0"
        >

            <i class="fa-solid fa-arrow-left"></i>

            <span>Retour</span>

        </a>

    </div>


    {{-- Informations --}}

    <div class="tk-card p-6">

        {{-- AGENTS --}}

        <div class="flex items-start justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

            <span class="font-semibold text-navy">
                Agent(s) de l'agence
            </span>

            <div class="text-slate-600 text-right">

                @forelse($agence->agents as $agent)

                    <p>{{ $agent->name }}</p>

                @empty

                    <p class="text-slate-400">
                        Aucun agent affecté
                    </p>

                @endforelse

            </div>

        </div>


        {{-- NOM --}}

        <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

            <span class="font-semibold text-navy">
                Nom de l'agence
            </span>

            <span class="text-slate-600 text-right">
                {{ $agence->nom_agence }}
            </span>

        </div>


        {{-- VILLE --}}

        <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

            <span class="font-semibold text-navy">
                Ville
            </span>

            <span class="text-slate-600 text-right">
                {{ $agence->ville }}
            </span>

        </div>


        {{-- TÉLÉPHONE --}}

        <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

            <span class="font-semibold text-navy">
                Téléphone
            </span>

            <span class="text-slate-600 text-right">
                {{ $agence->telephone }}
            </span>

        </div>


        {{-- ADRESSE --}}

        <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

            <span class="font-semibold text-navy">
                Adresse
            </span>

            <span class="text-slate-600 text-right">
                {{ $agence->adresse }}
            </span>

        </div>


        {{-- ACTIONS --}}

        <div class="mt-6 flex gap-3">

            <a
                href="/admin/agences/{{ $agence->id }}/edit"
                class="tk-btn-accent"
            >

                <i class="fa-solid fa-pen"></i>

                Modifier

            </a>

            <form action="/admin/agences/{{ $agence->id }}" method="POST">

                @csrf

                @method('DELETE')

                <button
                    onclick="event.preventDefault(); tkConfirm('Supprimer cette agence ?', () => this.form.submit())"
                    class="tk-btn bg-red-600 text-white hover:bg-red-700"
                >

                    <i class="fa-solid fa-trash"></i>

                    Supprimer

                </button>

            </form>

        </div>

    </div>

</div>

</x-layouts.admin>
