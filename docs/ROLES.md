# Rollen-Struktur

```
useroll (string column on users table)
─────────────────────────────────────────────────
                                                    capabilities grow ▶
   ┌──────────┐         ┌─────────────┐         ┌──────────┐
   │   user   │ ──────▶ │ contributor │ ──────▶ │  admin   │
   │ default  │         │    (new)    │         │          │
   └──────────┘         └─────────────┘         └──────────┘
                              │                       │
                              │                       └─ all of admin/*
                              └─ edit questions, resolve issues

Orthogonal role (pivot on ortsverband_user, NOT useroll):
   ┌──────────────────────────┐
   │  ausbildungsbeauftragter │  →  manages one Ortsverband's
   └──────────────────────────┘     lernpools / sessions / members
```

## Capability matrix

| Capability                                            | user | contributor | admin |
|-------------------------------------------------------|:----:|:-----------:|:-----:|
| Dashboard, Practice, Exam, browse Lehrgänge           |  ✓   |      ✓      |   ✓   |
| **Edit** global questions (frage / antworten / lösung)|      |      ✓      |   ✓   |
| **Edit** Zusatz-Fragen                                |      |      ✓      |   ✓   |
| **Edit** Lehrgang questions (per-question)            |      |      ✓      |   ✓   |
| Resolve issue reports (read + status update)          |      |      ✓      |   ✓   |
| Create or delete questions / Zusatz-Fragen            |      |             |   ✓   |
| Lehrgang CRUD + CSV import + delete-question          |      |             |   ✓   |
| Delete issue reports                                  |      |             |   ✓   |
| User management (`/admin/users`)                      |      |             |   ✓   |
| Admin dashboard, statistics, shop-analytics           |      |             |   ✓   |
| Newsletter, surveys, push, contact-messages           |      |             |   ✓   |
| Ortsverband admin, Leagues, logs, failed-jobs         |      |             |   ✓   |
| Time-simulator (non-prod only)                        |      |             |   ✓   |

## Code surface

| Layer       | admin only                         | admin + contributor                          |
|-------------|------------------------------------|----------------------------------------------|
| Helper      | `User::isAdmin()`                  | `User::canEditQuestions()`                   |
| Middleware  | `AdminMiddleware`                  | `QuestionEditorMiddleware`                   |
| Route group | second `admin.*` group in `routes/web.php` | first `admin.*` group in `routes/web.php` |

## Hinweise

- `ausbildungsbeauftragter` ist unabhängig von `useroll`: ein `user` kann Ausbildungsbeauftragter seines Ortsverbands sein, ohne `contributor` oder `admin` zu sein. Die beiden Systeme komponieren — ein Admin kann gleichzeitig Ausbildungsbeauftragter sein.
- `useroll` ist **nicht** in `User::$fillable`. Beim Setzen per Code (z. B. Seeder, Tinker) muss die Property direkt zugewiesen und `save()` aufgerufen werden.
- Rolle vergeben: `/admin/users` → User aufklappen → Rolle-Select → Speichern.
