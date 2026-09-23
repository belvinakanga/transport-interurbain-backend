<x-layouts.admin>

<div class="py-12">

    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white p-8 rounded-xl shadow">

            <!-- Titre -->

            <h1 class="text-3xl font-bold text-slate-800 mb-6">
                ✏️ Modifier un utilisateur
            </h1>

            <!-- Erreurs -->

            @if ($errors->any())

                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 p-4 rounded-xl">

                    <p class="font-semibold mb-2">
                        ⚠️ Veuillez corriger les erreurs suivantes :
                    </p>

                    <ul class="list-disc list-inside">

                        @foreach ($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif

            <!-- Formulaire -->

            <form
                method="POST"
                action="{{ route('voyageurs.update', $voyageur->id) }}">

                @csrf
                @method('PUT')


                <!-- Nom -->

                <div class="mb-5">

                    <label
                        for="name"
                        class="block font-semibold text-slate-700 mb-2">

                        👤 Nom

                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $voyageur->name) }}"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 focus:outline-none"
                        required>

                </div>


                <!-- Email -->

                <div class="mb-5">

                    <label
                        for="email"
                        class="block font-semibold text-slate-700 mb-2">

                        📧 Adresse email

                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $voyageur->email) }}"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 focus:outline-none"
                        required>

                </div>


                <!-- Rôle -->

                <div class="mb-5">

                    <label
                        for="role"
                        class="block font-semibold text-slate-700 mb-2">

                        👑 Rôle

                    </label>

                    <select
                        id="role"
                        name="role"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 focus:outline-none"
                        required>

                        <option
                            value="user"
                            {{ old('role', $voyageur->role) == 'user' ? 'selected' : '' }}>

                            👤 Utilisateur

                        </option>

                        <option
                            value="agent"
                            {{ old('role', $voyageur->role) == 'agent' ? 'selected' : '' }}>

                            👨‍💼 Agent

                        </option>

                        <option
                            value="admin"
                            {{ old('role', $voyageur->role) == 'admin' ? 'selected' : '' }}>

                            👑 Administrateur

                        </option>

                    </select>

                </div>


                <!-- Agence -->

                <div class="mb-5">

                    <label
                        for="agence_id"
                        class="block font-semibold text-slate-700 mb-2">

                        🏢 Agence

                    </label>

                    <select
                        id="agence_id"
                        name="agence_id"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 focus:outline-none">

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


                <!-- Nouveau mot de passe -->

                <div class="mb-5">

                    <label
                        for="password"
                        class="block font-semibold text-slate-700 mb-2">

                        🔑 Nouveau mot de passe

                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 focus:outline-none"
                        placeholder="Laisser vide pour conserver l'ancien mot de passe">

                    <p class="text-sm text-gray-500 mt-2">
                        💡 Si vous ne souhaitez pas modifier le mot de passe,
                        laissez ce champ vide.
                    </p>

                </div>


                <!-- Confirmation mot de passe -->

                <div class="mb-6">

                    <label
                        for="password_confirmation"
                        class="block font-semibold text-slate-700 mb-2">

                        🔐 Confirmer le nouveau mot de passe

                    </label>

                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 focus:outline-none"
                        placeholder="Confirmer le nouveau mot de passe">

                </div>


                <!-- Boutons -->

                <div class="flex flex-col sm:flex-row gap-4 mt-6">

                    <!-- Enregistrer -->

                    <button
                        type="submit"
                        class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-xl font-semibold shadow transition">

                        💾 Enregistrer les modifications

                    </button>


                    <!-- Annuler -->

                    <a
                        href="{{ route('voyageurs.index') }}"
                        class="bg-slate-600 hover:bg-slate-700 text-white px-6 py-3 rounded-xl font-semibold text-center shadow transition">

                        ← Retour

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

</x-layouts.admin>