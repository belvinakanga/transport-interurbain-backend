<x-layouts.admin>

<div class="support-conversation-page">

    {{-- =========================================================
         EN-TÊTE DE LA CONVERSATION
    ========================================================== --}}

    <div class="conversation-top">

        <a
            href="{{ route('admin.support.index') }}"
            class="back-conversations"
        >
            <i class="bi bi-arrow-left"></i>
            <span>Retour aux conversations</span>
        </a>


        <div class="conversation-heading">

            <div class="conversation-heading-left">

                <div class="conversation-icon">
                    <i class="bi bi-chat-dots-fill"></i>
                </div>

                <div>

                    <div class="conversation-title-line">

                        <h1>
                            {{ $conversation->subject }}
                        </h1>

                        @if($conversation->status === 'open')

                            <span class="status-badge status-open">
                                <span class="status-dot"></span>
                                Ouverte
                            </span>

                        @else

                            <span class="status-badge status-closed">
                                <i class="bi bi-lock-fill"></i>
                                Fermée
                            </span>

                        @endif

                    </div>

                    <p class="conversation-subtitle">
                        Conversation avec
                        <strong>
                            {{ $conversation->user->name ?? 'Voyageur' }}
                        </strong>
                    </p>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         CARTE DU VOYAGEUR
    ========================================================== --}}

    <div class="traveler-summary">

        <div class="traveler-summary-left">

            <div class="traveler-avatar-big">
                <i class="bi bi-person-fill"></i>
            </div>

            <div class="traveler-details">

                <span class="traveler-role">
                    VOYAGEUR
                </span>

                <strong class="traveler-name">
                    {{ $conversation->user->name ?? 'Voyageur' }}
                </strong>

                <span class="traveler-email">
                    <i class="bi bi-envelope"></i>
                    {{ $conversation->user->email ?? 'Email inconnu' }}
                </span>

            </div>

        </div>


        <div class="traveler-created">

            <span>
                CONVERSATION CRÉÉE LE
            </span>

            <strong>
                {{ $conversation->created_at->format('d/m/Y à H:i') }}
            </strong>

        </div>

    </div>


    {{-- =========================================================
         GRANDE CARTE DE CONVERSATION
    ========================================================== --}}

    <div class="conversation-card">


        {{-- =====================================================
             HEADER MESSAGES
        ====================================================== --}}

        <div class="conversation-card-header">

            <div class="messages-heading">

                <div class="messages-heading-icon">
                    <i class="bi bi-chat-square-text-fill"></i>
                </div>

                <div>

                    <h2>
                        Messages
                    </h2>

                    <p>
                        Historique de la conversation
                    </p>

                </div>

            </div>


            <div class="messages-counter">

                <i class="bi bi-chat-fill"></i>

                <strong>
                    {{ $conversation->messages->count() }}
                </strong>

                message{{ $conversation->messages->count() > 1 ? 's' : '' }}

            </div>

        </div>


        {{-- =====================================================
             ZONE DES MESSAGES
        ====================================================== --}}

        <div class="messages-area">

            @forelse($conversation->messages as $message)

                @if($message->sender_type === 'admin')

                    {{-- =================================================
                         MESSAGE ADMIN
                    ================================================== --}}

                    <div class="chat-message admin-message">

                        <div class="message-wrapper">

                            <div class="message-author admin-author">

                                <span class="message-avatar admin-avatar">
                                    <i class="bi bi-shield-check"></i>
                                </span>

                                <span class="author-name">
                                    Administration TOKENDÉ
                                </span>

                                <span class="you-label">
                                    Vous
                                </span>

                            </div>


                            <div class="message-bubble admin-bubble">

                                <div class="bubble-text">
                                    {{ $message->message }}
                                </div>

                                <div class="bubble-footer">

                                    <span>
                                        {{ $message->created_at->format('d/m/Y à H:i') }}
                                    </span>

                                    <i class="bi bi-check2-all"></i>

                                </div>

                            </div>

                        </div>

                    </div>

                @else

                    {{-- =================================================
                         MESSAGE VOYAGEUR
                    ================================================== --}}

                    <div class="chat-message traveler-message">

                        <div class="message-wrapper">

                            <div class="message-author traveler-author">

                                <span class="message-avatar traveler-avatar">
                                    <i class="bi bi-person-fill"></i>
                                </span>

                                <span class="author-name">
                                    {{ $message->user->name ?? 'Voyageur' }}
                                </span>

                            </div>


                            <div class="message-bubble traveler-bubble">

                                <div class="bubble-text">
                                    {{ $message->message }}
                                </div>

                                <div class="bubble-footer">

                                    <i class="bi bi-clock"></i>

                                    <span>
                                        {{ $message->created_at->format('d/m/Y à H:i') }}
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                @endif

            @empty

                {{-- =================================================
                     AUCUN MESSAGE
                ================================================== --}}

                <div class="empty-conversation">

                    <div class="empty-conversation-icon">
                        <i class="bi bi-chat-square-text"></i>
                    </div>

                    <h3>
                        Aucun message
                    </h3>

                    <p>
                        Aucun message n'a encore été envoyé.
                    </p>

                </div>

            @endforelse

        </div>


        {{-- =====================================================
             ZONE DE RÉPONSE
        ====================================================== --}}

        @if($conversation->status === 'open')

            <div class="reply-container">

                <div class="reply-title">

                    <div class="reply-title-icon">
                        <i class="bi bi-reply-fill"></i>
                    </div>

                    <div>

                        <h3>
                            Répondre au voyageur
                        </h3>

                        <p>
                            Votre réponse sera ajoutée à la conversation.
                        </p>

                    </div>

                </div>


                <form
                    method="POST"
                    action="{{ route('admin.support.reply', $conversation->id) }}"
                    class="reply-form"
                >

                    @csrf


                    <textarea
                        name="message"
                        rows="4"
                        placeholder="Écrivez votre réponse au voyageur..."
                        required
                    >{{ old('message') }}</textarea>


                    @error('message')

                        <div class="form-error">
                            <i class="bi bi-exclamation-circle"></i>
                            {{ $message }}
                        </div>

                    @enderror


                    <div class="reply-actions">

                        <div class="reply-hint">

                            <i class="bi bi-info-circle"></i>

                            <span>
                                Le voyageur verra votre réponse dans son application.
                            </span>

                        </div>


                        <button
                            type="submit"
                            class="send-button"
                        >

                            <i class="bi bi-send-fill"></i>

                            <span>
                                Envoyer la réponse
                            </span>

                        </button>

                    </div>

                </form>

            </div>

        @else

            {{-- =====================================================
                 CONVERSATION FERMÉE
            ====================================================== --}}

            <div class="closed-container">

                <div class="closed-message">

                    <div class="closed-icon">
                        <i class="bi bi-lock-fill"></i>
                    </div>

                    <div>

                        <h3>
                            Conversation fermée
                        </h3>

                        <p>
                            Cette conversation est actuellement fermée.
                        </p>

                    </div>

                </div>


                <form
                    method="POST"
                    action="{{ route('admin.support.reopen', $conversation->id) }}"
                >

                    @csrf

                    <button
                        type="submit"
                        class="reopen-button"
                    >

                        <i class="bi bi-unlock-fill"></i>

                        Réouvrir la conversation

                    </button>

                </form>

            </div>

        @endif

    </div>


    {{-- =========================================================
         ACTION FERMER
    ========================================================== --}}

    @if($conversation->status === 'open')

        <div class="conversation-close">

            <div class="close-information">

                <div class="close-icon">
                    <i class="bi bi-lock"></i>
                </div>

                <div>

                    <strong>
                        Terminer cette conversation ?
                    </strong>

                    <span>
                        Vous pourrez la rouvrir ultérieurement.
                    </span>

                </div>

            </div>


            <form
                method="POST"
                action="{{ route('admin.support.close', $conversation->id) }}"
            >

                @csrf

                <button
                    type="submit"
                    class="close-button"
                    onclick="return confirm('Voulez-vous vraiment fermer cette conversation ?');"
                >

                    <i class="bi bi-lock-fill"></i>

                    Fermer la conversation

                </button>

            </form>

        </div>

    @endif

