<x-layouts.admin :header="'Détails de l\'avis'">

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
                <i class="fa-solid fa-star"></i>
            </span>

            Détails de l'avis

        </h1>

        <a
            href="{{ url('/admin/avis') }}"
            class="tk-btn-ghost shrink-0"
        >

            <i class="fa-solid fa-arrow-left"></i>

            <span>Retour</span>

        </a>

    </div>


    {{-- Informations --}}

    <div class="tk-card p-6">

        {{-- VOYAGEUR --}}

        <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

            <span class="font-semibold text-navy">

                <i class="fa-solid fa-user text-brand"></i>

                Voyageur

            </span>

            <span class="text-slate-600 text-right">

                {{ $avis->user?->name ?? 'Utilisateur supprimé' }}

            </span>

        </div>


        {{-- NOTE --}}

        <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

            <span class="font-semibold text-navy">

                <i class="fa-solid fa-star text-brand"></i>

                Note

            </span>

            <span class="flex items-center justify-end gap-1 text-brand">

                @for($i = 1; $i <= $avis->note; $i++)

                    <i class="fa-solid fa-star"></i>

                @endfor

            </span>

        </div>


        {{-- COMMENTAIRE --}}

        <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

            <span class="font-semibold text-navy">

                <i class="fa-solid fa-comment text-brand"></i>

                Commentaire

            </span>

            <span class="text-slate-600 text-right">

                {{ $avis->commentaire }}

            </span>

        </div>


        {{-- DATE --}}

        <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

            <span class="font-semibold text-navy">

                <i class="fa-solid fa-calendar text-brand"></i>

                Date

            </span>

            <span class="text-slate-600 text-right tabular-nums">

                {{ $avis->created_at->format('d/m/Y') }}

            </span>

        </div>


        {{-- ACTIONS --}}

        <div class="mt-6 flex gap-3">

            <button
                onclick="window.print()"
                class="tk-btn-accent"
            >

                <i class="fa-solid fa-print"></i>

                Imprimer

            </button>

        </div>

    </div>

</div>

</x-layouts.admin>
