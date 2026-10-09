@php
    $layout = auth()->user()->role === 'agent'
        ? 'layouts.agent'
        : 'layouts.admin';
@endphp

<x-dynamic-component
    :component="$layout"
    :header="'Nouveau trajet'"
>

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
                <i class="fa-solid fa-bus"></i>
            </span>

            Nouveau trajet

        </h1>

    </div>


    {{-- Formulaire --}}

    <div class="tk-card p-6">


        {{-- Message agent --}}

        @if(auth()->user()->role === 'agent')

            <div
                class="
                    tk-alert mb-6
                    border-navy/20 bg-[#EEF4FF]
                    text-navy
                "
            >

                <p class="flex items-center gap-2 font-semibold">

                    <i class="fa-solid fa-user-tie"></i>

                    <strong>
                        Vous êtes connecté en tant qu'agent.
                    </strong>

                </p>

                <p class="mt-1">

                    Ce trajet sera automatiquement rattaché à votre agence :

                    <strong>
                        {{ auth()->user()->agence->nom_agence ?? 'Aucune agence' }}
                    </strong>

                </p>

            </div>

        @endif


        <form
            action="{{ url('/admin/trajets/store') }}"
            method="POST"
        >

            @csrf


            {{-- AGENCE --}}

            <div class="mb-5">

                <label class="tk-form-label">
                    Agence
                </label>


                @if(auth()->user()->role === 'agent')

                    {{-- AGENT : agence imposée --}}

                    <div class="tk-input flex items-center gap-2 text-slate-600">

                        <i class="fa-solid fa-building"></i>

                        {{ auth()->user()->agence->nom_agence ?? 'Aucune agence' }}

                    </div>

                    <input
                        type="hidden"
                        name="agence_id"
                        value="{{ auth()->user()->agence_id }}"
                    >

                @else

                    {{-- ADMIN : choix de l'agence --}}

                    <select
                        name="agence_id"
                        required
                        class="tk-input"
                    >

                        <option value="">
                            Sélectionner une agence
                        </option>

                        @foreach($agences as $agence)

                            <option
                                value="{{ $agence->id }}"
                                {{ old('agence_id') == $agence->id ? 'selected' : '' }}
                            >
                                {{ $agence->nom_agence }}
                            </option>

                        @endforeach

                    </select>

                @endif

            </div>


            {{-- DÉPART --}}

            <div class="mb-5">

                <label class="tk-form-label">
                    Ville de départ
                </label>

                <input
                    type="text"
                    name="depart"
                    value="{{ old('depart') }}"
                    placeholder="Exemple : Brazzaville"
                    required
                    class="tk-input"
                >

            </div>


            {{-- ARRIVÉE --}}

            <div class="mb-5">

                <label class="tk-form-label">
                    Ville d'arrivée
                </label>

                <input
                    type="text"
                    name="arrivee"
                    value="{{ old('arrivee') }}"
                    placeholder="Exemple : Pointe-Noire"
                    required
                    class="tk-input"
                >

            </div>


            {{-- DATE --}}

            <div class="mb-5">

                <label class="tk-form-label">
                    Date de départ
                </label>

                <input
                    type="date"
                    name="date_depart"
                    value="{{ old('date_depart') }}"
                    required
                    class="tk-input"
                >

            </div>


            {{-- HEURE --}}

            <div class="mb-5">

                <label class="tk-form-label">
                    Heure de départ
                </label>

                <input
                    type="time"
                    name="heure_depart"
                    value="{{ old('heure_depart') }}"
                    required
                    class="tk-input"
                >

            </div>


            {{-- PRIX --}}

            <div class="mb-5">

                <label class="tk-form-label">
                    Prix (FCFA)
                </label>

                <input
                    type="number"
                    name="prix"
                    value="{{ old('prix') }}"
                    min="0"
                    step="1"
                    placeholder="Exemple : 10000"
                    required
                    class="tk-input"
                >

            </div>


            {{-- PLACES --}}

            <div class="mb-6">

                <label class="tk-form-label">
                    Nombre de places
                </label>

                <input
                    type="number"
                    name="places_totales"
                    value="{{ old('places_totales') }}"
                    min="1"
                    placeholder="Exemple : 20"
                    required
                    class="tk-input"
                >

            </div>


            {{-- BOUTONS --}}

            <div class="flex flex-wrap gap-3 mt-6">

                <button
                    type="submit"
                    class="tk-btn-accent"
                >
                    <i class="fa-solid fa-check"></i>

                    ENREGISTRER LE TRAJET
                </button>


                <a
                    href="{{ route('admin.trajets') }}"
                    class="tk-btn-ghost"
                >
                    <i class="fa-solid fa-arrow-left"></i>

                    RETOUR
                </a>

            </div>

        </form>

    </div>

</div>

</x-dynamic-component>
