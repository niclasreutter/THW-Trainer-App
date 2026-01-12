# File Navigation Guide

## Schnell-Referenz: Wo finde ich was?

### Authentication
```
Controllers: app/Http/Controllers/Auth/
Views:       resources/views/auth/
Mail:        app/Mail/VerifyRegistrationMail.php
```

### Practice Mode
```
Controller:  app/Http/Controllers/PracticeController.php
View:        resources/views/practice.blade.php
Helper:      app/Helpers/ProgressHelper.php
Model:       app/Models/UserQuestionProgress.php
```

### Exams
```
Controller:  app/Http/Controllers/ExamController.php
Views:       resources/views/exam.blade.php, exam-result.blade.php
Model:       app/Models/ExamStatistic.php
```

### Courses (Lehrgänge)
```
Controller:  app/Http/Controllers/LehrgangController.php
Views:       resources/views/lehrgaenge/
Models:      app/Models/Lehrgang.php, LehrgangQuestion.php
```

### Organizations (Ortsverbände)
```
Controller:  app/Http/Controllers/OrtsverbandController.php
Views:       resources/views/ortsverband/
Model:       app/Models/Ortsverband.php
Middleware:  app/Http/Middleware/OrtsverbandAusbildungsbeauftragterMiddleware.php
```

### Learning Pools (Lernpools)
```
Controllers:
  - app/Http/Controllers/OrtsverbandLernpoolController.php
  - app/Http/Controllers/OrtsverbandLernpoolQuestionController.php
  - app/Http/Controllers/OrtsverbandLernpoolPracticeController.php

Views:       resources/views/ortsverband/lernpools/

Models:
  - app/Models/OrtsverbandLernpool.php
  - app/Models/OrtsverbandLernpoolQuestion.php
  - app/Models/OrtsverbandLernpoolEnrollment.php
  - app/Models/OrtsverbandLernpoolProgress.php

Policy:      app/Policies/OrtsverbandLernpoolPolicy.php
```

### Gamification
```
Service:     app/Services/GamificationService.php (MAIN)
Components:
  - resources/views/components/achievement-popup.blade.php
  - resources/views/components/gamification-notifications.blade.php
User Model:  app/Models/User.php (achievements, points, level, streaks)
```

### Admin Panel
```
Controllers: app/Http/Controllers/Admin/
Views:       resources/views/admin/
Middleware:  app/Http/Middleware/AdminMiddleware.php
```

### Emails
```
Mail:        app/Mail/ (8 mail classes)
Views:       resources/views/emails/
Commands:    app/Console/Commands/ (scheduled tasks)
```

### Configuration
```
Environment: .env
Laravel:     config/
Vite:        vite.config.js
Tailwind:    tailwind.config.js
Composer:    composer.json
NPM:         package.json
```

## Such-Befehle

```bash
# Controller für Feature finden
ls -la app/Http/Controllers/*Lernpool*

# Views für Feature finden
find resources/views -name "*lernpool*"

# Route-Definition finden
grep -r "route('ortsverband.lernpools" .

# Model-Verwendung finden
grep -r "OrtsverbandLernpool::" app/

# Migrations auflisten
ls -la database/migrations/*lernpool*

# Policy-Verwendung finden
grep -r "authorize('create'" app/Http/Controllers/

# Component-Verwendung finden
grep -r "<x-achievement-popup" resources/views/
```

## Verzeichnisstruktur

```
/app
  ├── Http/Controllers/     → Business Logic
  ├── Models/               → Daten + Business Logic
  ├── Services/             → Cross-cutting (nur Gamification)
  ├── Policies/             → Authorization
  ├── Mail/                 → E-Mail Templates
  └── Console/Commands/     → Scheduled Tasks

/resources/views
  ├── layouts/              → Master Layouts
  ├── components/           → Wiederverwendbare UI
  ├── [feature]/            → Feature-Views
  └── admin/                → Admin Panel

/database/migrations        → 43 Migrations (chronologisch)
/routes/web.php             → Alle Routes
```
