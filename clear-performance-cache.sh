#!/bin/bash

# ========================================
# Clear Performance Cache Script
# THW-Trainer App (Development Use Only!)
# ========================================

echo "🧹 Clearing Performance Caches"
echo "================================"
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Warning
echo -e "${YELLOW}⚠️  WARNING: This will clear all cached data!${NC}"
echo -e "${YELLOW}   Only use this in development or when updating code.${NC}"
echo ""

# Step 1: Clear application cache
echo "🗑️  Clearing application cache..."
php artisan cache:clear
echo -e "${GREEN}✅ Application cache cleared${NC}"
echo ""

# Step 2: Clear config cache
echo "🗑️  Clearing config cache..."
php artisan config:clear
echo -e "${GREEN}✅ Config cache cleared${NC}"
echo ""

# Step 3: Clear route cache
echo "🗑️  Clearing route cache..."
php artisan route:clear
echo -e "${GREEN}✅ Route cache cleared${NC}"
echo ""

# Step 4: Clear view cache
echo "🗑️  Clearing view cache..."
php artisan view:clear
echo -e "${GREEN}✅ View cache cleared${NC}"
echo ""

# Step 5: Clear compiled classes
echo "🗑️  Clearing compiled classes..."
php artisan clear-compiled
echo -e "${GREEN}✅ Compiled classes cleared${NC}"
echo ""

echo "================================"
echo -e "${GREEN}✅ All caches cleared!${NC}"
echo "================================"
echo ""
echo "📝 Remember to rebuild caches before deployment:"
echo "   bash optimize-performance.sh"
echo ""
