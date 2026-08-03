<section>

    <header class="mb-8">

        <h2 class="text-3xl font-bold text-slate-800">
            🔒 Modifier le mot de passe
        </h2>

        <p class="mt-2 text-gray-500">
            Choisissez un mot de passe sécurisé afin de protéger votre compte.
        </p>

    </header>

    <form method="POST"
          action="{{ route('password.update') }}"
          class="space-y-6">

        @csrf
        @method('PUT')

        <!-- Mot de passe actuel -->

        <div>

            <label
                for="current_password"
                class="block mb-2 text-sm font-semibold text-slate-700">

                Mot de passe actuel

            </label>

            <div class="relative">

                <input
                    id="current_password"
                    name="current_password"
                    type="password"
                    autocomplete="current-password"
                    class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500 pr-12">

                <button
                    type="button"
                    onclick="togglePassword('current_password', this)"
                    class="absolute inset-y-0 right-0 px-4 text-gray-500 hover:text-orange-500">

                    👁️

                </button>

            </div>

            <x-input-error
                :messages="$errors->updatePassword->get('current_password')"
                class="mt-2"/>

        </div>

        <!-- Nouveau mot de passe -->

        <div>

            <label
                for="password"
                class="block mb-2 text-sm font-semibold text-slate-700">

                Nouveau mot de passe

            </label>

            <div class="relative">

                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500 pr-12">

                <button
                    type="button"
                    onclick="togglePassword('password', this)"
                    class="absolute inset-y-0 right-0 px-4 text-gray-500 hover:text-orange-500">

                    👁️

                </button>

            </div>

            <x-input-error
                :messages="$errors->updatePassword->get('password')"
                class="mt-2"/>

        </div>

        <!-- Confirmation -->

        <div>

            <label
                for="password_confirmation"
                class="block mb-2 text-sm font-semibold text-slate-700">

                Confirmer le mot de passe

            </label>

            <div class="relative">

                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500 pr-12">

                <button
                    type="button"
                    onclick="togglePassword('password_confirmation', this)"
                    class="absolute inset-y-0 right-0 px-4 text-gray-500 hover:text-orange-500">

                    👁️

                </button>

            </div>

            <x-input-error
                :messages="$errors->updatePassword->get('password_confirmation')"
                class="mt-2"/>

        </div>

        <div class="flex items-center gap-4">

            <button
                type="submit"
                class="bg-orange-500 hover:bg-orange-600 text-white px-8 py-3 rounded-xl font-bold shadow-lg transition">

                🔒 Modifier le mot de passe

            </button>

            @if (session('status') === 'password-updated')

                <span
                    x-data="{ show:true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show=false,2000)"
                    class="text-green-600 font-semibold">

                    ✔ Mot de passe modifié avec succès.

                </span>

            @endif

        </div>

    </form>

</section>

<script>

function togglePassword(id, button)
{
    let input = document.getElementById(id);

    if (input.type === "password") {

        input.type = "text";
        button.innerHTML = "🙈";

    } else {

        input.type = "password";
        button.innerHTML = "👁️";

    }
}

</script>