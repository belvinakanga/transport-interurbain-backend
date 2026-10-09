<x-layouts.admin :header="'Paramètres'">

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
                <i class="fa-solid fa-gear"></i>
            </span>

            Paramètres

        </h1>

    </div>


    {{-- Contenu --}}

    <div class="tk-card p-6">

        <h2 class="text-lg font-bold text-navy">
            Paramètres de l'application
        </h2>

        <p class="mt-3 text-sm text-slate-600">
            Cette page permettra de gérer les paramètres généraux de la plateforme TOKENDE.
        </p>

    </div>

</div>

</x-layouts.admin>
