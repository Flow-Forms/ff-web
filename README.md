# Documentation Organization

This directory contains the markdown documentation files for Flow Forms.

## File Naming Convention

Files use a numeric prefix to control their display order in the navigation:

- `01-getting-started.md` - Displays as "Getting Started" (first in navigation)
- `02-security.md` - Displays as "Security" (second in navigation)

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
- `01-getting-started.md` → `/getting-started`
- `forms/01-overview.md` → `/forms/overview`

This allows you to reorder documentation without breaking existing links.

## Search System

The documentation site includes a powerful search system powered by Laravel Scout with a database driver for fast, server-side full-text search.

### What Gets Indexed

The search index includes the following data from each markdown file:

- **Title** - Derived from the filename (primary search field)
- **Headings** - All H1, H2, H3, etc. headings in the content
- **Content** - The full text content of the markdown file  
- **Section** - The folder/category the document belongs to
- **Breadcrumb** - Navigation path for the document
- **URL** - The clean URL without numeric prefixes

### How Search Works

1. **Database Storage**: Documentation is stored in a `documentation` table with full-text indexing
2. **Index Generation**: Run `php artisan command:build-index` to populate the database from markdown files
3. **Auto-Updates**: The search index automatically rebuilds when markdown files change (via git pre-commit hook)
4. **Server-Side Search**: Laravel Scout performs database queries with relevance ranking
5. **Smart Snippets**: Search results include contextual content snippets around matching terms
6. **Keyboard Navigation**: Use ↑/↓ arrows to navigate results, Enter to select

### Search Features

- **Full-Text Search**: Database-powered search with proper relevance scoring
- **Grouped Results**: Results are organized by document section
- **Fast Performance**: Server-side search with optimized database queries
- **Keyboard Shortcuts**: Press ⌘K (Mac) or Ctrl+K (Windows/Linux) to focus search
- **Smart Highlighting**: Search terms are highlighted in titles and content
- **Real-Time Results**: Search as you type with request cancellation

### Search API

The search is exposed via a REST API endpoint:

```
GET /api/docs/search?q=your+search+term
```

Response format:
```json
{
  "results": [
    {
      "section": "Documentation", 
      "items": [
        {
          "id": "security",
          "title": "Security",
          "content": "...snippet with highlighted terms...",
          "section": "Documentation", 
          "breadcrumb": "Security",
          "url": "/security"
        }
      ]
    }
  ],
  "query": "your search term",
  "total": 1
}
```

### Technical Implementation

- **Laravel Scout**: Provides search abstraction layer
- **Database Driver**: Uses your existing database for search indexing  
- **Documentation Model**: `App\Models\Documentation` handles search indexing
- **Search Controller**: `App\Http\Controllers\Api\DocumentationSearchController`
- **Frontend**: Alpine.js component makes API calls for search results

The search index is automatically maintained - just add new markdown files and commit your changes!