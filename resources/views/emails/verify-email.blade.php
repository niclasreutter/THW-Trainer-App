@component('mail::message')
# E-Mail-Adresse bestätigen

Hallo {{ $user->name }},

du hast deine E-Mail-Adresse in deinem THW-Trainer Profil geändert. Um die Änderung zu bestätigen, klicke bitte auf den Button unten.

**Wichtig:** Dieser Link ist nur für 5 Minuten gültig.

@component('mail::button', ['url' => $verificationUrl])
E-Mail-Adresse bestätigen
@endcomponent

Falls du diese Änderung nicht vorgenommen hast, ignoriere diese E-Mail einfach.

Mit freundlichen Grüßen,<br>
THW-Trainer Team

---
Falls der Button nicht funktioniert, kopiere diesen Link in deinen Browser: {{ $verificationUrl }}
@endcomponent
