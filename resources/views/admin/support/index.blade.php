<x-layouts.admin> 

@section('content')
<div class="container-fluid py-4">

    {{-- En-tête --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                Support
            </h2>
            <p class="text-muted mb-0">
                Gérez les conversations avec les voyageurs.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary rounded-pill px-3 py-2">
                {{ $conversations->total() }}
                {{ $conversations->total() > 1 ? 'conversations' : 'conversation' }}
            </span>
        </div>
    </div>

    {{-- Messages de session --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Fermer"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Fermer"></button>
        </div>
    @endif

    {{-- Liste des conversations --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">

            @forelse($conversations as $conversation)

    @php
        $lastMessage = $conversation->messages->first();
        $isUnread = $conversation->unread_messages_count > 0;
    @endphp

    <a href="{{ route('admin.support.show', $conversation->id) }}"
       class="text-decoration-none text-dark">

        <div class="support-card
                    {{ $isUnread ? 'support-card-unread' : '' }}">

            {{-- Petite barre orange --}}
            <div class="support-accent"></div>

            <div class="support-card-content">

                {{-- Avatar --}}
                <div class="support-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>

                {{-- Contenu --}}
                <div class="support-main">

                    <div class="support-top">

                        <div>
                            <h5 class="support-name">
                                {{ $conversation->user->name ?? 'Voyageur' }}
                            </h5>

                            <div class="support-email">
                                {{ $conversation->user->email ?? 'Email inconnu' }}
                            </div>
                        </div>

                        <div class="support-meta">

                            <div class="support-date">
                                {{ $conversation->updated_at->format('d/m/Y à H:i') }}
                            </div>

                            @if($conversation->unread_messages_count > 0)

                                <span class="support-unread">
                                    <i class="bi bi-chat-fill me-1"></i>
                                    {{ $conversation->unread_messages_count }}
                                    nouveau{{ $conversation->unread_messages_count > 1 ? 'x' : '' }}
                                    message{{ $conversation->unread_messages_count > 1 ? 's' : '' }}
                                </span>

                            @endif

                        </div>

                    </div>


                    {{-- Sujet --}}
                    <div class="support-subject">
                        {{ $conversation->subject }}
                    </div>


                    {{-- Dernier message --}}
                    @if($lastMessage)

                        <div class="support-last-message">
                            {{ $lastMessage->message }}
                        </div>

                    @else

                        <div class="support-last-message">
                            Aucun message.
                        </div>

                    @endif

                </div>


                {{-- Flèche --}}
                <div class="support-arrow">
                    <i class="bi bi-chevron-right"></i>
                </div>

            </div>

        </div>

    </a>

@empty

    <div class="support-empty">

        <div class="support-empty-icon">
            <i class="bi bi-chat-square-text"></i>
        </div>

        <h5>
            Aucune conversation
        </h5>

        <p>
            Les conversations envoyées par les voyageurs apparaîtront ici.
        </p>

    </div>

@endforelse

        </div>
    </div>

    {{-- Pagination --}}
    @if($conversations->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $conversations->links() }}
        </div>
    @endif

</div>

<style>
    .support-conversation {
        transition: background-color 0.2s ease;
    }

    .support-conversation:hover {
        background-color: #f8f9fa;
    }
</style>

<style>

    .support-card {
        position: relative;
        display: flex;
        margin: 14px 20px;
        background: #FFFFFF;
        border: 1px solid #D9E4F5;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 14px rgba(10, 42, 102, 0.06);
        transition: all .2s ease;
    }

    .support-card:hover {
        border-color: #0A2A66;
        box-shadow: 0 8px 22px rgba(10, 42, 102, 0.12);
        transform: translateY(-2px);
    }

    .support-card-unread {
        border-color: #0A2A66;
        box-shadow: 0 5px 18px rgba(10, 42, 102, 0.10);
    }

    .support-accent {
        width: 5px;
        background: #FF6B00;
        flex-shrink: 0;
    }

    .support-card-content {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 20px 22px;
    }

    .support-avatar {
        width: 52px;
        height: 52px;
        min-width: 52px;
        border-radius: 50%;
        background: #EAF2FF;
        color: #0A2A66;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 21px;
    }

    .support-main {
        flex: 1;
        min-width: 0;
    }

    .support-top {
        display: flex;
        justify-content: space-between;
        gap: 20px;
    }

    .support-name {
        margin: 0;
        color: #0A2A66;
        font-size: 17px;
        font-weight: 800;
    }

    .support-email {
        margin-top: 3px;
        color: #6B7280;
        font-size: 13px;
    }

    .support-meta {
        text-align: right;
        flex-shrink: 0;
    }

    .support-date {
        color: #9CA3AF;
        font-size: 12px;
        margin-bottom: 6px;
    }

    .support-unread {
        display: inline-flex;
        align-items: center;
        background: #FFF1E8;
        color: #FF6B00;
        border-radius: 20px;
        padding: 5px 10px;
        font-size: 11px;
        font-weight: 700;
    }

    .support-subject {
        margin-top: 13px;
        color: #1F2937;
        font-size: 15px;
        font-weight: 800;
    }

    .support-last-message {
        margin-top: 5px;
        color: #6B7280;
        font-size: 13px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 750px;
    }

    .support-arrow {
        display: flex;
        align-items: center;
        color: #0A2A66;
        font-size: 18px;
        padding-left: 10px;
    }

    .support-empty {
        text-align: center;
        padding: 70px 20px;
    }

    .support-empty-icon {
        width: 70px;
        height: 70px;
        margin: 0 auto 18px;
        border-radius: 50%;
        background: #EAF2FF;
        color: #0A2A66;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
    }

    .support-empty h5 {
        color: #0A2A66;
        font-weight: 800;
    }

    .support-empty p {
        color: #6B7280;
    }

    @media (max-width: 768px) {

        .support-card {
            margin: 10px;
        }

        .support-card-content {
            padding: 16px;
        }

        .support-top {
            display: block;
        }

        .support-meta {
            text-align: left;
            margin-top: 6px;
        }

        .support-last-message {
            max-width: 100%;
        }

        .support-avatar {
            width: 44px;
            height: 44px;
            min-width: 44px;
        }

    }

</style>
</x-layouts.admin>