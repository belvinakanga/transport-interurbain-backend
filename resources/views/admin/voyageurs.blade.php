<x-layouts.admin :header="'Gestion des utilisateurs'">

<div class="tk-page">

    {{-- En-tête --}}

    <div
        class="
            tk-page-head
            flex flex-col gap-4
            md:flex-row md:items-center
            md:justify-between
        "
    >

        <h1 class="tk-page-title">

            <span
                class="
                    flex h-11 w-11 shrink-0
                    items-center justify-center
                    rounded-lg bg-orange-50
                    text-lg text-brand
                "
            >
                <i class="fa-solid fa-users"></i>
            </span>

            Gestion des utilisateurs

        </h1>

        <a
            href="{{ route('voyageurs.create') }}"
            class="tk-btn-accent shrink-0"
        >

            <i class="fa-solid fa-plus"></i>

            <span>Ajouter un utilisateur</span>

        </a>

    </div>


    {{-- Barre de recherche et filtres --}}

    <div class="tk-card p-6">

        <form action="/admin/voyageurs" method="GET">

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                {{-- Recherche --}}

                <div>

                    <input
                        type="text"
                        name="recherche"
                        value="{{ request('recherche') }}"
                        placeholder="Rechercher un utilisateur..."
                        class="tk-input">

                </div>


                {{-- Rôle --}}

                <div>

                    <select
                        name="role"
                        class="tk-input">

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

                </div>


                {{-- Pagination --}}

                <div>

                    <select
                        name="par_page"
                        class="tk-input">

                        @foreach([10,25,50,100] as $nb)

                            <option
                                value="{{ $nb }}"
                                {{ request('par_page',10) == $nb ? 'selected' : '' }}>

                                {{ $nb }} lignes

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Filtrer --}}

                <button
                    type="submit"
                    class="tk-btn-accent"
                >

                    <i class="fa-solid fa-search"></i>

                    Filtrer

                </button>


                {{-- Réinitialiser --}}

                <a
                    href="/admin/voyageurs"
                    class="tk-btn-ghost"
                >
                    Réinitialiser
                </a>

            </div>


            {{-- Informations --}}

            <div class="mt-5 text-sm text-slate-500">

                Total :

                <span class="font-bold text-brand">

                    {{ $voyageurs->total() }}

                </span>

                utilisateur(s)

            </div>

        </form>

    </div>


    {{-- Tableau des utilisateurs --}}

    <div class="tk-card overflow-hidden">

        <div class="overflow-x-auto">

            <table class="tk-table">

                {{-- EN-TÊTE --}}

                <thead>

                    <tr>

                        <th class="text-left whitespace-nowrap">
                            <i class="fa-solid fa-user"></i>
                            Utilisateur
                        </th>

                        <th class="text-left whitespace-nowrap">
                            Email
                        </th>

                        <th class="text-center whitespace-nowrap">
                            Rôle
                        </th>

                        <th class="text-center whitespace-nowrap">
                            Actions
                        </th>

                    </tr>

                </thead>


                {{-- CORPS DU TABLEAU --}}

                <tbody>

                    @forelse($voyageurs as $voyageur)

                        <tr>


                            {{-- NOM --}}

                            <td class="font-semibold whitespace-nowrap">

                                {{ $voyageur->name }}

                            </td>


                            {{-- EMAIL --}}

                            <td class="whitespace-nowrap">

                                {{ $voyageur->email }}

                            </td>


                            {{-- RÔLE --}}

                            <td class="text-center">

                                @if($voyageur->role == 'admin')

                                    <span class="tk-badge tk-badge-red">

                                        <i class="fa-solid fa-crown"></i>

                                        Administrateur

                                    </span>

                                @elseif($voyageur->role == 'agent')

                                    <span class="tk-badge tk-badge-navy">

                                        <i class="fa-solid fa-user-tie"></i>

                                        Agent

                                    </span>

                                @else

                                    <span class="tk-badge tk-badge-green">

                                        <i class="fa-solid fa-user"></i>

                                        Voyageur

                                    </span>

                                @endif

                            </td>


                            {{-- ACTIONS --}}

                            <td class="text-center whitespace-nowrap">

                                <div class="flex justify-center items-center gap-2">

                                    {{-- VOIR --}}

                                    <a
                                        href="{{ url('/admin/voyageurs/'.$voyageur->id) }}"
                                        title="Voir"
                                        class="tk-icon-btn tk-icon-btn-navy"
                                    >

                                        <i class="fa-solid fa-eye"></i>

                                    </a>


                                    {{-- MODIFIER --}}

                                    <a
                                        href="{{ url('/admin/voyageurs/'.$voyageur->id.'/edit') }}"
                                        title="Modifier"
                                        class="tk-icon-btn tk-icon-btn-brand"
                                    >

                                        <i class="fa-solid fa-pen"></i>

                                    </a>


                                    {{-- SUPPRIMER --}}

                                    <form
                                        action="{{ url('/admin/voyageurs/'.$voyageur->id) }}"
                                        method="POST"
                                        class="m-0"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            title="Supprimer"
                                            onclick="event.preventDefault(); tkConfirm('Voulez-vous vraiment supprimer cet utilisateur ?', () => this.form.submit())"
                                            class="tk-icon-btn tk-icon-btn-red"
                                        >

                                            <i class="fa-solid fa-trash"></i>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="4"
                                class="tk-empty"
                            >

                                <div class="mb-3">

                                    <i class="fa-solid fa-users text-4xl"></i>

                                </div>

                                <p class="text-lg font-semibold">

                                    Aucun utilisateur trouvé.

                                </p>

                                <p class="mt-2 text-sm text-slate-400">

                                    Modifiez vos filtres ou ajoutez de nouveaux utilisateurs.

                                </p>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}

        <div
            class="
                border-t border-slate-200
                px-6 py-4
                flex flex-col md:flex-row
                md:items-center md:justify-between
                gap-4
            "
        >

            <div class="text-sm text-slate-500">

                Affichage de

                <span class="font-semibold">

                    {{ $voyageurs->firstItem() ?? 0 }}

                </span>

                à

                <span class="font-semibold">

                    {{ $voyageurs->lastItem() ?? 0 }}

                </span>

                sur

                <span class="font-bold text-brand">

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

</x-layouts.admin>
