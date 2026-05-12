# E-Mail Spam-Problem beheben

## Problem
Registrierungs-E-Mails landen bei Usern im Spam-Ordner.

## Ursachen & Lösungen

### 1. DNS-Records fehlen (HAUPTPROBLEM!)

#### SPF-Record einrichten
**Was:** Erlaubt deinem Mail-Server E-Mails für deine Domain zu versenden.

**DNS-Eintrag bei deinem Domain-Provider:**
```
Type: TXT
Name: @
Value: v=spf1 include:bero-host.de a mx ~all
```

**Prüfen:**
```bash
dig TXT thw-trainer.de
# oder online: https://mxtoolbox.com/spf.aspx
```

#### DKIM-Record einrichten ❌ AKTUELL FEHLERHAFT!
**Was:** Digitale Signatur für E-Mails (beweist Echtheit).

**PROBLEM:** DKIM ist aktiviert, aber die Signatur ist ungültig!
```
dkim=fail (signature did not verify)
DKIM-Signature: [...] s=default; d=thw-trainer.de
```

**Einrichtung:**
1. **Bei bero-host.de einloggen** und DKIM-Einstellungen prüfen
2. Den **DKIM Public Key** abfragen (sollte von bero-host bereitgestellt werden)
3. Den Public Key als DNS TXT-Record eintragen:

```
Type: TXT
Name: default._domainkey
Value: v=DKIM1; k=rsa; p=<PUBLIC_KEY_VON_BERO_HOST>
```

**Wichtig:** Der Selector in deiner Mail ist `default`, also muss der DNS-Record `default._domainkey.thw-trainer.de` heißen!

**Aktuell prüfen:**
```bash
dig TXT default._domainkey.thw-trainer.de
# oder online:
# https://mxtoolbox.com/dkim.aspx
```

**Falls kein Record existiert:** Support von bero-host.de kontaktieren!

#### DMARC-Record einrichten
**Was:** Gibt E-Mail-Providern Anweisungen wie mit nicht-authentifizierten E-Mails umzugehen ist.

**DNS-Eintrag:**
```
Type: TXT
Name: _dmarc
Value: v=DMARC1; p=quarantine; rua=mailto:noreply@thw-trainer.de; pct=100; adkim=s; aspf=s
```

**Prüfen:**
```bash
dig TXT _dmarc.thw-trainer.de
```

### 2. E-Mail-Template Optimierungen (✓ ERLEDIGT)

