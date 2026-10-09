<x-layouts.admin :header="'Détails du billet'">

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
                <i class="fa-solid fa-ticket"></i>
            </span>

            Détails du billet

        </h1>

    </div>


    {{-- Détails du billet --}}

    <div class="tk-card p-6">

        <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

            <span class="font-semibold text-navy">
                N° Billet :
            </span>

            <span class="text-slate-600 text-right">
                {{ $billet->numero_billet }}
            </span>

        </div>


        <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

            <span class="font-semibold text-navy">
                QR Code :
            </span>

            <span class="text-slate-600 text-right">
                {{ $billet->qr_code }}
            </span>

        </div>


        <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

            <span class="font-semibold text-navy">
                Voyageur :
            </span>

            <span class="text-slate-600 text-right">
                {{ $billet->reservation?->user?->name ?? 'Utilisateur supprimé' }}
            </span>

        </div>


        <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

            <span class="font-semibold text-navy">
                Agence :
            </span>

            <span class="text-slate-600 text-right">
                {{ $billet->reservation?->trajet?->agence?->nom_agence ?? '-' }}
            </span>

        </div>


        <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

            <span class="font-semibold text-navy">
                Départ :
            </span>

            <span class="text-slate-600 text-right">
                {{ $billet->reservation?->trajet?->depart ?? '-' }}
            </span>

        </div>


        <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

            <span class="font-semibold text-navy">
                Arrivée :
            </span>

            <span class="text-slate-600 text-right">
                {{ $billet->reservation?->trajet?->arrivee ?? '-' }}
            </span>

        </div>


        <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

            <span class="font-semibold text-navy">
                Date :
            </span>

            <span class="text-slate-600 text-right">
                {{ $billet->reservation?->trajet?->date_depart ?? '-' }}
            </span>

        </div>


        <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

            <span class="font-semibold text-navy">
                Heure :
            </span>

            <span class="text-slate-600 text-right">
                {{ $billet->reservation?->trajet?->heure_depart ?? '-' }}
            </span>

        </div>


        <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

            <span class="font-semibold text-navy">
                Prix :
            </span>

            <span class="text-slate-600 text-right">
                {{ $billet->reservation?->trajet?->prix ?? '-' }} FCFA
            </span>

        </div>


        <div class="flex items-center justify-between gap-4 border-b border-slate-100 py-3 last:border-b-0 text-sm">

            <span class="font-semibold text-navy">
                Statut :
            </span>

            <span class="text-slate-600 text-right">
                {{ $billet->reservation?->statut ?? 'Réservation supprimée' }}
            </span>

        </div>

    </div>


    {{-- Actions --}}

    <div class="flex gap-4">

        <a
            href="{{ url('/admin/billets') }}"
            class="tk-btn-ghost"
        >

            <i class="fa-solid fa-arrow-left"></i>

            Retour

        </a>

        <button
            onclick="window.print()"
            class="tk-btn bg-emerald-600 text-white hover:bg-emerald-700"
        >

            <i class="fa-solid fa-print"></i>

            Imprimer

        </button>

    </div>

</div>

</x-layouts.admin>
