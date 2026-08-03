<x-layouts.admin>

    <div class="py-12">

        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 rounded shadow">

                <h1 style="font-size:30px; font-weight:bold; margin-bottom:20px;">
                    Nouveau trajet
                </h1>

                @if ($errors->any())

                    <div style="
                        background:#fee2e2;
                        color:#b91c1c;
                        padding:15px;
                        border-radius:5px;
                        margin-bottom:20px;
                    ">

                        <ul>

                            @foreach ($errors->all() as $error)

                                <li>{{ $error }}</li>

                            @endforeach

                        </ul>

                    </div>

                @endif

                <form action="/admin/trajets/store" method="POST">

                    @csrf

                    <div style="margin-bottom:15px;">

                        <label style="font-weight:bold;">
                            Agence
                        </label>

                        <select
                            name="agence_id"
                            style="
                                width:100%;
                                padding:10px;
                                border:1px solid #ccc;
                                border-radius:5px;
                                margin-top:5px;
                            ">

                            @foreach($agences as $agence)

                                <option value="{{ $agence->id }}">
                                    {{ $agence->nom_agence }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div style="margin-bottom:15px;">

                        <label style="font-weight:bold;">
                            Ville de départ
                        </label>

                        <input
                            type="text"
                            name="depart"
                            required
                            style="
                                width:100%;
                                padding:10px;
                                border:1px solid #ccc;
                                border-radius:5px;
                                margin-top:5px;
                            ">

                    </div>

                    <div style="margin-bottom:15px;">

                        <label style="font-weight:bold;">
                            Ville d'arrivée
                        </label>

                        <input
                            type="text"
                            name="arrivee"
                            required
                            style="
                                width:100%;
                                padding:10px;
                                border:1px solid #ccc;
                                border-radius:5px;
                                margin-top:5px;
                            ">

                    </div>

                    <div style="margin-bottom:15px;">

                        <label style="font-weight:bold;">
                            Date de départ
                        </label>

                        <input
                            type="date"
                            name="date_depart"
                            required
                            style="
                                width:100%;
                                padding:10px;
                                border:1px solid #ccc;
                                border-radius:5px;
                                margin-top:5px;
                            ">

                    </div>

                    <div style="margin-bottom:15px;">

                        <label style="font-weight:bold;">
                            Heure de départ
                        </label>

                        <input
                            type="time"
                            name="heure_depart"
                            required
                            style="
                                width:100%;
                                padding:10px;
                                border:1px solid #ccc;
                                border-radius:5px;
                                margin-top:5px;
                            ">

                    </div>

                    <div style="margin-bottom:15px;">

                        <label style="font-weight:bold;">
                            Prix (FCFA)
                        </label>

                        <input
                            type="number"
                            name="prix"
                            required
                            style="
                                width:100%;
                                padding:10px;
                                border:1px solid #ccc;
                                border-radius:5px;
                                margin-top:5px;
                            ">

                    </div>

                    <div style="margin-bottom:20px;">

                        <label style="font-weight:bold;">
                            Nombre de places
                        </label>

                        <input
                            type="number"
                            name="places_totales"
                            required
                            style="
                                width:100%;
                                padding:10px;
                                border:1px solid #ccc;
                                border-radius:5px;
                                margin-top:5px;
                            ">

                    </div>

                    <div style="margin-top:20px;">

                        <button
                            type="submit"
                            style="
                                background:red;
                                color:white;
                                padding:12px 25px;
                                border:none;
                                border-radius:5px;
                                font-weight:bold;
                                cursor:pointer;
                            ">

                            ENREGISTRER LE TRAJET

                        </button>

                        <a
                            href="/admin/trajets"
                            style="
                                background:blue;
                                color:white;
                                padding:12px 25px;
                                border-radius:5px;
                                text-decoration:none;
                                margin-left:10px;
                                font-weight:bold;
                            ">

                            RETOUR

                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-layouts.admin>