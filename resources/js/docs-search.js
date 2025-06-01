window.docsSearch = function() {
    return {
        query: '',
        results: [],
        selectedIndex: 0,
        showResults: false,
        loading: false,
        abortController: null,

        async search() {
            if (!this.query || this.query.length < 2) {
                this.results = [];
                this.showResults = false;
                return;
            }

            // Cancel previous request
            if (this.abortController) {
                this.abortController.abort();
            }

            this.loading = true;
            this.showResults = true;
            this.abortController = new AbortController();

            try {
                const response = await fetch(`/api/docs/search?q=${encodeURIComponent(this.query)}`, {
                    signal: this.abortController.signal
                });
                
                if (!response.ok) {
                    throw new Error('Search request failed');
                }

                const data = await response.json();
                this.results = data.results || [];
                this.selectedIndex = 0;

            } catch (error) {
                if (error.name !== 'AbortError') {
                    console.error('Search failed:', error);
                    this.results = [];
                }
            } finally {
                this.loading = false;
                this.abortController = null;
            }
        },

        get flatResults() {
            // Flatten grouped results for keyboard navigation
            const flat = [];
            this.results.forEach(group => {
                group.items.forEach((item) => {
                    flat.push({
                        ...item,
                        globalIndex: flat.length
                    });
                });
            });
            return flat;
        },

        navigateDown() {
            const flatResults = this.flatResults;
            if (flatResults.length > 0) {
                this.selectedIndex = (this.selectedIndex + 1) % flatResults.length;
            }
        },

        navigateUp() {
            const flatResults = this.flatResults;
            if (flatResults.length > 0) {
                this.selectedIndex = this.selectedIndex === 0 ? flatResults.length - 1 : this.selectedIndex - 1;
            }
        },

        selectResult() {
            const flatResults = this.flatResults;
            if (flatResults.length > 0 && flatResults[this.selectedIndex]) {
                window.location.href = flatResults[this.selectedIndex].url;
            }
        },

        onFocus() {
            if (this.query.length >= 2) {
                this.showResults = true;
            }
        },

        highlightMatch(text) {
            if (!text || !this.query) return text;
            
            const textLower = text.toLowerCase();
            const queryLower = this.query.toLowerCase();
            const index = textLower.indexOf(queryLower);
            
            if (index !== -1) {
                const before = text.substring(0, index);
                const match = text.substring(index, index + this.query.length);
                const after = text.substring(index + this.query.length);
                return `${before}<mark class="bg-yellow-200 dark:bg-yellow-800 rounded px-0.5">${match}</mark>${after}`;
            }
            
            return text;
        }
    };
};