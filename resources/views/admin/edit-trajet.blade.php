<x-layouts.admin :header="'Modifier un trajet'">

<div class="tk-page">

    {{-- En-tête --}}

    <div
        class="
            tk-page-head
            flex flex-col gap-4
            md:flex-row md:items-center
            md:justify-between
        "
    >

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

            Modifier un trajet

        </h1>

        <a
            href="/admin/trajets"
            class="tk-btn-ghost shrink-0"
        >
            <i class="fa-solid fa-arrow-left"></i>

            Retour
        </a>

    </div>


    {{-- Formulaire --}}

    <div class="tk-card p-6">


        <form method="POST" action="/admin/trajets/{{ $trajet->id }}">

            @csrf
            @method('PUT')

            <div class="mb-5">

                <label class="tk-form-label">
                    Agence
                </label>

                <select
                    name="agence_id"
                    class="tk-input"
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

            <div class="mb-5">

                <label class="tk-form-label">
                    Départ
                </label>

                <input
                    type="text"
                    name="depart"
                    value="{{ old('depart', $trajet->depart) }}"
                    class="tk-input"
                    required>

            </div>

            <div class="mb-5">

                <label class="tk-form-label">
                    Arrivée
                </label>

                <input
                    type="text"
                    name="arrivee"
                    value="{{ old('arrivee', $trajet->arrivee) }}"
                    class="tk-input"
                    required>

            </div>

            <div class="mb-5">

                <label class="tk-form-label">
                    Date de départ
                </label>

                <input
                    type="date"
                    name="date_depart"
                    value="{{ old('date_depart', $trajet->date_depart) }}"
                    class="tk-input"
                    required>

            </div>

            <div class="mb-5">

                <label class="tk-form-label">
                    Heure de départ
                </label>

                <input
                    type="time"
                    name="heure_depart"
                    value="{{ old('heure_depart', $trajet->heure_depart) }}"
                    class="tk-input"
                    required>

            </div>

            <div class="mb-5">

                <label class="tk-form-label">
                    Prix (FCFA)
                </label>

                <input
                    type="number"
                    name="prix"
                    value="{{ old('prix', $trajet->prix) }}"
                    class="tk-input"
                    required>

            </div>

            <div class="flex flex-wrap gap-3 mt-6">

                <button
                    type="submit"
                    class="tk-btn-accent">

                    <i class="fa-solid fa-check"></i>

                    Mettre à jour

                </button>

                <a
                    href="/admin/trajets"
                    class="tk-btn-ghost">

                    <i class="fa-solid fa-arrow-left"></i>

                    Retour

                </a>

            </div>

        </form>

    </div>

</div>

</x-layouts.admin>
