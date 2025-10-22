#!/bin/bash

# ========================================
# Performance Cache Setup Script
# THW-Trainer App
# ========================================

echo "🚀 THW-Trainer Performance Optimization"
echo "========================================"
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Step 1: Clear all caches
echo "🧹 Step 1: Clearing all caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✅ All caches cleared${NC}"
echo ""

# Step 2: Optimize autoload
echo "⚡ Step 2: Optimizing Composer autoload..."
composer dump-autoload --optimize
echo -e "${GREEN}✅ Autoload optimized${NC}"
echo ""

# Step 3: Cache configuration
echo "📝 Step 3: Caching configuration..."
php artisan config:cache
echo -e "${GREEN}✅ Configuration cached${NC}"
echo ""

# Step 4: Cache routes
echo "🛣️  Step 4: Caching routes..."
php artisan route:cache
echo -e "${GREEN}✅ Routes cached${NC}"
echo ""

# Step 5: Cache views
echo "👁️  Step 5: Caching views..."
php artisan view:cache
echo -e "${GREEN}✅ Views cached${NC}"
echo ""

# Step 6: Build frontend assets (if needed)
echo "🎨 Step 6: Building frontend assets..."
if [ -f "package.json" ]; then
    npm run build
    echo -e "${GREEN}✅ Frontend assets built${NC}"
else
    echo -e "${YELLOW}⚠️  No package.json found, skipping npm build${NC}"
fi
echo ""

# Final check
echo ""
echo "=========================================="
echo -e "${GREEN}✅ Performance optimization complete!${NC}"
echo "=========================================="
echo ""
echo "📊 Next steps:"
echo "  1. Test your application"
echo "  2. Monitor performance"
echo "  3. Check error logs if issues occur"
echo ""
echo "🔄 To revert (in development):"
echo "   bash clear-performance-cache.sh"
echo ""
