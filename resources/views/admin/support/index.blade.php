<x-layouts.admin :header="'Support'">

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

        <div>

            <h1 class="tk-page-title">

                <span
                    class="
                        flex h-11 w-11 shrink-0
                        items-center justify-center
                        rounded-lg bg-orange-50
                        text-lg text-brand
                    "
                >
                    <i class="fa-solid fa-headset"></i>
                </span>

                Support

            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Gérez les conversations avec les voyageurs.
            </p>

        </div>


        {{-- Nombre de conversations --}}

        <span class="tk-badge tk-badge-navy shrink-0">

            {{ $conversations->total() }}
            {{ $conversations->total() > 1 ? 'conversations' : 'conversation' }}

        </span>

    </div>


    {{-- Liste des conversations --}}

    <div class="tk-card overflow-hidden">

        <div>

            @forelse($conversations as $conversation)

                @php
                    $lastMessage = $conversation->messages->first();
                    $isUnread = $conversation->unread_messages_count > 0;
                @endphp


                <a
                    href="{{ route('admin.support.show', $conversation->id) }}"
                    class="
                        flex items-center gap-4
                        px-6 py-5
                        border-b border-slate-100
                        last:border-b-0
                        transition-colors hover:bg-slate-50
                        {{ $isUnread ? 'bg-orange-50' : '' }}
                    "
                >

                    {{-- AVATAR --}}

                    <span
                        class="
                            flex h-11 w-11 shrink-0
                            items-center justify-center
                            rounded-lg bg-[#EEF4FF]
                            text-lg text-navy
                        "
                    >
                        <i class="fa-solid fa-user"></i>
                    </span>


                    {{-- CONTENU --}}

                    <div class="min-w-0 flex-1">

                        <div
                            class="
                                flex items-start
                                justify-between gap-4
                            "
                        >

                            <div class="min-w-0">

                                <p class="truncate text-sm font-bold text-navy">
                                    {{ $conversation->user->name ?? 'Voyageur' }}
                                </p>

                                <p class="mt-0.5 truncate text-xs text-slate-500">
                                    {{ $conversation->user->email ?? 'Email inconnu' }}
                                </p>

                            </div>


                            <div
                                class="
                                    flex shrink-0 flex-col
                                    items-end gap-1.5
                                "
                            >

                                <span class="text-xs text-slate-400 tabular-nums">
                                    {{ $conversation->updated_at->format('d/m/Y à H:i') }}
                                </span>

                                @if($conversation->unread_messages_count > 0)

                                    <span class="tk-badge tk-badge-orange">

                                        <i class="fa-solid fa-comment"></i>

                                        {{ $conversation->unread_messages_count }}
                                        nouveau{{ $conversation->unread_messages_count > 1 ? 'x' : '' }}
                                        message{{ $conversation->unread_messages_count > 1 ? 's' : '' }}

                                    </span>

                                @endif

                            </div>

                        </div>


                        {{-- SUJET --}}

                        <p class="mt-2 truncate text-sm font-semibold text-slate-800">
                            {{ $conversation->subject }}
                        </p>


                        {{-- DERNIER MESSAGE --}}

                        <p class="mt-1 truncate text-xs text-slate-500">

                            @if($lastMessage)

                                {{ $lastMessage->message }}

                            @else

                                Aucun message.

                            @endif

                        </p>

                    </div>


                    {{-- FLÈCHE --}}

                    <i class="fa-solid fa-chevron-right shrink-0 text-slate-300"></i>

                </a>

            @empty

                <div class="tk-empty">

                    <i class="fa-solid fa-inbox mb-3 block text-3xl text-slate-300"></i>

                    <h5 class="text-sm font-bold text-navy">
                        Aucune conversation
                    </h5>

                    <p class="mt-1">
                        Les conversations envoyées par les voyageurs apparaîtront ici.
                    </p>

                </div>

            @endforelse

        </div>


        {{-- PAGINATION --}}

        @if($conversations->hasPages())

            <div class="border-t border-slate-200 px-6 py-4">

                {{ $conversations->links() }}

            </div>

        @endif

    </div>

</div>

</x-layouts.admin>
