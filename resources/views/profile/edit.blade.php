<x-layouts.admin>

<div
    x-data="{
        profileModal:false,
        passwordModal:false,
        deleteModal:false
    }"
    class="p-8">

    <!-- ===================================== -->
    <!-- TITRE -->
    <!-- ===================================== -->

    <div class="mb-6">

        <h1 class="text-4xl font-bold text-gray-800 flex items-center gap-3">

            <i class="fas fa-user-circle text-yellow-500"></i>

            Mon Profil

        </h1>

        <p class="text-gray-500 mt-2">

            Consultez et gérez votre compte administrateur.

        </p>

    </div>

    <!-- ===================================== -->
    <!-- BANNIERE -->
    <!-- ===================================== -->

    <div class="bg-gradient-to-r from-gray-900 via-gray-800 to-black rounded-2xl shadow-sm overflow-hidden mb-6">

        <div class="px-8 py-6">

            <div class="flex flex-col lg:flex-row items-center justify-between gap-6">

                <div class="flex items-center gap-6">

                    <!-- Avatar -->

                    <div
                        class="w-20 h-20 rounded-full bg-yellow-500 flex items-center justify-center text-white text-4xl font-bold shadow-lg">

                        {{ strtoupper(substr(Auth::user()->name,0,1)) }}

                    </div>

                    <!-- Infos -->

                    <div class="text-white">

                        <h2 class="text-3xl font-bold">

                            {{ Auth::user()->name }}

                        </h2>

                        <p class="text-gray-300 mt-1">

                            <i class="fas fa-envelope mr-2"></i>

                            {{ Auth::user()->email }}

                        </p>

                        <div class="flex flex-wrap gap-3 mt-4">

                            <span
                                class="bg-yellow-500 text-white px-4 py-2 rounded-full text-sm font-semibold">

                                <i class="fas fa-user-shield mr-2"></i>

                                Administrateur

                            </span>

                            <span
                                class="bg-green-500 text-white px-4 py-2 rounded-full text-sm font-semibold">

                                <i class="fas fa-circle-check mr-2"></i>

                                Actif

                            </span>

                        </div>

                    </div>

                </div>

                <!-- Actions -->

                <div class="flex gap-3">

                    <button
                        @click="profileModal=true"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-3 rounded-xl transition">

                        <i class="fas fa-pen mr-2"></i>

                        Modifier

                    </button>

                    <button
                        class="bg-white/10 hover:bg-white/20 text-white px-5 py-3 rounded-xl transition">

                        <i class="fas fa-eye mr-2"></i>

                        Voir

                    </button>

                </div>

            </div>

        </div>

    </div>

    <!-- ===================================== -->
    <!-- CARTES -->
    <!-- ===================================== -->

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        <!-- Informations -->

        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm">

            <div class="flex justify-between items-center border-b px-6 py-5">

                <h2 class="text-xl font-bold text-gray-800">

                    <i class="fas fa-id-card text-yellow-500 mr-2"></i>

                    Informations personnelles

                </h2>

                <button
                    @click="profileModal=true"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg">

                    <i class="fas fa-pen mr-2"></i>

                    Modifier

                </button>

            </div>

            <div class="p-6">

                <div class="grid md:grid-cols-2 gap-6">

                    <div>

                        <p class="text-gray-500 text-sm">

                            Nom

                        </p>

                        <p class="font-semibold text-lg">

                            {{ Auth::user()->name }}

                        </p>

                    </div>

                    <div>

                        <p class="text-gray-500 text-sm">

                            Adresse e-mail

                        </p>

                        <p class="font-semibold text-lg">

                            {{ Auth::user()->email }}

                        </p>

                    </div>

                </div>

            </div>

        </div>

        <!-- Compte -->

        <div class="bg-white rounded-2xl shadow-sm">

            <div class="border-b px-6 py-5">

                <h2 class="text-xl font-bold text-gray-800">

                    <i class="fas fa-circle-info text-yellow-500 mr-2"></i>

                    Informations du compte

                </h2>

            </div>

            <div class="p-6 space-y-5">

                <div class="flex justify-between">

                    <span class="text-gray-500">

                        Rôle

                    </span>

                    <span class="font-semibold">

                        Administrateur

                    </span>

                </div>

                <div class="flex justify-between">

                    <span class="text-gray-500">

                        Statut

                    </span>

                    <span class="text-green-600 font-semibold">

                        Actif

                    </span>

                </div>

                <div class="flex justify-between">

                    <span class="text-gray-500">

                        Créé le

                    </span>

                    <span class="font-semibold">

                        {{ Auth::user()->created_at->format('d/m/Y') }}

                    </span>

                </div>

            </div>

        </div>

    </div>
        <!-- ===================================== -->
    <!-- SECURITE -->
    <!-- ===================================== -->

    <div class="bg-white rounded-2xl shadow-sm mb-6">

        <div class="flex justify-between items-center border-b px-6 py-5">

            <h2 class="text-xl font-bold text-gray-800">

                <i class="fas fa-shield-halved text-yellow-500 mr-2"></i>

                Sécurité

            </h2>

            <button
                @click="passwordModal = true"
                class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition">

                <i class="fas fa-key mr-2"></i>

                Modifier

            </button>

        </div>

        <div class="p-6">

            <div class="flex items-center justify-between">

                <div>

                    <p class="text-sm text-gray-500">

                        Mot de passe

                    </p>

                    <p class="text-2xl tracking-[8px] font-bold">

                        ••••••••••••

                    </p>

                </div>

                <div>

                    <span
                        class="inline-flex items-center px-4 py-2 rounded-full bg-green-100 text-green-700 text-sm font-semibold">

                        <i class="fas fa-lock mr-2"></i>

                        Sécurisé

                    </span>

                </div>

            </div>

        </div>

    </div>

    <!-- ===================================== -->
    <!-- ZONE DANGEREUSE -->
    <!-- ===================================== -->

    <div class="bg-white rounded-2xl shadow-sm border-l-4 border-red-500 mb-6">

        <div class="flex justify-between items-center px-6 py-5 border-b">

            <div>

                <h2 class="text-xl font-bold text-red-600">

                    <i class="fas fa-triangle-exclamation mr-2"></i>

                    Zone dangereuse

                </h2>

                <p class="text-gray-500 text-sm mt-1">

                    La suppression du compte est définitive.

                </p>

            </div>

            <button
                @click="deleteModal = true"
                class="bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-lg transition">

                <i class="fas fa-trash mr-2"></i>

                Supprimer

            </button>

        </div>

        <div class="p-6">

            <p class="text-gray-600">

                Cette action supprimera définitivement votre compte
                ainsi que toutes les données associées.

            </p>

        </div>

    </div>

    <!-- ===================================== -->
    <!-- MODAL PROFIL -->
    <!-- ===================================== -->

    <div
        x-show="profileModal"
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
        style="display:none;">

        <div
            @click.outside="profileModal = false"
            class="bg-white rounded-2xl shadow-xl w-full max-w-3xl overflow-hidden">

            <div class="bg-gray-900 px-6 py-5 flex justify-between items-center">

                <h2 class="text-xl font-bold text-white">

                    <i class="fas fa-user-edit text-yellow-500 mr-2"></i>

                    Modifier mes informations

                </h2>

                <button
                    @click="profileModal = false"
                    class="text-white hover:text-red-400 text-xl">

                    <i class="fas fa-times"></i>

                </button>

            </div>

            <div class="p-6">
            @include('profile.partials.update-profile-information-form')

