<div
    x-data="{
        open: false,
        message: '',
        onOk: null,
        ask(message, onOk) {
            this.message = message;
            this.onOk = onOk;
            this.open = true;
            this.$nextTick(() => this.$refs.ok && this.$refs.ok.focus());
        },
        confirmNow() {
            const cb = this.onOk;
            this.open = false;
            this.onOk = null;
            if (cb) cb();
        },
        cancel() {
            this.open = false;
            this.onOk = null;
        },
    }"
    @tk-confirm.window="ask($event.detail.message, $event.detail.onOk)"
    @keydown.escape.window="open && cancel()"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[110] flex items-center justify-center p-4"
    role="dialog"
    aria-modal="true"
    aria-label="Confirmation"
>

    <div
        class="absolute inset-0 bg-slate-900/50"
        x-transition.opacity
        @click="cancel()"
    ></div>

    <div
        x-transition
        class="
            relative w-full max-w-sm
            rounded-xl border border-slate-200
            bg-white p-5 shadow-xl
        "
    >

        <div class="flex items-start gap-3.5">

            <span
                class="
                    flex h-10 w-10 shrink-0
                    items-center justify-center rounded-full
                    bg-[#FFF3E8] text-brand
                "
            >
                <i class="fa-solid fa-circle-question text-lg"></i>
            </span>


            <div class="min-w-0 flex-1">

                <div class="text-sm font-bold text-navy">
                    Confirmation
                </div>

                <p class="mt-1 text-sm leading-relaxed text-slate-600 break-words"
                   x-text="message"></p>

            </div>

        </div>


        <div class="mt-4 flex justify-end gap-2">

            <button
                type="button"
                @click="cancel()"
                class="tk-btn-ghost h-9 px-4 text-sm"
            >
                Annuler
            </button>

            <button
                type="button"
                x-ref="ok"
                @click="confirmNow()"
                class="tk-btn-navy h-9 px-4 text-sm"
            >
                Confirmer
            </button>

        </div>

    </div>

</div>

<script>

    function tkConfirm(message, onOk) {

        window.dispatchEvent(
            new CustomEvent('tk-confirm', {
                detail: {
                    message: message,
                    onOk: typeof onOk === 'function' ? onOk : null,
                },
            })
        );

    }

</script>
