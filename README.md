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