</div>

</div>

</div>

<!-- ===================================== -->
<!-- MODAL MOT DE PASSE -->
<!-- ===================================== -->

<div
x-show="passwordModal"
x-transition
class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
style="display:none;">

<div
@click.outside="passwordModal = false"
class="bg-white rounded-2xl shadow-xl w-full max-w-3xl overflow-hidden">

<div class="bg-gray-900 px-6 py-5 flex justify-between items-center">

    <h2 class="text-xl font-bold text-white">

        <i class="fas fa-lock text-yellow-500 mr-2"></i>

        Modifier le mot de passe

    </h2>

    <button
        @click="passwordModal = false"
        class="text-white hover:text-red-400 text-xl">

        <i class="fas fa-times"></i>

    </button>

</div>

<div class="p-6">

    @include('profile.partials.update-password-form')

</div>

</div>

</div>

<!-- ===================================== -->
<!-- MODAL SUPPRESSION -->
<!-- ===================================== -->

<div
x-show="deleteModal"
x-transition
class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
style="display:none;">

<div
@click.outside="deleteModal = false"
class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden">

<div class="bg-red-600 px-6 py-5 flex justify-between items-center">

    <h2 class="text-xl font-bold text-white">

        <i class="fas fa-triangle-exclamation mr-2"></i>

        Confirmation

    </h2>

    <button
        @click="deleteModal = false"
        class="text-white hover:text-gray-200 text-xl">

        <i class="fas fa-times"></i>

    </button>

</div>

<div class="p-6">

    @include('profile.partials.delete-user-form')

</div>

</div>

</div>

</div>

</x-layouts.admin>