</div>


<style>
/* =========================================================
   TOKENDÉ — PAGE CONVERSATION
========================================================= */

.support-conversation-page {
    --tokende-blue: #0A2A66;
    --tokende-blue-dark: #071E4B;
    --tokende-orange: #EE5807;
    --tokende-orange-dark: #D94D03;
    --tokende-orange-light: #FFF1E9;
    --tokende-background: #F6F8FC;
    --tokende-border: #DDE6F2;
    --tokende-text: #263247;
    --tokende-muted: #8791A1;

    width: 100%;
    min-height: calc(100vh - 120px);
    padding: 28px 34px 50px;
    box-sizing: border-box;
    background: linear-gradient(180deg, #F8FAFD 0%, #F4F7FB 100%);
}

/* Retour */
.conversation-top { margin-bottom: 22px; }

.back-conversations {
    display: inline-flex;
    align-items: center;
    gap: 9px;
    padding: 10px 16px;
    background: #FFFFFF;
    color: var(--tokende-blue);
    border: 1px solid var(--tokende-border);
    border-radius: 11px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 800;
    box-shadow: 0 4px 12px rgba(10,42,102,.04);
    transition: all .2s ease;
}

.back-conversations i { color: var(--tokende-orange); font-size: 15px; }

.back-conversations:hover {
    color: #FFFFFF;
    background: var(--tokende-orange);
    border-color: var(--tokende-orange);
    transform: translateX(-2px);
    box-shadow: 0 7px 18px rgba(238,88,7,.20);
}

.back-conversations:hover i { color: #FFFFFF; }

/* Titre */
.conversation-heading { margin-top: 20px; }

.conversation-heading-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.conversation-icon {
    width: 54px;
    height: 54px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #FFFFFF;
    background: linear-gradient(135deg, #EE5807, #FF7628);
    border-radius: 15px;
    font-size: 22px;
    box-shadow: 0 8px 20px rgba(238,88,7,.20);
}

.conversation-title-line {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.conversation-title-line h1 {
    margin: 0;
    color: var(--tokende-blue);
    font-size: 25px;
    font-weight: 850;
}

.conversation-subtitle {
    margin: 5px 0 0;
    color: var(--tokende-muted);
    font-size: 13px;
}

.conversation-subtitle strong { color: var(--tokende-blue); }

/* Statut */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 850;
}

.status-open {
    color: var(--tokende-orange);
    background: var(--tokende-orange-light);
    border: 1px solid #FFD6C1;
}

.status-closed {
    color: #667080;
    background: #EEF1F5;
    border: 1px solid #E0E4EA;
}

.status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--tokende-orange);
}

/* Carte voyageur */
.traveler-summary {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 25px;
    padding: 17px 20px 17px 23px;
    margin-bottom: 18px;
    background: #FFFFFF;
    border: 1px solid var(--tokende-border);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 5px 18px rgba(10,42,102,.045);
}

.traveler-summary::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: var(--tokende-orange);
}

