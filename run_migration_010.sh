#!/bin/bash
# Script de migration pour Linux/Mac
# Pour Windows, utilisez: php run_migration_010.php

clear

echo "═══════════════════════════════════════════════════════════════"
echo "    Migration 010: Initialisation des Rôles et Permissions"
echo "═══════════════════════════════════════════════════════════════"
echo ""

# Check if php is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP n'est pas trouvé. Assurez-vous que PHP est installé."
    exit 1
fi

# Run the PHP migration script
php run_migration_010.php

exit $?
