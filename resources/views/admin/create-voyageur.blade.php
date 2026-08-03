<x-layouts.admin>

<div class="py-12">

    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white rounded-2xl shadow-lg p-8">

            <h1 class="text-3xl font-bold text-slate-800 mb-8">

                ➕ Ajouter un utilisateur

            </h1>

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

                <!-- Nom -->

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

                <!-- Email -->

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

                <!-- Mot de passe -->

                <div class="mb-5">

                    <label class="block mb-2 font-semibold text-slate-700">

                        🔒 Mot de passe

                    </label>

                    <input
                        type="password"
                        name="password"
                        class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500"
                        placeholder="********"
                        required>

                </div>

                <!-- Confirmation -->

                <div class="mb-5">

                    <label class="block mb-2 font-semibold text-slate-700">

                        🔒 Confirmer le mot de passe

                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500"
                        placeholder="********"
                        required>

                </div>

                <!-- Rôle -->

                <div class="mb-8">

                    <label class="block mb-2 font-semibold text-slate-700">

                        🎭 Rôle

                    </label>

                    <select
                        name="role"
                        class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500"
                        required>

                        <option value="">Sélectionner un rôle</option>

                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>

                            👤 Utilisateur

                        </option>

                        <option value="agence" {{ old('role') == 'agence' ? 'selected' : '' }}>

                            🏢 Agence

                        </option>

                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>

                            👑 Administrateur

                        </option>

                    </select>

                </div>

                <!-- Boutons -->

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

</x-layouts.admin>