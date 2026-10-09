<x-layouts.admin :header="'Ajouter un utilisateur'">

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
                <i class="fa-solid fa-user-plus"></i>
            </span>

            Ajouter un utilisateur

        </h1>

    </div>


    {{-- Formulaire --}}

    <div class="tk-card p-6">

        <form action="{{ route('voyageurs.store') }}" method="POST">

            @csrf


            {{-- Nom complet --}}

            <div class="mb-5">

                <label class="tk-form-label">

                    <i class="fa-solid fa-user"></i>

                    Nom complet

                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="tk-input"
                    placeholder="Entrez le nom complet"
                    required>

            </div>


            {{-- Email --}}

            <div class="mb-5">

                <label class="tk-form-label">

                    <i class="fa-solid fa-envelope"></i>

                    Adresse email

                </label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="tk-input"
                    placeholder="exemple@email.com"
                    required>

            </div>


            {{-- Mot de passe --}}

            <div class="mb-5">

                <label class="tk-form-label">

                    <i class="fa-solid fa-lock"></i>

                    Mot de passe

                </label>

                <div class="relative">

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="tk-input pr-12"
                        placeholder="********"
                        required>

                    <button
                        type="button"
                        onclick="togglePassword('password', 'eyePassword')"
                        class="
                            absolute right-4 top-1/2
                            -translate-y-1/2
                            text-slate-400 hover:text-brand
                        ">

                        <span id="eyePassword">

                            <i class="fa-solid fa-eye"></i>

                        </span>

                    </button>

                </div>

            </div>


            {{-- Confirmation du mot de passe --}}

            <div class="mb-5">

                <label class="tk-form-label">

                    <i class="fa-solid fa-lock"></i>

                    Confirmer le mot de passe

                </label>

                <div class="relative">

                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="tk-input pr-12"
                        placeholder="********"
                        required>

                    <button
                        type="button"
                        onclick="togglePassword('password_confirmation', 'eyeConfirmation')"
                        class="
                            absolute right-4 top-1/2
                            -translate-y-1/2
                            text-slate-400 hover:text-brand
                        ">

                        <span id="eyeConfirmation">

                            <i class="fa-solid fa-eye"></i>

                        </span>

                    </button>

                </div>

            </div>


            {{-- Rôle --}}

            <div class="mb-5">

                <label class="tk-form-label">

                    <i class="fa-solid fa-id-card"></i>

                    Rôle

                </label>

                <select
                    name="role"
                    id="role"
                    class="tk-input"
                    required>

                    <option value="">
                        Sélectionner un rôle
                    </option>

                    <option
                        value="user"
                        {{ old('role') == 'user' ? 'selected' : '' }}>
                        Utilisateur
                    </option>

                    <option
                        value="agent"
                        {{ old('role') == 'agent' ? 'selected' : '' }}>
                        Agent
                    </option>

                    <option
                        value="admin"
                        {{ old('role') == 'admin' ? 'selected' : '' }}>
                        Administrateur
                    </option>

                </select>

            </div>


            {{-- Agence --}}

            <div
                id="agenceContainer"
                class="mb-8 {{ old('role') == 'agent' ? '' : 'hidden' }}">

                <label class="tk-form-label">

                    <i class="fa-solid fa-building"></i>

                    Agence

                </label>

                <select
                    name="agence_id"
                    id="agence_id"
                    class="tk-input">

                    <option value="">
                        Sélectionner une agence
                    </option>

                    @foreach($agences as $agence)

                        <option
                            value="{{ $agence->id }}"
                            {{ old('agence_id') == $agence->id ? 'selected' : '' }}>

                            {{ $agence->nom_agence }}

                        </option>

                    @endforeach

                </select>

            </div>


            {{-- Boutons --}}

            <div class="flex justify-between gap-4">

                <a
                    href="{{ url('/admin/voyageurs') }}"
                    class="tk-btn-ghost"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Retour

                </a>

                <button
                    type="submit"
                    class="tk-btn-accent"
                >

                    <i class="fa-solid fa-floppy-disk"></i>

                    Enregistrer

                </button>

            </div>

        </form>

    </div>

</div>


{{-- JavaScript --}}

<script>

function togglePassword(inputId, eyeId) {

    const input = document.getElementById(inputId);
    const eye = document.getElementById(eyeId);

    if (!input || !eye) {
        return;
    }

    if (input.type === "password") {

        input.type = "text";
        eye.textContent = "🙈";

    } else {

        input.type = "password";
        eye.textContent = "👁️";

    }

}


document.addEventListener('DOMContentLoaded', function () {

    const role = document.getElementById('role');
    const agenceContainer = document.getElementById('agenceContainer');
    const agence = document.getElementById('agence_id');

    function afficherAgence() {

        if (role.value === 'agent') {

            agenceContainer.classList.remove('hidden');

            agence.required = true;

        } else {

            agenceContainer.classList.add('hidden');

            agence.required = false;

            agence.value = '';

        }

    }

    role.addEventListener('change', afficherAgence);

    afficherAgence();

});

</script>

</x-layouts.admin>