.traveler-summary-left {
    display: flex;
    align-items: center;
    gap: 13px;
}

.traveler-avatar-big {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: var(--tokende-orange-light);
    color: var(--tokende-orange);
    border: 1px solid #FFD5C1;
    border-radius: 13px;
    font-size: 20px;
}

.traveler-details {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.traveler-role {
    color: var(--tokende-orange);
    font-size: 9px;
    font-weight: 900;
    letter-spacing: .8px;
}

.traveler-name {
    color: var(--tokende-blue);
    font-size: 16px;
    font-weight: 850;
}

.traveler-email {
    display: flex;
    align-items: center;
    gap: 5px;
    color: var(--tokende-muted);
    font-size: 11px;
}

.traveler-email i { color: var(--tokende-orange); }

.traveler-created {
    display: flex;
    flex-direction: column;
    gap: 3px;
    text-align: right;
}

.traveler-created span {
    color: #A0A8B2;
    font-size: 8px;
    font-weight: 900;
    letter-spacing: .7px;
}

.traveler-created strong {
    color: var(--tokende-blue);
    font-size: 11px;
}

/* Carte principale */
.conversation-card {
    background: #FFFFFF;
    border: 1px solid var(--tokende-border);
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 8px 28px rgba(10,42,102,.055);
}

/* Header messages */
.conversation-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: 17px 22px;
    border-bottom: 1px solid #E7EDF5;
    background: #FFFFFF;
}

