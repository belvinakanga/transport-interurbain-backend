@props([
    'name',
    'label',
    'villes' => [],
    'selected' => null,
])

@php
    $valeur = old($name, $selected);

    // Une ville absente du référentiel — cas d'un ancien trajet — reste
    // modifiable : on bascule le champ en saisie libre pré-remplie.
    $horsListe = $valeur !== null
        && $valeur !== ''
        && !in_array($valeur, $villes, true);

    $idSelect = 'select-' . $name;
    $idAutre = 'autre-' . $name;
@endphp

<div class="mb-5" data-ville-groupe>

    <label class="tk-form-label" for="{{ $idSelect }}">
        {{ $label }}
    </label>

    <select
        id="{{ $idSelect }}"
        name="{{ $name }}"
        required
        class="tk-input"
        data-ville-select
        data-cible="{{ $idAutre }}"
    >
        <option value="" {{ $valeur ? '' : 'selected' }} disabled>
            Choisir une ville
        </option>

        @foreach ($villes as $ville)
            <option
                value="{{ $ville }}"
                {{ $valeur === $ville ? 'selected' : '' }}
            >
                {{ $ville }}
            </option>
        @endforeach

        <option value="__autre__" {{ $horsListe ? 'selected' : '' }}>
            Autre ville…
        </option>
    </select>

    {{-- Champ libre, révélé par l'option « Autre ville ». --}}
    <div
        id="{{ $idAutre }}"
        class="mt-3 {{ $horsListe ? '' : 'hidden' }}"
        data-ville-autre
    >
        <input
            type="text"
            name="{{ $name }}_autre"
            value="{{ $horsListe ? $valeur : old($name . '_autre') }}"
            placeholder="Nom de la nouvelle ville"
            class="tk-input"
        >

        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
            Cette ville sera ajoutée à la liste et proposée aux voyageurs
            dans l'application mobile.
        </p>
    </div>

</div>

{{-- Le composant porte son propre comportement : @once garantit qu'il
     n'est émis qu'une fois, même avec plusieurs sélecteurs sur la page. --}}
@once
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('[data-ville-select]').forEach(function (select) {

        const champLibre = document.getElementById(select.dataset.cible);
        if (!champLibre) return;

        const saisie = champLibre.querySelector('input');

        function basculer() {
            const autre = select.value === '__autre__';

            champLibre.classList.toggle('hidden', !autre);

            // Le champ libre n'est obligatoire que lorsqu'il est visible.
            if (saisie) {
                saisie.required = autre;
            }
        }

        select.addEventListener('change', function () {
            basculer();
            if (select.value === '__autre__' && saisie) saisie.focus();
        });

        basculer();
    });

});
</script>
@endonce
