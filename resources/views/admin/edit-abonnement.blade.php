<x-layouts.admin :header="'Modifier l\'abonnement'">

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
                <i class="fa-solid fa-pen"></i>
            </span>

            Modifier l'abonnement

        </h1>

        <p class="mt-2 text-sm text-slate-500">
            Modifiez les informations de cet abonnement.
        </p>

    </div>


    {{-- Formulaire --}}

    <div class="tk-card p-6">


        {{-- Formulaire --}}

        <form
            action="{{ route('admin.abonnements.update', $abonnement->id) }}"
            method="POST"
        >

            @csrf

            @method('PUT')


            {{-- Agence --}}

            <div class="mb-6">

                <label
                    for="agence_id"
                    class="tk-form-label"
                >
                    <i class="fa-solid fa-building mr-1.5"></i>
                    Agence
                </label>

                <select
                    id="agence_id"
                    name="agence_id"
                    required
                    class="tk-input">

                    <option value="">
                        -- Sélectionner une agence --
                    </option>

                    @foreach($agences as $agence)

                        <option
                            value="{{ $agence->id }}"
                            {{ old('agence_id', $abonnement->agence_id) == $agence->id ? 'selected' : '' }}>

                            {{ $agence->nom_agence }}

                        </option>

                    @endforeach

                </select>

            </div>


            {{-- Type --}}

            <div class="mb-6">

                <label
                    for="type"
                    class="tk-form-label"
                >
                    <i class="fa-solid fa-box mr-1.5"></i>
                    Type d'abonnement
                </label>

                <input
                    type="text"
                    id="type"
                    name="type"
                    value="{{ old('type', $abonnement->type) }}"
                    required
                    maxlength="100"
                    placeholder="Exemple : Mensuel, Trimestriel..."
                    class="tk-input">

            </div>


            {{-- Montant --}}

            <div class="mb-6">

                <label
                    for="montant"
                    class="tk-form-label"
                >
                    <i class="fa-solid fa-money-bill-wave mr-1.5"></i>
                    Montant
                </label>

                <div class="relative">

                    <input
                        type="number"
                        id="montant"
                        name="montant"
                        value="{{ old('montant', $abonnement->montant) }}"
                        required
                        min="0"
                        step="0.01"
                        placeholder="Exemple : 50000"
                        class="tk-input pr-20">

                    <span class="absolute right-4 top-1/2 -translate-y-1/2 font-semibold text-slate-500">
                        FCFA
                    </span>

                </div>

            </div>


            {{-- Dates --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                {{-- Date début --}}

                <div>

                    <label
                        for="date_debut"
                        class="tk-form-label"
                    >
                        <i class="fa-solid fa-calendar mr-1.5"></i>
                        Date de début
                    </label>

                    <input
                        type="date"
                        id="date_debut"
                        name="date_debut"
                        value="{{ old('date_debut', \Carbon\Carbon::parse($abonnement->date_debut)->format('Y-m-d')) }}"
                        required
                        class="tk-input">

                </div>


                {{-- Date fin --}}

                <div>

                    <label
                        for="date_fin"
                        class="tk-form-label"
                    >
                        <i class="fa-solid fa-calendar mr-1.5"></i>
                        Date de fin
                    </label>

                    <input
                        type="date"
                        id="date_fin"
                        name="date_fin"
                        value="{{ old('date_fin', \Carbon\Carbon::parse($abonnement->date_fin)->format('Y-m-d')) }}"
                        required
                        class="tk-input">

                </div>

            </div>


            {{-- Statut --}}

            <div class="mb-8">

                <label
                    for="statut"
                    class="tk-form-label"
                >
                    <i class="fa-solid fa-toggle-on mr-1.5"></i>
                    Statut
                </label>

                <select
                    id="statut"
                    name="statut"
                    required
                    class="tk-input">

                    <option
                        value="Actif"
                        {{ old('statut', $abonnement->statut) == 'Actif' ? 'selected' : '' }}>

                        Actif

                    </option>

                    <option
                        value="Expiré"
                        {{ old('statut', $abonnement->statut) == 'Expiré' ? 'selected' : '' }}>

                        Expiré

                    </option>

                </select>

            </div>


            {{-- Boutons --}}

            <div class="flex flex-col sm:flex-row gap-4">

                {{-- Enregistrer --}}

                <button
                    type="submit"
                    class="flex-1 tk-btn-accent"
                >

                    <i class="fa-solid fa-check"></i>

                    Enregistrer les modifications

                </button>


                {{-- Annuler --}}

                <a
                    href="{{ route('admin.abonnements') }}"
                    class="flex-1 tk-btn-ghost text-center"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Annuler

                </a>

            </div>

        </form>

    </div>

</div>

</x-layouts.admin>
