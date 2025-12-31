#!/bin/bash

set -e  # Exit on any error

echo "🚀 Starting Conductor workspace setup..."

# Check for required tools
echo "Checking for required tools..."

if ! command -v php &> /dev/null; then
    echo "❌ Error: PHP is not installed or not in PATH"
    exit 1
fi

if ! command -v composer &> /dev/null; then
    echo "❌ Error: Composer is not installed or not in PATH"
    exit 1
fi

if ! command -v npm &> /dev/null; then
    echo "❌ Error: npm is not installed or not in PATH"
    exit 1
fi

echo "✅ All required tools found"

# Check for .env in root
if [ ! -f "$CONDUCTOR_ROOT_PATH/.env" ]; then
    echo "❌ Error: .env file not found at $CONDUCTOR_ROOT_PATH/.env"
    echo "Please ensure you have a .env file in your repository root"
    exit 1
fi

# Copy .env from root
echo "📝 Copying .env file..."
cp "$CONDUCTOR_ROOT_PATH/.env" .env
echo "✅ .env copied"

# Set secure permissions on .env
chmod 600 .env
echo "✅ Secure permissions set on .env"

# Update workspace-specific environment variables
if [ ! -z "$CONDUCTOR_WORKSPACE_NAME" ]; then
    echo "🔧 Configuring workspace-specific variables..."

    # Validate workspace name for security (only alphanumeric, underscore, and dash)
    if [[ ! "$CONDUCTOR_WORKSPACE_NAME" =~ ^[a-zA-Z0-9_-]+$ ]]; then
        echo "❌ Error: Invalid workspace name. Only alphanumeric, underscore, and dash allowed."
        exit 1
    fi

    # Update APP_URL to use workspace name
    if grep -q "^APP_URL=" .env; then
        sed -i '' "s|^APP_URL=.*|APP_URL=http://${CONDUCTOR_WORKSPACE_NAME}.test|" .env
        echo "✅ APP_URL set to http://${CONDUCTOR_WORKSPACE_NAME}.test"
    fi
fi

# Check for auth.json in root
if [ -f "$CONDUCTOR_ROOT_PATH/auth.json" ]; then
    echo "🔐 Copying auth.json..."
    cp "$CONDUCTOR_ROOT_PATH/auth.json" auth.json
    chmod 600 auth.json
    echo "✅ auth.json copied with secure permissions"
else
    echo "⚠️  Warning: auth.json not found at $CONDUCTOR_ROOT_PATH/auth.json"
    echo "Flux Pro may not work without authentication credentials"
fi

# Install Composer dependencies
echo "📦 Installing Composer dependencies..."
composer install --no-interaction --prefer-dist
echo "✅ Composer dependencies installed"

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate --ansi
    echo "✅ Application key generated"
else
    echo "✅ Application key already set"
fi

# Install NPM dependencies
echo "📦 Installing NPM dependencies..."
npm install
echo "✅ NPM dependencies installed"

# Run database migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force
echo "✅ Database migrations complete"

# Build frontend assets
echo "🎨 Building frontend assets..."
npm run build
echo "✅ Frontend assets built"

# Link with Laravel Herd if available
if command -v herd &> /dev/null; then
    echo "🔗 Linking with Laravel Herd..."
    herd link
    echo "✅ Herd linked - site available at http://${CONDUCTOR_WORKSPACE_NAME}.test"
else
    echo "⚠️  Warning: Laravel Herd not found, skipping herd link"
    echo "You may need to configure your local web server manually"
fi

# Configure Claude Code allowed commands for this workspace
if [ -d "$CONDUCTOR_ROOT_PATH/.claude" ]; then
    echo "🤖 Configuring Claude Code settings..."
    mkdir -p .claude

    # Copy Claude Code settings from root if they exist
    if [ -f "$CONDUCTOR_ROOT_PATH/.claude/settings.json" ]; then
        cp "$CONDUCTOR_ROOT_PATH/.claude/settings.json" .claude/settings.json
        echo "✅ Claude Code settings copied from root"
    fi
else
    echo "⚠️  Warning: No .claude directory found in root"
    echo "Claude Code command permissions will need to be granted manually"
fi

echo ""
echo "🎉 Workspace setup complete!"
echo "You can now start working in this workspace."
