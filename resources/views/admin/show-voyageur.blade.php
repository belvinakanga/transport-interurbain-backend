<x-layouts.admin :header="'Profil de l\'utilisateur'">

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
                <i class="fa-solid fa-user"></i>
            </span>

            Profil de l'utilisateur

        </h1>

    </div>


    {{-- Informations --}}

    <div class="tk-card p-6">

        <div
            class="
                flex items-center justify-between
                gap-4 border-b border-slate-100
                py-3 last:border-b-0 text-sm
            "
        >

            <span class="font-semibold text-navy">
                Nom :
            </span>

            <span class="text-slate-600 text-right">
                {{ $voyageur->name }}
            </span>

        </div>


        <div
            class="
                flex items-center justify-between
                gap-4 border-b border-slate-100
                py-3 last:border-b-0 text-sm
            "
        >

            <span class="font-semibold text-navy">
                Email :
            </span>

            <span class="text-slate-600 text-right">
                {{ $voyageur->email }}
            </span>

        </div>


        <div
            class="
                flex items-center justify-between
                gap-4 border-b border-slate-100
                py-3 last:border-b-0 text-sm
            "
        >

            <span class="font-semibold text-navy">
                Rôle :
            </span>

            <span class="text-slate-600 text-right">
                {{ ucfirst($voyageur->role) }}
            </span>

        </div>


        <div
            class="
                flex items-center justify-between
                gap-4 border-b border-slate-100
                py-3 last:border-b-0 text-sm
            "
        >

            <span class="font-semibold text-navy">
                Date d'inscription :
            </span>

            <span class="text-slate-600 text-right">
                {{ $voyageur->created_at->format('d/m/Y H:i') }}
            </span>

        </div>


        {{-- Actions --}}

        <div class="mt-8 flex flex-wrap gap-3">

            <a
                href="/admin/voyageurs"
                class="tk-btn-ghost"
            >

                <i class="fa-solid fa-arrow-left"></i>

                Retour

            </a>

            <button
                onclick="window.print()"
                class="tk-btn-navy"
            >

                <i class="fa-solid fa-print"></i>

                Imprimer

            </button>

        </div>

    </div>

</div>

</x-layouts.admin>