.messages-heading {
    display: flex;
    align-items: center;
    gap: 10px;
}

.messages-heading-icon {
    width: 37px;
    height: 37px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--tokende-orange);
    background: var(--tokende-orange-light);
    border-radius: 10px;
    font-size: 15px;
}

.messages-heading h2 {
    margin: 0;
    color: var(--tokende-blue);
    font-size: 16px;
    font-weight: 850;
}

.messages-heading p {
    margin: 2px 0 0;
    color: var(--tokende-muted);
    font-size: 11px;
}

.messages-counter {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 12px;
    color: var(--tokende-orange);
    background: var(--tokende-orange-light);
    border: 1px solid #FFD7C4;
    border-radius: 20px;
    font-size: 11px;
    white-space: nowrap;
}

/* Zone messages */
.messages-area {
    max-height: 570px;
    overflow-y: auto;
    padding: 25px 28px;
    background: linear-gradient(180deg, #FBFCFE 0%, #F6F8FB 100%);
}

/* Ligne message */
.chat-message {
    display: flex;
    width: 100%;
    margin-bottom: 20px;
    box-sizing: border-box;
}

.chat-message:last-child { margin-bottom: 0; }

.traveler-message {
    justify-content: flex-start !important;
    text-align: left !important;
}

.admin-message {
    justify-content: flex-end !important;
    text-align: right !important;
}

/* Conteneur : la largeur suit la bulle */
.message-wrapper {
    display: flex !important;
    flex-direction: column !important;
    width: fit-content !important;
    max-width: 58% !important;
    min-width: 0 !important;
    box-sizing: border-box !important;
}

.traveler-message .message-wrapper {
    align-items: flex-start !important;
}

.admin-message .message-wrapper {
    align-items: flex-end !important;
}

/* Auteur */
.message-author {
    display: flex;
    align-items: center;
    gap: 7px;
    margin-bottom: 7px;
    color: #596476;
    font-size: 11px;
    font-weight: 750;
    white-space: nowrap;
}

.traveler-author { justify-content: flex-start; }
.admin-author { justify-content: flex-end; }

.message-avatar {
    width: 27px;
    height: 27px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border-radius: 9px;
    font-size: 11px;
}

.admin-avatar {
    color: var(--tokende-blue);
    background: #EAF2FF;
}

.traveler-avatar {
    color: var(--tokende-orange);
    background: var(--tokende-orange-light);
}

.author-name { color: #596476; }

.you-label {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 3px 7px;
    color: var(--tokende-orange);
    background: var(--tokende-orange-light);
    border: 1px solid #FFD7C4;
    border-radius: 10px;
    font-size: 9px;
    font-weight: 900;
}

/* Bulles */
.message-bubble {
    display: block !important;
    width: fit-content !important;
    max-width: 100% !important;
    min-width: 0 !important;
    height: auto !important;
    min-height: 0 !important;
    padding: 12px 15px !important;
    box-sizing: border-box !important;
    border-radius: 16px;
    text-align: left !important;
    overflow-wrap: anywhere !important;
    word-break: normal !important;
}

/* Voyageur : blanc à gauche */
.traveler-bubble {
    align-self: flex-start !important;
    color: #273142;
    background: #FFFFFF;
    border: 1px solid #DCE5F0;
    border-left: 3px solid var(--tokende-orange);
    border-bottom-left-radius: 5px;
    box-shadow: 0 4px 12px rgba(10,42,102,.035);
}

/* Admin : orange à droite */
.admin-bubble {
    align-self: flex-end !important;
    color: #FFFFFF;
    background: linear-gradient(135deg, #EE5807 0%, #F66D20 100%);
    border: none;
    border-bottom-right-radius: 5px;
    box-shadow: 0 7px 18px rgba(238,88,7,.18);
    text-align: left !important;
}

/* IMPORTANT : ton HTML utilise .bubble-text */
.bubble-text {
    display: block !important;
    width: auto !important;
    max-width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    color: inherit !important;
    font-size: 14px !important;
    line-height: 1.5 !important;
    text-align: left !important;
    text-align-last: left !important;
    white-space: pre-wrap !important;
    overflow-wrap: anywhere !important;
    word-break: normal !important;
}

/* Date */
.bubble-footer {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 8px;
    font-size: 9px;
    line-height: 1;
}

.admin-bubble .bubble-footer {
    justify-content: flex-end !important;
    color: rgba(255,255,255,.72);
}

.traveler-bubble .bubble-footer {
    justify-content: flex-start !important;
    color: #9AA3AF;
}

/* Petits messages : merci / ok / bonsoir */
.admin-bubble,
.traveler-bubble {
    width: fit-content !important;
    height: auto !important;
    min-height: 0 !important;
    max-height: none !important;
}

/* Longs messages */
.admin-bubble .bubble-text,
.traveler-bubble .bubble-text {
    max-width: 100% !important;
    overflow-wrap: anywhere !important;
    word-break: normal !important;
}

/* Aucun message */
.empty-conversation {
    padding: 70px 20px;
    text-align: center;
}

.empty-conversation-icon {
    width: 62px;
    height: 62px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    color: var(--tokende-orange);
    background: var(--tokende-orange-light);
    border: 1px solid #FFD8C5;
    border-radius: 17px;
    font-size: 24px;
}

.empty-conversation h3 {
    margin: 14px 0 5px;
    color: var(--tokende-blue);
    font-size: 16px;
}

.empty-conversation p {
    margin: 0;
    color: var(--tokende-muted);
    font-size: 12px;
}

/* Réponse */
.reply-container {
    padding: 22px 24px;
    background: #FFFFFF;
    border-top: 1px solid #E7EDF5;
}

.reply-title {
    display: flex;
    align-items: center;
    gap: 11px;
    margin-bottom: 15px;
}

.reply-title-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #FFFFFF;
    background: linear-gradient(135deg, #EE5807, #FF7628);
    border-radius: 11px;
    font-size: 16px;
    box-shadow: 0 5px 13px rgba(238,88,7,.18);
}

.reply-title h3 {
    margin: 0;
    color: var(--tokende-blue);
    font-size: 15px;
    font-weight: 850;
}

.reply-title p {
    margin: 2px 0 0;
    color: var(--tokende-muted);
    font-size: 11px;
}

.reply-form textarea {
    display: block;
    width: 100%;
    min-height: 105px;
    padding: 13px 15px;
    box-sizing: border-box;
    resize: vertical;
    outline: none;
    color: var(--tokende-text);
    background: #F9FBFE;
    border: 1.5px solid #D9E3EF;
    border-radius: 12px;
    font-family: inherit;
    font-size: 13px;
    line-height: 1.5;
    transition: all .2s ease;
}

.reply-form textarea::placeholder { color: #A7AFBB; }

.reply-form textarea:focus {
    background: #FFFFFF;
    border-color: var(--tokende-orange);
    box-shadow: 0 0 0 3px rgba(238,88,7,.09);
}

.form-error {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 7px;
    color: #DC3545;
    font-size: 11px;
}

.reply-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    margin-top: 12px;
}

.reply-hint {
    display: flex;
    align-items: center;
    gap: 6px;
    color: var(--tokende-muted);
    font-size: 10.5px;
}

.reply-hint i {
    color: var(--tokende-orange);
    font-size: 12px;
}

.send-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 19px;
    color: #FFFFFF;
    background: linear-gradient(135deg, #EE5807, #FF7628);
    border: none;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 850;
    cursor: pointer;
    box-shadow: 0 6px 15px rgba(238,88,7,.22);
    transition: all .2s ease;
}

.send-button:hover {
    background: linear-gradient(135deg, #D94D03, #EE5807);
    transform: translateY(-2px);
    box-shadow: 0 9px 19px rgba(238,88,7,.28);
}

.send-button:active { transform: translateY(0); }

/* Conversation fermée */
.closed-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: 20px 24px;
    background: #FBFCFE;
    border-top: 1px solid #E7EDF5;
}

.closed-message {
    display: flex;
    align-items: center;
    gap: 11px;
}

.closed-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6E7784;
    background: #EEF1F5;
    border-radius: 11px;
}

.closed-message h3 {
    margin: 0;
    color: var(--tokende-blue);
    font-size: 14px;
}

.closed-message p {
    margin: 2px 0 0;
    color: var(--tokende-muted);
    font-size: 11px;
}

.reopen-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 11px 16px;
    color: #FFFFFF;
    background: var(--tokende-orange);
    border: none;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 850;
    cursor: pointer;
    box-shadow: 0 5px 13px rgba(238,88,7,.18);
    transition: all .2s ease;
}

.reopen-button:hover {
    background: var(--tokende-orange-dark);
    transform: translateY(-1px);
}

/* Fermer */
.conversation-close {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-top: 16px;
    padding: 14px 17px;
    background: #FFFFFF;
    border: 1px solid var(--tokende-border);
    border-radius: 13px;
}

.close-information {
    display: flex;
    align-items: center;
    gap: 9px;
}

.close-icon {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--tokende-orange);
    background: var(--tokende-orange-light);
    border: 1px solid #FFD7C4;
    border-radius: 9px;
}

