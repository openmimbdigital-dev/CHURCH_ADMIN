document.addEventListener('alpine:init', () => {
    Alpine.data('dualScroll', () => ({
        hasHorizontalScroll: false,
        _observer: null,
        _mutationObserver: null,
        _syncing: false,
        _onNavigate: null,

        init() {
            this.$nextTick(() => {
                this.bindScrollTarget();
                this.resize();
            });

            if (typeof ResizeObserver !== 'undefined' && this.$refs.main) {
                this._observer = new ResizeObserver(() => {
                    this.bindScrollTarget();
                    this.resize();
                });
                this._observer.observe(this.$refs.main);
            }

            if (typeof MutationObserver !== 'undefined' && this.$refs.main) {
                this._mutationObserver = new MutationObserver(() => {
                    this.bindScrollTarget();
                    this.resize();
                });
                this._mutationObserver.observe(this.$refs.main, {
                    childList: true,
                    subtree: true,
                    attributes: true,
                });
            }

            this._onNavigate = () => {
                this.$nextTick(() => {
                    this.bindScrollTarget();
                    this.resize();
                });
            };

            document.addEventListener('livewire:navigated', this._onNavigate);
        },

        destroy() {
            this._observer?.disconnect();
            this._mutationObserver?.disconnect();

            if (this._scrollListener && this._scrollEl) {
                this._scrollEl.removeEventListener('scroll', this._scrollListener);
            }

            if (this._onNavigate) {
                document.removeEventListener('livewire:navigated', this._onNavigate);
            }
        },

        bindScrollTarget() {
            if (this._scrollListener && this._scrollEl) {
                this._scrollEl.removeEventListener('scroll', this._scrollListener);
            }

            this._scrollEl = this.$refs.main;

            if (! this._scrollEl) {
                return;
            }

            this._scrollListener = () => this.syncFromBottom();
            this._scrollEl.addEventListener('scroll', this._scrollListener, { passive: true });
        },

        contentElement() {
            const main = this.$refs.main;

            if (! main) {
                return null;
            }

            return main.querySelector('.table.min-w-full')
                ?? main.querySelector('table')
                ?? main.firstElementChild;
        },

        resize() {
            const main = this.$refs.main;
            const topInner = this.$refs.topInner;
            const content = this.contentElement();

            if (! main || ! topInner || ! content) {
                this.hasHorizontalScroll = false;

                return;
            }

            const scrollWidth = content.scrollWidth;
            topInner.style.width = `${scrollWidth}px`;

            this.hasHorizontalScroll = scrollWidth > main.clientWidth + 1;

            if (this.$refs.top && this.hasHorizontalScroll) {
                this.$refs.top.scrollLeft = main.scrollLeft;
            }
        },

        syncFromTop() {
            if (this._syncing || ! this.$refs.main || ! this.$refs.top) {
                return;
            }

            this._syncing = true;
            this.$refs.main.scrollLeft = this.$refs.top.scrollLeft;
            this._syncing = false;
        },

        syncFromBottom() {
            if (this._syncing || ! this.$refs.main || ! this.$refs.top) {
                return;
            }

            this._syncing = true;

            if (this.hasHorizontalScroll) {
                this.$refs.top.scrollLeft = this.$refs.main.scrollLeft;
            }

            this._syncing = false;
        },
    }));
});
