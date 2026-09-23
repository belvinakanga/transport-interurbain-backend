<x-layouts.admin>

<div class="py-12">

    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        <!-- Carte principale -->

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">

            <!-- En-tête -->

            <div class="px-8 py-6 border-b bg-slate-50">

                <h1 class="text-3xl font-bold text-slate-800">
                    ✏️ Modifier l'abonnement
                </h1>

                <p class="text-gray-500 mt-2">
                    Modifiez les informations de cet abonnement.
                </p>

            </div>


            <!-- Erreurs -->

            @if ($errors->any())

                <div class="mx-8 mt-6 bg-red-100 border border-red-400 text-red-700 px-5 py-4 rounded-xl">

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
                action="{{ route('admin.abonnements.update', $abonnement->id) }}"
                method="POST"
                class="p-8">

                @csrf
                @method('PUT')


                <!-- Agence -->

                <div class="mb-6">

                    <label
                        for="agence_id"
                        class="block text-sm font-semibold text-slate-700 mb-2">

                        🏢 Agence

                    </label>

                    <select
                        id="agence_id"
                        name="agence_id"
                        required
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500">

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


                <!-- Type -->

                <div class="mb-6">

                    <label
                        for="type"
                        class="block text-sm font-semibold text-slate-700 mb-2">

                        📦 Type d'abonnement

                    </label>

                    <input
                        type="text"
                        id="type"
                        name="type"
                        value="{{ old('type', $abonnement->type) }}"
                        required
                        maxlength="100"
                        placeholder="Exemple : Mensuel, Trimestriel..."
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500">

                </div>


                <!-- Montant -->

                <div class="mb-6">

                    <label
                        for="montant"
                        class="block text-sm font-semibold text-slate-700 mb-2">

                        💰 Montant

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
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 pr-20 focus:outline-none focus:ring-2 focus:ring-orange-500">

                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">
                            FCFA
                        </span>

                    </div>

                </div>


                <!-- Dates -->

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                    <!-- Date début -->

                    <div>

                        <label
                            for="date_debut"
                            class="block text-sm font-semibold text-slate-700 mb-2">

                            📅 Date de début

                        </label>

                        <input
                            type="date"
                            id="date_debut"
                            name="date_debut"
                            value="{{ old('date_debut', \Carbon\Carbon::parse($abonnement->date_debut)->format('Y-m-d')) }}"
                            required
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500">

                    </div>


                    <!-- Date fin -->

                    <div>

                        <label
                            for="date_fin"
                            class="block text-sm font-semibold text-slate-700 mb-2">

                            📅 Date de fin

                        </label>

                        <input
                            type="date"
                            id="date_fin"
                            name="date_fin"
                            value="{{ old('date_fin', \Carbon\Carbon::parse($abonnement->date_fin)->format('Y-m-d')) }}"
                            required
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500">

                    </div>

                </div>


                <!-- Statut -->

                <div class="mb-8">

                    <label
                        for="statut"
                        class="block text-sm font-semibold text-slate-700 mb-2">

                        📌 Statut

                    </label>

                    <select
                        id="statut"
                        name="statut"
                        required
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-orange-500">

                        <option
                            value="Actif"
                            {{ old('statut', $abonnement->statut) == 'Actif' ? 'selected' : '' }}>

                            ✅ Actif

                        </option>

                        <option
                            value="Expiré"
                            {{ old('statut', $abonnement->statut) == 'Expiré' ? 'selected' : '' }}>

                            ❌ Expiré

                        </option>

                    </select>

                </div>


                <!-- Boutons -->

                <div class="flex flex-col sm:flex-row gap-4">

                    <!-- Enregistrer -->

                    <button
                        type="submit"
                        class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-bold px-6 py-3 rounded-xl shadow-md transition">

                        💾 Enregistrer les modifications

                    </button>


                    <!-- Annuler -->

                    <a
                        href="{{ route('admin.abonnements') }}"
                        class="flex-1 bg-slate-600 hover:bg-slate-700 text-white font-bold px-6 py-3 rounded-xl text-center shadow-md transition">

                        ↩️ Annuler

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

</x-layouts.admin>