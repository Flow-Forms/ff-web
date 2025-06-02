# Flow Forms Documentation

## Frontmatter Configuration

Documentation files use YAML frontmatter for navigation and ordering:

```yaml
---
title: Getting Started Guide
order: 2
---
```

### Regular Documentation Files

- **`title`** - Display title for navigation (defaults to filename)
- **`order`** - Menu position (lower numbers appear first, default: 999)

### Main Documentation Index

```yaml
---
title: Flow Forms Documentation
is_index: true
sections:
  - title: Getting Started
    items:
      - title: Quick Start
        description: Get up and running fast
        url: /quick-start
---
```

### Folder Configuration

Create `_meta.md` inside any folder:

```yaml
---
title: API Documentation
order: 5
---
```

## File Structure

```
getting-started.md
security.md
forms/
  _meta.md
  overview.md
  field-types.md
```

## Adding Documentation

1. Create a markdown file with a descriptive name
2. Add frontmatter for title and order
3. Commit changes (search index updates automatically)

## URLs

- `getting-started.md` → `/getting-started`
- `forms/overview.md` → `/forms/overview`

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