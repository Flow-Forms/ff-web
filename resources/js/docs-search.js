import Fuse from 'fuse.js';

window.docsSearch = function() {
    return {
        query: '',
        results: [],
        selectedIndex: 0,
        showResults: false,
        loading: false,
        searchIndex: null,
        fuse: null,

        async init() {
            // Preload search index
            this.loadSearchIndex();
        },

        async loadSearchIndex() {
            if (this.searchIndex) return;

            try {
                const response = await fetch('/docs-search-index.json');
                this.searchIndex = await response.json();
                
                // Initialize Fuse.js with the search index
                this.fuse = new Fuse(this.searchIndex, {
                    keys: [
                        { name: 'title', weight: 0.4 },
                        { name: 'headings', weight: 0.3 },
                        { name: 'content', weight: 0.2 },
                        { name: 'breadcrumb', weight: 0.1 }
                    ],
                    threshold: 0.4,
                    includeScore: true,
                    includeMatches: true,
                    minMatchCharLength: 2,
                    shouldSort: true,
                    findAllMatches: true,
                    ignoreLocation: true
                });
            } catch (error) {
                console.error('Failed to load search index:', error);
            }
        },

        async search() {
            if (!this.query || this.query.length < 2) {
                this.results = [];
                this.showResults = false;
                return;
            }

            this.loading = true;
            this.showResults = true;

            // Ensure index is loaded
            if (!this.fuse) {
                await this.loadSearchIndex();
            }

            // Perform search
            if (this.fuse) {
                this.results = this.fuse.search(this.query).slice(0, 10);
                this.selectedIndex = 0;
            }

            this.loading = false;
        },

        get groupedResults() {
            const groups = {};
            let globalIndex = 0;

            this.results.forEach(result => {
                const section = result.item.section;
                if (!groups[section]) {
                    groups[section] = {
                        section,
                        items: []
                    };
                }
                
                groups[section].items.push({
                    ...result,
                    globalIndex: globalIndex++
                });
            });

            return Object.values(groups);
        },

        navigateDown() {
            if (this.results.length > 0) {
                this.selectedIndex = (this.selectedIndex + 1) % this.results.length;
            }
        },

        navigateUp() {
            if (this.results.length > 0) {
                this.selectedIndex = this.selectedIndex === 0 ? this.results.length - 1 : this.selectedIndex - 1;
            }
        },

        selectResult() {
            if (this.results.length > 0 && this.results[this.selectedIndex]) {
                window.location.href = this.results[this.selectedIndex].item.url;
            }
        },

        onFocus() {
            if (this.query.length >= 2) {
                this.showResults = true;
            }
        },

        highlightMatch(text, matches, fieldName) {
            if (!matches || !text) return text;

            const match = matches.find(m => m.key === fieldName);
            if (!match || !match.indices || match.indices.length === 0) return text;

            let highlighted = '';
            let lastIndex = 0;

            // Sort indices by start position
            const sortedIndices = [...match.indices].sort((a, b) => a[0] - b[0]);

            sortedIndices.forEach(([start, end]) => {
                // Add text before match
                highlighted += text.substring(lastIndex, start);
                // Add highlighted match
                highlighted += `<mark class="bg-yellow-200 dark:bg-yellow-800 rounded px-0.5">${text.substring(start, end + 1)}</mark>`;
                lastIndex = end + 1;
            });

            // Add remaining text
            highlighted += text.substring(lastIndex);

            return highlighted;
        }
    };
};