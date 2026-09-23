<x-layouts.admin>

<div class="py-12">

    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white rounded-2xl shadow-lg p-8">

            <h1 class="text-3xl font-bold text-slate-800 mb-8">
                ➕ Ajouter un utilisateur
            </h1>

            {{-- Messages d'erreur --}}

            @if ($errors->any())

                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6">

                    <ul class="list-disc pl-5">

                        @foreach ($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif


            <form action="{{ route('voyageurs.store') }}" method="POST">

                @csrf


                {{-- Nom complet --}}

                <div class="mb-5">

                    <label class="block mb-2 font-semibold text-slate-700">
                        👤 Nom complet
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500"
                        placeholder="Entrez le nom complet"
                        required>

                </div>


                {{-- Email --}}

                <div class="mb-5">

                    <label class="block mb-2 font-semibold text-slate-700">
                        📧 Adresse email
                    </label>

                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500"
                        placeholder="exemple@email.com"
                        required>

                </div>


                {{-- Mot de passe --}}

                <div class="mb-5">

                    <label class="block mb-2 font-semibold text-slate-700">
                        🔒 Mot de passe
                    </label>

                    <div class="relative">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="w-full border rounded-xl px-4 py-3 pr-12 focus:ring-2 focus:ring-orange-500"
                            placeholder="********"
                            required>

                        <button
                            type="button"
                            onclick="togglePassword('password', 'eyePassword')"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-orange-500">

                            <span id="eyePassword">👁️</span>

                        </button>

                    </div>

                </div>


                {{-- Confirmation du mot de passe --}}

                <div class="mb-5">

                    <label class="block mb-2 font-semibold text-slate-700">
                        🔒 Confirmer le mot de passe
                    </label>

                    <div class="relative">

                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="w-full border rounded-xl px-4 py-3 pr-12 focus:ring-2 focus:ring-orange-500"
                            placeholder="********"
                            required>

                        <button
                            type="button"
                            onclick="togglePassword('password_confirmation', 'eyeConfirmation')"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-orange-500">

                            <span id="eyeConfirmation">👁️</span>

                        </button>

                    </div>

                </div>


                {{-- Rôle --}}

                <div class="mb-5">

                    <label class="block mb-2 font-semibold text-slate-700">
                        🎭 Rôle
                    </label>

                    <select
                        name="role"
                        id="role"
                        class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500"
                        required>

                        <option value="">
                            Sélectionner un rôle
                        </option>

                        <option
                            value="user"
                            {{ old('role') == 'user' ? 'selected' : '' }}>
                            👤 Utilisateur
                        </option>

                        <option
                            value="agent"
                            {{ old('role') == 'agent' ? 'selected' : '' }}>
                            👨‍💼 Agent
                        </option>

                        <option
                            value="admin"
                            {{ old('role') == 'admin' ? 'selected' : '' }}>
                            👑 Administrateur
                        </option>

                    </select>

                </div>


                {{-- Agence --}}

                <div
                    id="agenceContainer"
                    class="mb-8 {{ old('role') == 'agent' ? '' : 'hidden' }}">

                    <label class="block mb-2 font-semibold text-slate-700">
                        🏢 Agence
                    </label>

                    <select
                        name="agence_id"
                        id="agence_id"
                        class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500">

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

                <div class="flex justify-between">

                    <a
                        href="{{ url('/admin/voyageurs') }}"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-xl">

                        ⬅ Retour

                    </a>

                    <button
                        type="submit"
                        class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-xl">

                        💾 Enregistrer

                    </button>

                </div>

            </form>

        </div>

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