.close-information strong {
    display: block;
    color: var(--tokende-blue);
    font-size: 11px;
}

.close-information span {
    display: block;
    margin-top: 2px;
    color: var(--tokende-muted);
    font-size: 10px;
}

.close-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 9px 14px;
    color: var(--tokende-orange);
    background: #FFF7F3;
    border: 1px solid #FFD4C0;
    border-radius: 9px;
    font-size: 11px;
    font-weight: 850;
    cursor: pointer;
    transition: all .2s ease;
}

.close-button:hover {
    color: #FFFFFF;
    background: var(--tokende-orange);
    border-color: var(--tokende-orange);
}

/* Scrollbar */
.messages-area::-webkit-scrollbar { width: 7px; }

.messages-area::-webkit-scrollbar-track {
    background: #F0F3F7;
}

.messages-area::-webkit-scrollbar-thumb {
    background: #F3A078;
    border-radius: 10px;
}

.messages-area::-webkit-scrollbar-thumb:hover {
    background: var(--tokende-orange);
}

/* Responsive */
@media (max-width: 900px) {

    .support-conversation-page {
        padding: 22px 18px 40px;
    }

    .traveler-summary {
        align-items: flex-start;
        flex-direction: column;
    }

    .traveler-created {
        text-align: left;
    }

    .message-wrapper {
        max-width: 75% !important;
    }
}

