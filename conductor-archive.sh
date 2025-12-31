#!/bin/bash

set -e  # Exit on any error

echo "🧹 Starting Conductor workspace cleanup..."

# Unlink from Laravel Herd if available
if command -v herd &> /dev/null; then
    echo "🔗 Unlinking from Laravel Herd..."
    if ! herd unlink 2>/dev/null; then
        echo "⚠️  Warning: Could not unlink from Herd (may already be unlinked or not linked)"
    else
        echo "✅ Herd unlinked"
    fi
else
    echo "⚠️  Laravel Herd not found, skipping herd unlink"
fi

echo ""
echo "🎉 Workspace cleanup complete!"
