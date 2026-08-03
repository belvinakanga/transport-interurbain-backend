<x-app-layout>

    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <h1 class="text-3xl font-bold mb-8">
                🎫 Mes billets
            </h1>

            @if(session('success'))

                <div class="bg-green-100 border border-green-400 text-green-700 p-4 rounded mb-6">

                    {{ session('success') }}

                </div>

            @endif

            @forelse($billets as $billet)

                <div class="bg-white rounded-lg shadow p-6 mb-6">

                    <h2 class="text-2xl font-bold text-blue-700 mb-4">

                        Billet N° {{ $billet->numero_billet }}

                    </h2>

                    <p class="mb-2">

                        <strong>Agence :</strong>

                        {{ $billet->reservation->trajet->agence->nom_agence }}

                    </p>

                    <p class="mb-2">

                        <strong>Trajet :</strong>

                        {{ $billet->reservation->trajet->depart }}

                        →

                        {{ $billet->reservation->trajet->arrivee }}

                    </p>

                    <p class="mb-2">

                        <strong>Date :</strong>

                        {{ $billet->reservation->trajet->date_depart }}

                    </p>

                    <p class="mb-2">

                        <strong>Heure :</strong>

                        {{ $billet->reservation->trajet->heure_depart }}

                    </p>

                    <p class="mb-2">

                        <strong>Nombre de places :</strong>

                        {{ $billet->reservation->nombre_places }}

                    </p>

                    <p class="mb-4">

                        <strong>Statut :</strong>

                        <span class="text-green-600 font-bold">

                            {{ $billet->reservation->statut }}

                        </span>

                    </p>

                    <div class="flex gap-4">
                    <a
    href="#"
    style="
        display:inline-block;
        background:#2563eb;
        color:white;
        padding:12px 22px;
        border-radius:6px;
        text-decoration:none;
        font-weight:bold;
    ">

    📄 Télécharger le billet PDF

</a>

                    </div>

                </div>

            @empty

                <div class="bg-white rounded-lg shadow p-6 text-center">

                    <h2 class="text-xl font-semibold">

                        Aucun billet disponible.

                    </h2>

                </div>

            @endforelse

        </div>

    </div>

</x-app-layout>