@media (max-width: 650px) {

    .support-conversation-page {
        padding: 18px 12px 35px;
    }

    .conversation-heading-left {
        align-items: flex-start;
    }

    .conversation-icon {
        width: 46px;
        height: 46px;
        font-size: 19px;
    }

    .conversation-title-line h1 {
        font-size: 20px;
    }

    .conversation-subtitle {
        font-size: 11px;
    }

    .traveler-summary {
        padding: 15px;
    }

    .conversation-card-header {
        padding: 15px;
    }

    .messages-area {
        padding: 20px 14px;
    }

    .message-wrapper {
        max-width: 86% !important;
    }

    .message-bubble {
        max-width: 100% !important;
        padding: 11px 14px !important;
    }

    .bubble-text {
        font-size: 13px !important;
    }

    .reply-container {
        padding: 17px;
    }

    .reply-actions {
        align-items: stretch;
        flex-direction: column;
    }

    .reply-hint {
        align-items: flex-start;
    }

    .send-button {
        width: 100%;
    }

    .closed-container {
        align-items: stretch;
        flex-direction: column;
    }

    .reopen-button {
        width: 100%;
    }

    .conversation-close {
        align-items: stretch;
        flex-direction: column;
    }

    .close-button {
        width: 100%;
    }
}
</style>

</x-layouts.admin>