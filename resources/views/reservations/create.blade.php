<x-app-layout>

    <div class="py-12">
        <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">

            <h2 class="text-2xl font-bold mb-6">
                Réserver : {{ $trajet->depart }} - {{ $trajet->arrivee }}
            </h2>

            <form method="POST" action="/reservation/store">

                @csrf

                <input
                    type="hidden"
                    name="trajet_id"
                    value="{{ $trajet->id }}">

                <div class="mb-4">
                    <label for="nombre_places" class="block mb-2 font-semibold">
                        Nombre de places
                    </label>

                    <input
                        id="nombre_places"
                        type="number"
                        name="nombre_places"
                        value="1"
                        min="1"
                        required
                        class="border border-gray-300 p-2 w-full rounded">
                </div>

                <div class="mt-6">
                    <input
                        type="submit"
                        value="Confirmer réservation"
                        style="background-color: green; color: white; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                </div>

            </form>

        </div>
    </div>

</x-app-layout>