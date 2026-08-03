<x-layouts.admin>

<div class="py-12">

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Titre -->

        <div class="flex justify-between items-center mb-6">

        <div class="flex justify-between items-center mb-6">

<h1 class="text-3xl font-bold text-slate-800">
    👥 Gestion des utilisateurs
</h1>

<a href="{{ route('voyageurs.create') }}"
   class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-3 rounded-xl shadow">

    ➕ Ajouter un utilisateur

</a>

</div>

        </div>

        <!-- Message de succès -->

        @if(session('success'))

            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6">

                {{ session('success') }}

            </div>

        @endif

        <!-- Barre de recherche -->

        <div class="bg-white rounded-xl shadow p-6 mb-6">

            <form action="/admin/voyageurs" method="GET">

                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                    <!-- Recherche -->

                    <input
                        type="text"
                        name="recherche"
                        value="{{ request('recherche') }}"
                        placeholder="🔍 Rechercher un utilisateur..."
                        class="border rounded-xl px-4 py-3 focus:ring-2 focus:ring-orange-500 focus:outline-none">

                    <!-- Rôle -->

                    <select
                        name="role"
                        class="border rounded-xl px-4 py-3">

                        <option value="">
                            Tous les rôles
                        </option>

                        <option
                            value="admin"
                            {{ request('role') == 'admin' ? 'selected' : '' }}>

                            Administrateur

                        </option>

                        <option
                            value="user"
                            {{ request('role') == 'user' ? 'selected' : '' }}>

                            Utilisateur

                        </option>

                    </select>

                    <!-- Pagination -->

                    <select
                        name="par_page"
                        class="border rounded-xl px-4 py-3">

                        @foreach([10,25,50,100] as $nb)

                            <option
                                value="{{ $nb }}"
                                {{ request('par_page',10) == $nb ? 'selected' : '' }}>

                                {{ $nb }} lignes

                            </option>

                        @endforeach

                    </select>

                    <!-- Filtrer -->

                    <button
                        type="submit"
                        class="bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-semibold">

                        🔍 Filtrer

                    </button>

                    <!-- Réinitialiser -->

                    <a
                        href="/admin/voyageurs"
                        class="bg-slate-600 hover:bg-slate-700 text-white rounded-xl flex items-center justify-center font-semibold">

                        Réinitialiser

                    </a>

                </div>

                <div class="mt-5 text-gray-600">

                    Total :

                    <span class="font-bold text-orange-600">

                        {{ $voyageurs->total() }}

                    </span>

                    utilisateur(s)

                </div>

            </form>

        </div>

        <!-- Tableau -->

        <div class="bg-white rounded-xl shadow overflow-hidden">

            <table class="min-w-full">

                <thead class="bg-slate-100">

                    <tr>

                        <th class="p-4 text-left">
                            Nom
                        </th>

                        <th class="p-4 text-left">
                            Email
                        </th>

                        <th class="p-4 text-center">
                            Rôle
                        </th>

                        <th class="p-4 text-center">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($voyageurs as $voyageur)
                    <tr class="border-t hover:bg-orange-50 transition duration-200">

    <!-- Nom -->

    <td class="p-4 font-semibold text-slate-800">

        {{ $voyageur->name }}

    </td>

    <!-- Email -->

    <td class="p-4 text-gray-700">

        {{ $voyageur->email }}

    </td>

    <!-- Rôle -->

    <td class="p-4 text-center">

        @if($voyageur->role == 'admin')

            <span class="inline-flex items-center px-3 py-1 rounded-full bg-red-100 text-red-700 font-semibold text-sm">

                👑 Administrateur

            </span>

        @else

            <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-700 font-semibold text-sm">

                👤 Utilisateur

            </span>

        @endif

    </td>

    <!-- Actions -->

    <td class="p-4">

        <div class="flex justify-center gap-2">

            <!-- Voir -->

            <a
                href="{{ url('/admin/voyageurs/'.$voyageur->id) }}"
                title="Voir"

                class="w-10 h-10 flex items-center justify-center rounded-lg bg-slate-600 hover:bg-slate-700 text-white">

                👁️

            </a>

            <!-- Supprimer -->

            <form
                action="{{ url('/admin/voyageurs/'.$voyageur->id) }}"
                method="POST">

                @csrf
                @method('DELETE')

                <button
                    type="submit"

                    title="Supprimer"

                    onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?')"

                    class="w-10 h-10 flex items-center justify-center rounded-lg bg-red-600 hover:bg-red-700 text-white">

                    🗑️

                </button>

            </form>

        </div>

    </td>

</tr>

@empty

<tr>

    <td
        colspan="4"
        class="text-center py-12 text-gray-500">

        <div class="text-5xl mb-3">

            👥

        </div>

        <p class="text-lg font-semibold">

            Aucun utilisateur trouvé.

        </p>

        <p class="text-sm text-gray-400 mt-2">

            Modifiez vos filtres ou ajoutez de nouveaux utilisateurs.

        </p>

    </td>

</tr>

@endforelse
</tbody>

</table>

<!-- Pagination -->

<div class="p-6 border-t flex flex-col md:flex-row justify-between items-center gap-4">

    <div class="text-gray-600">

        Affichage de

        <span class="font-semibold">

            {{ $voyageurs->firstItem() ?? 0 }}

        </span>

        à

        <span class="font-semibold">

            {{ $voyageurs->lastItem() ?? 0 }}

        </span>

        sur

        <span class="font-bold text-orange-600">

            {{ $voyageurs->total() }}

        </span>

        utilisateur(s)

    </div>

    <div>

        {{ $voyageurs->links() }}

    </div>

</div>

</div>

</div>

</div>

</x-layouts.admin>