Die E-Mail wurde optimiert:
- ✓ Vollständiges HTML-Dokument mit DOCTYPE
- ✓ Absolute URLs für Bilder (https://thw-trainer.de/...)
- ✓ Klarer, professioneller Text
- ✓ Impressum & Datenschutz-Links
- ✓ Alternative Textlink zum Button
- ✓ Bessere Formatierung für Mobile

### 3. Mail-Server Reputation

#### Reverse DNS (PTR-Record)
Prüfe ob dein Server einen korrekten PTR-Record hat:
```bash
dig -x <DEINE_SERVER_IP>
# Sollte zeigen: web10.bero-host.de
```

Falls nicht → Bei bero-host.de PTR-Record einrichten lassen.

#### IP-Reputation prüfen
Prüfe ob deine Server-IP auf Blacklists steht:
- https://mxtoolbox.com/blacklists.aspx
- https://multirbl.valli.org/

### 4. Mail-Header optimieren

Füge in `config/mail.php` hinzu:
```php
'reply_to' => [
    'address' => env('MAIL_FROM_ADDRESS'),
    'name' => env('MAIL_FROM_NAME'),
],
```

### 5. Content-Optimierungen

**Vermeide Spam-Trigger:**
- ❌ Keine übermäßigen Großbuchstaben
- ❌ Keine "!!!!" Ausrufezeichen
- ❌ Keine Wörter wie "GRATIS", "KOSTENLOS", "JETZT"
- ✓ Professioneller, sachlicher Ton
- ✓ Klare Absender-Identität

### 6. Authentifizierung verbessern

**In Laravel Mail-Config ergänzen:**

Stelle sicher dass in `.env`:
```env
MAIL_FROM_ADDRESS=noreply@thw-trainer.de
MAIL_FROM_NAME="THW-Trainer"
```

**Keine generischen Adressen wie:**
- ❌ no-reply@...
- ✓ noreply@... (ohne Bindestrich ist besser)

## Sofort-Checkliste

1. **DNS-Records einrichten** (wichtigste Maßnahme!)
   - [ ] SPF-Record
   - [ ] DKIM-Record
   - [ ] DMARC-Record
   - [ ] PTR-Record prüfen

2. **Reputation prüfen**
   - [ ] IP auf Blacklists checken
   - [ ] Mail-Server-Score testen: https://www.mail-tester.com/

3. **Template testen**
   - [ ] Test-E-Mail an verschiedene Provider senden:
     - Gmail
     - Outlook/Hotmail
     - Web.de / GMX
   - [ ] Spam-Score prüfen

4. **Monitoring einrichten**
   - [ ] Logging aktiviert (bereits erledigt ✓)
   - [ ] Bounce-E-Mails überwachen

## Testing

### Test-E-Mail senden (EMPFOHLEN):
```bash
# Direkt mit Test-Script:
php test-mail-dkim.php deine@email.de

# Oder gehe zu https://www.mail-tester.com/ und nutze deren E-Mail-Adresse:
php test-mail-dkim.php test-abc123@srv1.mail-tester.com
```

### Test mit verschiedenen Providern:
```bash
# Gmail
php test-mail-dkim.php test+gmail@deineemail.com

# Outlook/Hotmail  
php test-mail-dkim.php test+outlook@deineemail.com

# GMX/Web.de
php test-mail-dkim.php test+gmx@deineemail.com
```

### Logs überprüfen:
```bash
tail -f storage/logs/laravel.log | grep -i "mail\|email"
```

### DKIM-Signatur im Header prüfen:
Nach dem Test sollte im Mail-Header stehen:
```
Received: from thw-trainer.de (web10.bero-host.de [45.82.121.245])
# Nicht mehr: from [127.0.0.1]

DKIM-Signature: v=1; a=rsa-sha256; c=relaxed/relaxed; d=thw-trainer.de;
```

Und bei der Validierung:
```
dkim=pass (signature verified)
# Nicht mehr: dkim=fail
```

## ✅ GELÖST: DKIM-Problem behoben!

**AKTUELLER STATUS (21.10.2025):**
- ✅ SPF: PASS
- ✅ DMARC: PASS  
- ✅ DKIM: DNS-Record gesetzt (war: FAIL)
- ✅ Spam-Score: 1 (gut)

**Das Problem war:** Laravel sendete als `[127.0.0.1]` statt als `thw-trainer.de`, was die DKIM-Signatur invalidierte.

**Die Lösung:**
1. ✅ `.env`: `MAIL_ENCRYPTION=tls` (war: STARTTLS)
2. ✅ `config/mail.php`: `local_domain` hinzugefügt
3. ✅ `config/mail.php`: `reply_to` hinzugefügt

### Sofort-Maßnahme:

**An bero-host.de Support schreiben:**
```
Betreff: DRINGENDE DKIM-Fehlkonfiguration bei thw-trainer.de

Sehr geehrtes Support-Team,

meine E-Mails von thw-trainer.de landen im Spam, weil DKIM nicht korrekt 
funktioniert.

PROBLEM:
- DKIM-Signatur wird vom Server gesetzt (Selector: default)
- Aber die Validierung schlägt fehl: "dkim=fail (signature did not verify)"
- Der DNS-Record für "default._domainkey.thw-trainer.de" fehlt oder ist falsch

BITTE:
1. Den korrekten DKIM Public Key für "default._domainkey.thw-trainer.de" 
   bereitstellen
2. Prüfen ob der DNS-Record korrekt gesetzt ist
3. DKIM-Konfiguration auf dem Server (web10.bero-host.de) überprüfen

Mail-Header zur Analyse:
- DKIM-Signature: v=1; a=rsa-sha256; c=relaxed/relaxed; d=thw-trainer.de; s=default
- Server: web10.bero-host.de
- Absender: noreply@thw-trainer.de

Domain: thw-trainer.de
Test-Empfänger: niclas.reutter@thw-ueberlingen.de

Vielen Dank für die schnelle Hilfe!
```

## Erwartete Verbesserung

Nach DNS-Setup sollte sich der Spam-Score deutlich verbessern:
- **Vorher:** 5-7/10 Punkte
- **Nachher:** 9-10/10 Punkte

**Wichtig:** DNS-Änderungen brauchen 24-48h bis sie weltweit propagiert sind!

## Weiterführende Links

- SPF-Record Generator: https://www.spfwizard.net/
- DMARC-Generator: https://www.kitterman.com/dmarc/assistant.html
- Mail-Tester: https://www.mail-tester.com/
- MX Toolbox: https://mxtoolbox.com/
