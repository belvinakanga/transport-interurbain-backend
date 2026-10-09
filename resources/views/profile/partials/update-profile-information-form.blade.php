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
                class="tk-form-label">

                <i class="fa-solid fa-user text-brand mr-1.5"></i>

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
                class="tk-input">


        </div>

        <!-- Email -->

        <div>

            <label
                for="email"
                class="tk-form-label">

                <i class="fa-solid fa-envelope text-brand mr-1.5"></i>

                Adresse e-mail

            </label>

            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email', $user->email) }}"
                required
                autocomplete="username"
                class="tk-input">


        </div>

    </div>

    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())

        <div class="rounded-xl border border-[#FFD7B8] bg-[#FFF3E8] p-4">

            <div class="flex items-center justify-between flex-wrap gap-4">

                <div>

                    <p class="font-semibold text-[#9A3412]">

                        Adresse e-mail non vérifiée.

                    </p>


                </div>

                <button
                    form="send-verification"
                    class="tk-btn-accent">

                    <i class="fa-solid fa-paper-plane"></i>

                    Vérifier

                </button>

            </div>

        </div>

    @endif

    <div class="flex justify-end items-center gap-3 pt-2">

        <button
            type="button"
            @click="profileModal=false"
            class="tk-btn-ghost">

            Annuler

        </button>

        <button
            type="submit"
            class="tk-btn-accent">

            <i class="fa-solid fa-floppy-disk"></i>

            Enregistrer

        </button>

    </div>


</form>
