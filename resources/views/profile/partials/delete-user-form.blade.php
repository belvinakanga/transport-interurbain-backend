<section class="space-y-6">

    <header>

        <h2 class="text-3xl font-bold text-red-700">
            🗑 Zone de danger
        </h2>

        <p class="mt-2 text-gray-600">
            La suppression de votre compte est définitive.
            Toutes vos données seront supprimées et cette action est irréversible.
        </p>

    </header>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-red-600 hover:bg-red-700 text-white font-bold px-8 py-3 rounded-xl shadow-lg transition">

        🗑 Supprimer mon compte

    </button>

    <x-modal
        name="confirm-user-deletion"
        :show="$errors->userDeletion->isNotEmpty()"
        focusable>

        <form
            method="POST"
            action="{{ route('profile.destroy') }}"
            class="p-8">

            @csrf
            @method('DELETE')

            <h2 class="text-2xl font-bold text-red-600">

                Confirmer la suppression

            </h2>

            <p class="mt-4 text-gray-600">

                Cette opération supprimera définitivement votre compte
                ainsi que toutes les données associées.

                <br><br>

                Entrez votre mot de passe pour confirmer.

            </p>

            <div class="mt-6">

                <label
                    for="password"
                    class="block mb-2 font-semibold">

                    Mot de passe

                </label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    class="w-full rounded-xl border-gray-300 focus:ring-red-500 focus:border-red-500"
                    placeholder="Votre mot de passe">

                <x-input-error
                    :messages="$errors->userDeletion->get('password')"
                    class="mt-2"/>

            </div>

            <div class="mt-8 flex justify-end gap-4">

                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="px-6 py-3 rounded-xl bg-gray-200 hover:bg-gray-300 font-semibold">

                    Annuler

                </button>

                <button
                    type="submit"
                    class="px-6 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold">

                    Supprimer définitivement

                </button>

            </div>

        </form>

    </x-modal>

</section>