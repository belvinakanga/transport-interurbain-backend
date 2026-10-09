<section class="space-y-6">

    <header>

        <h2
            class="
                text-xl font-bold
                text-red-600
                flex items-center gap-2.5
            "
        >
            <i class="fa-solid fa-trash"></i>
            Zone de danger
        </h2>

        <p class="mt-2 text-sm text-slate-600">
            La suppression de votre compte est définitive.
            Toutes vos données seront supprimées et cette action est irréversible.
        </p>

    </header>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="tk-btn bg-red-600 text-white hover:bg-red-700">

        <i class="fa-solid fa-trash"></i>
        Supprimer mon compte

    </button>

    <x-modal
        name="confirm-user-deletion"
        :show="$errors->userDeletion->isNotEmpty()"
        focusable>

        <form
            method="POST"
            action="{{ route('profile.destroy') }}"
            class="p-6">

            @csrf
            @method('DELETE')

            <h2 class="text-2xl font-bold text-red-600">

                Confirmer la suppression

            </h2>

            <p class="mt-4 text-sm text-slate-600">

                Cette opération supprimera définitivement votre compte
                ainsi que toutes les données associées.

                <br><br>

                Entrez votre mot de passe pour confirmer.

            </p>

            <div class="mt-6">

                <label
                    for="password"
                    class="tk-form-label">

                    Mot de passe

                </label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    class="tk-input"
                    placeholder="Votre mot de passe">


            </div>

            <div class="mt-8 flex justify-end gap-4">

                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="tk-btn-ghost">

                    Annuler

                </button>

                <button
                    type="submit"
                    class="tk-btn bg-red-600 text-white hover:bg-red-700">

                    Supprimer définitivement

                </button>

            </div>

        </form>

    </x-modal>

</section>
