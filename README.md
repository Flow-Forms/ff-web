# Documentation Organization

This directory contains the markdown documentation files for Flow Forms.

## File Naming Convention

Files use a numeric prefix to control their display order in the navigation:

- `01-quick-start.md` - Displays as "Quick Start" (first in navigation)
- `02-security.md` - Displays as "Security" (second in navigation)
- `03-terms.md` - Displays as "Terms" (third in navigation)
- `04-policy.md` - Displays as "Policy" (fourth in navigation)

### Folder Structure

Folders can contain their own ordered markdown files:

```
forms/
  01-overview.md    - Displays as "Overview" (first in Forms section)
  02-field-types.md - Displays as "Field Types" (second in Forms section)
```

## Adding New Documentation

1. Create a new markdown file with a numeric prefix (e.g., `05-new-feature.md`)
2. The numeric prefix controls the order but is not shown in the navigation
3. The search index will automatically update when you commit changes (via git pre-commit hook)

## URL Structure

URLs automatically strip the numeric prefix:
- `01-quick-start.md` → `/quick-start`
- `forms/01-overview.md` → `/forms/overview`

This allows you to reorder documentation without breaking existing links.

## Search System

The documentation site includes a powerful search system powered by Fuse.js for fast, client-side fuzzy search.

### What Gets Indexed

The search index includes the following data from each markdown file:

- **Title** - Derived from the filename (highest search weight: 50%)
- **Headings** - All H1, H2, H3, etc. headings in the content (weight: 30%)
- **Content** - The full text content of the markdown file (weight: 20%)
- **Section** - The folder/category the document belongs to
- **Breadcrumb** - Navigation path for the document
- **URL** - The clean URL without numeric prefixes

### How Search Works

1. **Index Generation**: Run `php artisan command:build-index` to generate `/public/docs-search-index.json`
2. **Auto-Updates**: The search index automatically rebuilds when markdown files change (via git pre-commit hook)
3. **Client-Side Search**: Fuse.js performs fuzzy search in the browser with no server requests
4. **Smart Highlighting**: Search terms are highlighted in results with fallback logic
5. **Keyboard Navigation**: Use ↑/↓ arrows to navigate results, Enter to select

### Search Features

- **Fuzzy Matching**: Finds results even with typos or partial matches
- **Grouped Results**: Results are organized by document section
- **Instant Results**: No loading delays - search happens as you type
- **Keyboard Shortcuts**: Press ⌘K (Mac) or Ctrl+K (Windows/Linux) to focus search
- **Smart Ranking**: Title matches are weighted higher than content matches

### Search Configuration

Search behavior can be customized in `resources/js/docs-search.js`:

```javascript
// Search thresholds and weights
threshold: 0.3,        // Lower = more strict matching
minMatchCharLength: 2, // Minimum characters to highlight
keys: [
    { name: 'title', weight: 0.5 },     // 50% weight
    { name: 'headings', weight: 0.3 },  // 30% weight  
    { name: 'content', weight: 0.2 }    // 20% weight
]
```

The search index is automatically maintained - just add new markdown files and commit your changes!