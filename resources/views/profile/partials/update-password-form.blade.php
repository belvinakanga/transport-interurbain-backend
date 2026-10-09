<section>

    <header class="mb-8">

        <h2
            class="
                text-xl font-bold
                text-navy
                flex items-center gap-2.5
            "
        >
            <i class="fa-solid fa-lock text-brand"></i>
            Modifier le mot de passe
        </h2>

        <p class="mt-2 text-sm text-slate-500">
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
                class="tk-form-label">

                Mot de passe actuel

            </label>

            <div class="relative">

                <input
                    id="current_password"
                    name="current_password"
                    type="password"
                    autocomplete="current-password"
                    class="tk-input pr-12">

                <button
                    type="button"
                    onclick="togglePassword('current_password', this)"
                    class="absolute right-2 top-1/2 -translate-y-1/2 inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:text-navy">

                    <i class="fa-solid fa-eye"></i>

                </button>

            </div>


        </div>

        <!-- Nouveau mot de passe -->

        <div>

            <label
                for="password"
                class="tk-form-label">

                Nouveau mot de passe

            </label>

            <div class="relative">

                <input
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    class="tk-input pr-12">

                <button
                    type="button"
                    onclick="togglePassword('password', this)"
                    class="absolute right-2 top-1/2 -translate-y-1/2 inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:text-navy">

                    <i class="fa-solid fa-eye"></i>

                </button>

            </div>


        </div>

        <!-- Confirmation -->

        <div>

            <label
                for="password_confirmation"
                class="tk-form-label">

                Confirmer le mot de passe

            </label>

            <div class="relative">

                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    class="tk-input pr-12">

                <button
                    type="button"
                    onclick="togglePassword('password_confirmation', this)"
                    class="absolute right-2 top-1/2 -translate-y-1/2 inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:text-navy">

                    <i class="fa-solid fa-eye"></i>

                </button>

            </div>


        </div>

        <div class="flex items-center gap-4">

            <button
                type="submit"
                class="tk-btn-accent">

                <i class="fa-solid fa-lock"></i>

                Modifier le mot de passe

            </button>


        </div>

    </form>

</section>

<script>

function togglePassword(id, button)
{
    let input = document.getElementById(id);

    if (input.type === "password") {

        input.type = "text";
        button.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';

    } else {

        input.type = "password";
        button.innerHTML = '<i class="fa-solid fa-eye"></i>';

    }
}

</script>
