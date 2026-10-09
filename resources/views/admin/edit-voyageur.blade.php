<x-layouts.admin :header="'Modifier un utilisateur'">

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

            Modifier un utilisateur

        </h1>

    </div>


    {{-- Formulaire --}}

    <div class="tk-card p-6">

        <form
            method="POST"
            action="{{ route('voyageurs.update', $voyageur->id) }}"
        >

            @csrf

            @method('PUT')


            {{-- Nom --}}

            <div class="mb-5">

                <label
                    for="name"
                    class="tk-form-label"
                >

                    <i class="fa-solid fa-user"></i>

                    Nom

                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $voyageur->name) }}"
                    class="tk-input"
                    required>

            </div>


            {{-- Email --}}

            <div class="mb-5">

                <label
                    for="email"
                    class="tk-form-label"
                >

                    <i class="fa-solid fa-envelope"></i>

                    Adresse email

                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $voyageur->email) }}"
                    class="tk-input"
                    required>

            </div>


            {{-- Rôle --}}

            <div class="mb-5">

                <label
                    for="role"
                    class="tk-form-label"
                >

                    <i class="fa-solid fa-crown"></i>

                    Rôle

                </label>

                <select
                    id="role"
                    name="role"
                    class="tk-input"
                    required>

                    <option
                        value="user"
                        {{ old('role', $voyageur->role) == 'user' ? 'selected' : '' }}>

                        Utilisateur

                    </option>

                    <option
                        value="agent"
                        {{ old('role', $voyageur->role) == 'agent' ? 'selected' : '' }}>

                        Agent

                    </option>

                    <option
                        value="admin"
                        {{ old('role', $voyageur->role) == 'admin' ? 'selected' : '' }}>

                        Administrateur

                    </option>

                </select>

            </div>


            {{-- Agence --}}

            <div class="mb-5">

                <label
                    for="agence_id"
                    class="tk-form-label"
                >

                    <i class="fa-solid fa-building"></i>

                    Agence

                </label>

                <select
                    id="agence_id"
                    name="agence_id"
                    class="tk-input">

                    <option value="">
                        -- Aucune agence --
                    </option>

                    @if(isset($agences))

                        @foreach($agences as $agence)

                            <option
                                value="{{ $agence->id }}"
                                {{ old('agence_id', $voyageur->agence_id) == $agence->id ? 'selected' : '' }}>

                                {{ $agence->nom_agence }}

                            </option>

                        @endforeach

                    @endif

                </select>

            </div>


            {{-- Nouveau mot de passe --}}

            <div class="mb-5">

                <label
                    for="password"
                    class="tk-form-label"
                >

                    <i class="fa-solid fa-key"></i>

                    Nouveau mot de passe

                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    class="tk-input"
                    placeholder="Laisser vide pour conserver l'ancien mot de passe">

                <p class="mt-2 text-xs text-slate-500">

                    <i class="fa-solid fa-circle-info"></i>

                    Si vous ne souhaitez pas modifier le mot de passe,
                    laissez ce champ vide.

                </p>

            </div>


            {{-- Confirmation mot de passe --}}

            <div class="mb-6">

                <label
                    for="password_confirmation"
                    class="tk-form-label"
                >

                    <i class="fa-solid fa-lock"></i>

                    Confirmer le nouveau mot de passe

                </label>

                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="tk-input"
                    placeholder="Confirmer le nouveau mot de passe">

            </div>


            {{-- Boutons --}}

            <div class="flex flex-col sm:flex-row gap-4 mt-6">

                {{-- Enregistrer --}}

                <button
                    type="submit"
                    class="tk-btn-accent"
                >

                    <i class="fa-solid fa-floppy-disk"></i>

                    Enregistrer les modifications

                </button>


                {{-- Annuler --}}

                <a
                    href="{{ route('voyageurs.index') }}"
                    class="tk-btn-ghost"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Retour

                </a>

            </div>

        </form>

    </div>

</div>

</x-layouts.admin>
