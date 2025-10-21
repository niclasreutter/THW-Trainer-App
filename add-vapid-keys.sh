#!/bin/bash

# Script zum Hinzufügen der VAPID-Keys zur .env Datei
# Ausführen mit: bash add-vapid-keys.sh

ENV_FILE=".env"

# Prüfen ob .env existiert
if [ ! -f "$ENV_FILE" ]; then
    echo "❌ .env Datei nicht gefunden!"
    exit 1
fi

# Prüfen ob VAPID-Keys bereits vorhanden sind
if grep -q "VAPID_PUBLIC_KEY" "$ENV_FILE"; then
    echo "⚠️  VAPID-Keys sind bereits in .env vorhanden!"
    echo "Möchtest du sie überschreiben? (y/n)"
    read -r response
    if [ "$response" != "y" ]; then
        echo "Abgebrochen."
        exit 0
    fi
    # Alte Keys entfernen
    sed -i.bak '/VAPID_SUBJECT/d' "$ENV_FILE"
    sed -i.bak '/VAPID_PUBLIC_KEY/d' "$ENV_FILE"
    sed -i.bak '/VAPID_PRIVATE_KEY/d' "$ENV_FILE"
fi

# VAPID-Keys hinzufügen
echo "" >> "$ENV_FILE"
echo "# VAPID Keys für Push-Benachrichtigungen" >> "$ENV_FILE"
echo "VAPID_SUBJECT=mailto:niclas@thw-trainer.de" >> "$ENV_FILE"
echo "VAPID_PUBLIC_KEY=BBbF_AH9rF_1KPspaZ_blQgxkElPP3INrBBErFeNoVw7zyMj6m7Votl-UzPiq3u7Vib0OE02WseQkWfI07IQJ4s" >> "$ENV_FILE"
echo "VAPID_PRIVATE_KEY=ADU_xBryHePpnfumIR87CRNedFnTHrAsjZEGRTbQU50" >> "$ENV_FILE"

echo "✅ VAPID-Keys wurden zur .env hinzugefügt!"
echo ""
echo "Nächste Schritte:"
echo "1. Config-Cache leeren: php artisan config:clear"
echo "2. PWA testen: App als PWA installieren"
echo "3. Push testen: Im Profil 'Test-Benachrichtigung' klicken"
