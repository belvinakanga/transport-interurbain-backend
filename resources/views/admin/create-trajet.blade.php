@php
    $layout = auth()->user()->role === 'agent'
        ? 'layouts.agent'
        : 'layouts.admin';
@endphp

<x-dynamic-component
    :component="$layout"
    :header="'Nouveau trajet'"
>

    <div class="py-12">

        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 rounded-2xl shadow">

                {{-- =========================================================
                     TITRE
                ========================================================== --}}

                <h1 style="
                    font-size:30px;
                    font-weight:bold;
                    margin-bottom:25px;
                    color:#111827;
                ">
                    Nouveau trajet
                </h1>


                {{-- =========================================================
                     ERREURS
                ========================================================== --}}

                @if ($errors->any())

                    <div style="
                        background:#fee2e2;
                        color:#b91c1c;
                        padding:15px;
                        border-radius:8px;
                        margin-bottom:20px;
                    ">

                        <strong>
                            Veuillez corriger les erreurs suivantes :
                        </strong>

                        <ul style="
                            margin-top:10px;
                            padding-left:20px;
                        ">

                            @foreach ($errors->all() as $error)

                                <li>
                                    {{ $error }}
                                </li>

                            @endforeach

                        </ul>

                    </div>

                @endif


                {{-- =========================================================
                     MESSAGE AGENT
                ========================================================== --}}

                @if(auth()->user()->role === 'agent')

                    <div style="
                        background:#eff6ff;
                        border:1px solid #bfdbfe;
                        color:#1e40af;
                        padding:15px;
                        border-radius:10px;
                        margin-bottom:20px;
                    ">

                        <strong>
                            👨‍💼 Vous êtes connecté en tant qu'agent.
                        </strong>

                        <br>

                        Ce trajet sera automatiquement rattaché à votre agence :

                        <strong>
                            {{ auth()->user()->agence->nom_agence ?? 'Aucune agence' }}
                        </strong>

                    </div>

                @endif


                {{-- =========================================================
                     FORMULAIRE
                ========================================================== --}}

                <form
                    action="{{ url('/admin/trajets/store') }}"
                    method="POST"
                >

                    @csrf


                    {{-- =====================================================
                         AGENCE
                    ====================================================== --}}

                    <div style="margin-bottom:18px;">

                        <label
                            style="
                                font-weight:bold;
                                display:block;
                                margin-bottom:7px;
                            "
                        >
                            Agence
                        </label>


                        @if(auth()->user()->role === 'agent')

                            {{-- AGENT : agence imposée --}}

                            <div style="
                                width:100%;
                                padding:12px;
                                border:1px solid #d1d5db;
                                border-radius:8px;
                                background:#f3f4f6;
                                color:#374151;
                                box-sizing:border-box;
                            ">

                                🏢
                                {{ auth()->user()->agence->nom_agence ?? 'Aucune agence' }}

                            </div>

                            <input
                                type="hidden"
                                name="agence_id"
                                value="{{ auth()->user()->agence_id }}"
                            >

                        @else

                            {{-- ADMIN : choix de l'agence --}}

                            <select
                                name="agence_id"
                                required
                                style="
                                    width:100%;
                                    padding:12px;
                                    border:1px solid #ccc;
                                    border-radius:8px;
                                    margin-top:5px;
                                    background:white;
                                "
                            >

                                <option value="">
                                    Sélectionner une agence
                                </option>

                                @foreach($agences as $agence)

                                    <option
                                        value="{{ $agence->id }}"
                                        {{ old('agence_id') == $agence->id ? 'selected' : '' }}
                                    >
                                        {{ $agence->nom_agence }}
                                    </option>

                                @endforeach

                            </select>

                        @endif

                    </div>


                    {{-- =====================================================
                         DÉPART
                    ====================================================== --}}

                    <div style="margin-bottom:18px;">

                        <label
                            style="
                                font-weight:bold;
                                display:block;
                                margin-bottom:7px;
                            "
                        >
                            Ville de départ
                        </label>

                        <input
                            type="text"
                            name="depart"
                            value="{{ old('depart') }}"
                            placeholder="Exemple : Brazzaville"
                            required
                            style="
                                width:100%;
                                padding:12px;
                                border:1px solid #ccc;
                                border-radius:8px;
                                box-sizing:border-box;
                            "
                        >

                    </div>


                    {{-- =====================================================
                         ARRIVÉE
                    ====================================================== --}}

                    <div style="margin-bottom:18px;">

                        <label
                            style="
                                font-weight:bold;
                                display:block;
                                margin-bottom:7px;
                            "
                        >
                            Ville d'arrivée
                        </label>

                        <input
                            type="text"
                            name="arrivee"
                            value="{{ old('arrivee') }}"
                            placeholder="Exemple : Pointe-Noire"
                            required
                            style="
                                width:100%;
                                padding:12px;
                                border:1px solid #ccc;
                                border-radius:8px;
                                box-sizing:border-box;
                            "
                        >

                    </div>


                    {{-- =====================================================
                         DATE
                    ====================================================== --}}

                    <div style="margin-bottom:18px;">

                        <label
                            style="
                                font-weight:bold;
                                display:block;
                                margin-bottom:7px;
                            "
                        >
                            Date de départ
                        </label>

                        <input
                            type="date"
                            name="date_depart"
                            value="{{ old('date_depart') }}"
                            required
                            style="
                                width:100%;
                                padding:12px;
                                border:1px solid #ccc;
                                border-radius:8px;
                                box-sizing:border-box;
                            "
                        >

                    </div>


                    {{-- =====================================================
                         HEURE
                    ====================================================== --}}

                    <div style="margin-bottom:18px;">

                        <label
                            style="
                                font-weight:bold;
                                display:block;
                                margin-bottom:7px;
                            "
                        >
                            Heure de départ
                        </label>

                        <input
                            type="time"
                            name="heure_depart"
                            value="{{ old('heure_depart') }}"
                            required
                            style="
                                width:100%;
                                padding:12px;
                                border:1px solid #ccc;
                                border-radius:8px;
                                box-sizing:border-box;
                            "
                        >

                    </div>


                    {{-- =====================================================
                         PRIX
                    ====================================================== --}}

                    <div style="margin-bottom:18px;">

                        <label
                            style="
                                font-weight:bold;
                                display:block;
                                margin-bottom:7px;
                            "
                        >
                            Prix (FCFA)
                        </label>

                        <input
                            type="number"
                            name="prix"
                            value="{{ old('prix') }}"
                            min="0"
                            step="1"
                            placeholder="Exemple : 10000"
                            required
                            style="
                                width:100%;
                                padding:12px;
                                border:1px solid #ccc;
                                border-radius:8px;
                                box-sizing:border-box;
                            "
                        >

                    </div>


                    {{-- =====================================================
                         PLACES
                    ====================================================== --}}

                    <div style="margin-bottom:25px;">

                        <label
                            style="
                                font-weight:bold;
                                display:block;
                                margin-bottom:7px;
                            "
                        >
                            Nombre de places
                        </label>

                        <input
                            type="number"
                            name="places_totales"
                            value="{{ old('places_totales') }}"
                            min="1"
                            placeholder="Exemple : 20"
                            required
                            style="
                                width:100%;
                                padding:12px;
                                border:1px solid #ccc;
                                border-radius:8px;
                                box-sizing:border-box;
                            "
                        >

                    </div>


                    {{-- =====================================================
                         BOUTONS
                    ====================================================== --}}

                    <div
                        style="
                            display:flex;
                            flex-wrap:wrap;
                            gap:10px;
                            margin-top:20px;
                        "
                    >

                        <button
                            type="submit"
                            style="
                                background:#dc2626;
                                color:white;
                                padding:12px 25px;
                                border:none;
                                border-radius:8px;
                                font-weight:bold;
                                cursor:pointer;
                            "
                        >
                            ENREGISTRER LE TRAJET
                        </button>


                        <a
                            href="{{ route('admin.trajets') }}"
                            style="
                                background:#2563eb;
                                color:white;
                                padding:12px 25px;
                                border-radius:8px;
                                text-decoration:none;
                                font-weight:bold;
                                display:inline-block;
                            "
                        >
                            RETOUR
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-dynamic-component>
