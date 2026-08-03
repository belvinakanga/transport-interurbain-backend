<form id="send-verification" method="POST" action="{{ route('verification.send') }}">
    @csrf
</form>

<form
    method="POST"
    action="{{ route('profile.update') }}"
    class="space-y-6">

    @csrf
    @method('PATCH')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Nom -->

        <div>

            <label
                for="name"
                class="block text-sm font-semibold text-gray-700 mb-2">

                <i class="fas fa-user text-yellow-500 mr-2"></i>

                Nom complet

            </label>

            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name', $user->name) }}"
                required
                autofocus
                autocomplete="name"
                class="w-full rounded-xl border-gray-300 focus:border-yellow-500 focus:ring-yellow-500">

            <x-input-error
                :messages="$errors->get('name')"
                class="mt-2"/>

        </div>

        <!-- Email -->

        <div>

            <label
                for="email"
                class="block text-sm font-semibold text-gray-700 mb-2">

                <i class="fas fa-envelope text-yellow-500 mr-2"></i>

                Adresse e-mail

            </label>

            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
                class="w-full rounded-xl border-gray-300 focus:border-yellow-500 focus:ring-yellow-500">

            <x-input-error
                :messages="$errors->get('email')"
                class="mt-2"/>

        </div>

    </div>

    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())

        <div class="rounded-xl bg-yellow-50 border border-yellow-300 p-4">

            <div class="flex items-center justify-between flex-wrap gap-4">

                <div>

                    <p class="font-semibold text-yellow-700">

                        Adresse e-mail non vérifiée.

                    </p>

                    @if (session('status') === 'verification-link-sent')

                        <p class="text-green-600 text-sm mt-1">

                            ✔ Un nouveau lien de vérification a été envoyé.

                        </p>

                    @endif

                </div>

                <button
                    form="send-verification"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition">

                    <i class="fas fa-paper-plane mr-2"></i>

                    Vérifier

                </button>

            </div>

        </div>

    @endif

    <div class="flex justify-end items-center gap-3 pt-2">

        <button
            type="button"
            @click="profileModal=false"
            class="px-5 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 transition">

            Annuler

        </button>

        <button
            type="submit"
            class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-xl font-semibold transition">

            <i class="fas fa-floppy-disk mr-2"></i>

            Enregistrer

        </button>

    </div>

    @if (session('status') === 'profile-updated')

        <div
            x-data="{show:true}"
            x-show="show"
            x-transition
            x-init="setTimeout(()=>show=false,2500)"
            class="mt-4 rounded-xl bg-green-100 text-green-700 px-4 py-3">

            ✔ Vos informations ont été mises à jour avec succès.

        </div>

    @endif

</form>