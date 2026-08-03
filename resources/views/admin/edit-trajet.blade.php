<x-layouts.admin>

<div class="py-12">

    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white p-8 rounded-xl shadow">

            <h1 class="text-3xl font-bold mb-6">
                ✏️ Modifier un trajet
            </h1>

            @if ($errors->any())

                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 p-4 rounded">

                    <ul class="list-disc list-inside">

                        @foreach ($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif

            <form method="POST" action="/admin/trajets/{{ $trajet->id }}">

                @csrf
                @method('PUT')

                <div class="mb-4">

                    <label class="block font-semibold mb-2">
                        Agence
                    </label>

                    <select
                        name="agence_id"
                        class="w-full border rounded p-3"
                        required>

                        @foreach($agences as $agence)

                            <option
                                value="{{ $agence->id }}"
                                {{ $trajet->agence_id == $agence->id ? 'selected' : '' }}>

                                {{ $agence->nom_agence }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="mb-4">

                    <label class="block font-semibold mb-2">
                        Départ
                    </label>

                    <input
                        type="text"
                        name="depart"
                        value="{{ old('depart', $trajet->depart) }}"
                        class="w-full border rounded p-3"
                        required>

                </div>

                <div class="mb-4">

                    <label class="block font-semibold mb-2">
                        Arrivée
                    </label>

                    <input
                        type="text"
                        name="arrivee"
                        value="{{ old('arrivee', $trajet->arrivee) }}"
                        class="w-full border rounded p-3"
                        required>

                </div>

                <div class="mb-4">

                    <label class="block font-semibold mb-2">
                        Date de départ
                    </label>

                    <input
                        type="date"
                        name="date_depart"
                        value="{{ old('date_depart', $trajet->date_depart) }}"
                        class="w-full border rounded p-3"
                        required>

                </div>

                <div class="mb-4">

                    <label class="block font-semibold mb-2">
                        Heure de départ
                    </label>

                    <input
                        type="time"
                        name="heure_depart"
                        value="{{ old('heure_depart', $trajet->heure_depart) }}"
                        class="w-full border rounded p-3"
                        required>

                </div>

                <div class="mb-4">

                    <label class="block font-semibold mb-2">
                        Prix (FCFA)
                    </label>

                    <input
                        type="number"
                        name="prix"
                        value="{{ old('prix', $trajet->prix) }}"
                        class="w-full border rounded p-3"
                        required>

                </div>

                <div class="flex gap-4 mt-6">

                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded">

                        💾 Mettre à jour

                    </button>

                    <a
                        href="/admin/trajets"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded">

                        ← Retour

                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

</x-layouts.admin>