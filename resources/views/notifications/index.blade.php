{{-- La page est la même pour les deux rôles, mais chacun doit garder son
     menu : l'agent n'a rien à faire dans la navigation administrateur. --}}
@php
    $gabarit = auth()->user()?->role === 'agent'
        ? 'layouts.agent'
        : 'layouts.admin';
@endphp

<x-dynamic-component :component="$gabarit" :header="'Notifications'">

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

                    @php
                        // L'icône dit de quoi il s'agit, la couleur dit s'il
                        // y a lieu de s'inquiéter. Les variables --tk-* sont
                        // les seules à avoir une déclinaison en mode sombre.
                        $icone = match ($notification->type) {
                            'message' => 'fa-comment',
                            'voyage_rappel' => 'fa-calendar-check',
                            'voyage_imminent' => 'fa-bus',
                            'billet_emis' => 'fa-ticket',
                            'reservation_enregistree', 'reservation' => 'fa-bookmark',
                            'reservation_attente' => 'fa-clock',
                            'reservation_derniere_chance' => 'fa-triangle-exclamation',
                            'reservation_expiree' => 'fa-calendar-xmark',
                            'trajet_modifie' => 'fa-rotate',
                            'trajet_supprime' => 'fa-ban',
                            'vente_agence' => 'fa-arrow-trend-up',
                            'paiement' => 'fa-money-bill-wave',
                            default => 'fa-bell',
                        };

                        $teinte = match ($notification->type) {
                            'billet_emis', 'vente_agence' => 'success',
                            'reservation_attente',
                            'reservation_derniere_chance',
                            'trajet_modifie' => 'warning',
                            'reservation_expiree', 'trajet_supprime' => 'danger',
                            default => null,
                        };
                    @endphp

                    @php
                        // Fond pâle, icône saturée. La bordure est ce qui
                        // détache la pastille du fond crème d'une ligne non
                        // lue, qui est la même teinte qu'elle.
                        $fond = $teinte
                            ? "var(--tk-{$teinte}-bg)"
                            : 'var(--tk-orange-tint-bg)';

                        $bord = $teinte
                            ? "var(--tk-{$teinte}-bd)"
                            : 'var(--tk-orange-tint-bd)';

                        $trait = $teinte
                            ? "var(--tk-{$teinte}-fg)"
                            : 'var(--tk-accent-text)';
                    @endphp

                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border"
                        style="background: {{ $fond }}; border-color: {{ $bord }};"
                    >
                        <i
                            class="fa-solid {{ $icone }}"
                            style="color: {{ $trait }};"
                        ></i>
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

</x-dynamic-component>
