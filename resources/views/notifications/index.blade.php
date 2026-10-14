<x-layouts.admin :header="'Notifications'">

    <div class="tk-page">

        <div class="tk-page-head">

            <div>
                <h1 class="tk-page-title">Notifications</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Messages des voyageurs et suivi de votre activité.
                </p>
            </div>

            @if($notifications->total() > 0)
                <form method="POST" action="{{ route('notifications.tout-lu') }}">
                    @csrf
                    <button type="submit" class="tk-btn tk-btn-ghost">
                        <i class="fa-solid fa-check-double"></i>
                        Tout marquer comme lu
                    </button>
                </form>
            @endif

        </div>

        <div class="tk-card overflow-hidden">

            @forelse($notifications as $notification)

                <a
                    href="{{ route('notifications.ouvrir', $notification->id) }}"
                    class="flex gap-4 border-b border-slate-100 px-5 py-4 transition-colors hover:bg-slate-50 last:border-0 dark:border-transparent"
                    @unless($notification->lue)
                        style="background: var(--tk-orange-tint-bg);"
                    @endunless
                >

                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl"
                        style="background: var(--tk-stat-accent-bg);"
                    >
                        <i class="fa-solid {{
                            $notification->type === 'message'
                                ? 'fa-comment'
                                : ($notification->type === 'reservation'
                                    ? 'fa-ticket'
                                    : 'fa-bell')
                        }} text-brand"></i>
                    </div>

                    <div class="min-w-0 flex-1">

                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-navy">
                                {{ $notification->titre }}
                            </span>

                            @unless($notification->lue)
                                <span class="h-2 w-2 rounded-full bg-brand"></span>
                            @endunless
                        </div>

                        <div class="mt-0.5 text-sm text-slate-500">
                            {{ $notification->corps }}
                        </div>

                        <div class="mt-1 text-xs text-slate-400">
                            {{ $notification->created_at?->diffForHumans() }}
                        </div>

                    </div>

                </a>

            @empty

                <div class="tk-empty">
                    Aucune notification pour le moment.
                </div>

            @endforelse

        </div>

        @if($notifications->hasPages())
            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        @endif

    </div>

</x-layouts.admin>
