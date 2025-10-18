# Translation Management System - Architecture

## System Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                        USER INTERFACE                            │
│                   (Admin Dashboard - Browser)                    │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             │ HTTP Requests
                             │
┌────────────────────────────▼────────────────────────────────────┐
│                         ROUTES LAYER                             │
│                     (routes/web.php)                             │
│                                                                   │
│  Auth Middleware → Admin Prefix → Translation Routes             │
│                                                                   │
│  • GET    /admin/translations              → index()             │
│  • GET    /admin/translations/create       → create()            │
│  • POST   /admin/translations              → store()             │
│  • GET    /admin/translations/{id}/edit    → edit()              │
│  • PUT    /admin/translations/{id}         → update()            │
│  • DELETE /admin/translations/{id}         → destroy()           │
│  • GET    /admin/translations/export       → export()            │
│  • POST   /admin/translations/import       → import()            │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             │ Route to Controller
                             │
┌────────────────────────────▼────────────────────────────────────┐
│                      CONTROLLER LAYER                            │
│         (app/Http/Controllers/Web/AdminTranslationController)    │
│                                                                   │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │   index()    │  │  create()    │  │   store()    │          │
│  │              │  │              │  │              │          │
│  │ • Search     │  │ • Get groups │  │ • Validate   │          │
│  │ • Filter     │  │ • Show form  │  │ • Create     │          │
│  │ • Paginate   │  │              │  │ • Redirect   │          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
│                                                                   │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │   edit()     │  │  update()    │  │  destroy()   │          │
│  │              │  │              │  │              │          │
│  │ • Get record │  │ • Validate   │  │ • Delete     │          │
│  │ • Get groups │  │ • Update     │  │ • Redirect   │          │
│  │ • Show form  │  │ • Redirect   │  │              │          │
│  └──────────────┘  └──────────────┘  └──────────────┘          │
│                                                                   │
│  ┌──────────────┐  ┌──────────────┐                             │
│  │  export()    │  │  import()    │                             │
│  │              │  │              │                             │
│  │ • Get all    │  │ • Validate   │                             │
│  │ • Format JSON│  │ • Parse JSON │                             │
│  │ • Return file│  │ • Bulk save  │                             │
│  └──────────────┘  └──────────────┘                             │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             │ Eloquent ORM
                             │
┌────────────────────────────▼────────────────────────────────────┐
│                        MODEL LAYER                               │
│                   (app/Models/Translation)                       │
│                                                                   │
│  Properties:                                                      │
│  • $fillable = ['key', 'value_en', 'value_ar', 'group']         │
│  • $timestamps = true                                            │
│                                                                   │
│  Static Methods:                                                  │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │ getByKey($key, $locale)                                   │   │
│  │ • Finds translation by key                                │   │
│  │ • Returns value in specified locale                       │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                   │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │ getByGroup($group, $locale)                               │   │
│  │ • Finds all translations in group                         │   │
│  │ • Returns array of key => value                           │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                   │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │ getAllGroups()                                            │   │
│  │ • Returns array of unique group names                     │   │
│  └──────────────────────────────────────────────────────────┘   │
└────────────────────────────┬────────────────────────────────────┘
                             │
                             │ SQL Queries
                             │
┌────────────────────────────▼────────────────────────────────────┐
│                       DATABASE LAYER                             │
│                    (MySQL/PostgreSQL/SQLite)                     │
│                                                                   │
│  Table: translations                                              │
│  ┌──────────────┬─────────────┬──────────────────────────┐      │
│  │ Column       │ Type        │ Constraints              │      │
│  ├──────────────┼─────────────┼──────────────────────────┤      │
│  │ id           │ BIGINT      │ PRIMARY KEY, AUTO_INC    │      │
│  │ key          │ VARCHAR(255)│ UNIQUE, NOT NULL         │      │
│  │ value_en     │ TEXT        │ NOT NULL                 │      │
│  │ value_ar     │ TEXT        │ NOT NULL                 │      │
│  │ group        │ VARCHAR(255)│ DEFAULT 'general'        │      │
│  │ created_at   │ TIMESTAMP   │ NULL                     │      │
│  │ updated_at   │ TIMESTAMP   │ NULL                     │      │
│  └──────────────┴─────────────┴──────────────────────────┘      │
│                                                                   │
│  Indexes:                                                         │
│  • PRIMARY KEY (id)                                              │
│  • UNIQUE INDEX (key)                                            │
│  • INDEX (group) - for filtering                                │
└───────────────────────────────────────────────────────────────────┘


┌─────────────────────────────────────────────────────────────────┐
│                         VIEW LAYER                               │
│              (resources/views/admin/translations/)               │
└─────────────────────────────────────────────────────────────────┘

┌──────────────────────────┐
│   index.blade.php        │  List View
│                          │
│  ┌────────────────────┐  │  • Search bar
│  │   Search Bar       │  │  • Group filter tabs
│  └────────────────────┘  │  • Data table
│                          │  • Pagination
│  ┌────────────────────┐  │  • Export/Import
│  │  Group Tabs        │  │  • Action buttons
│  │  [All][Hero][Feat] │  │
│  └────────────────────┘  │  Components:
│                          │  • Export dropdown
│  ┌────────────────────┐  │  • Import modal
│  │   Data Table       │  │  • Delete confirmation
│  │  ┌──┬────┬────┬──┐ │  │
│  │  │ID│ Key│ EN │AR│ │  │  Data Flow:
│  │  ├──┼────┼────┼──┤ │  │  User Input → Controller
│  │  │1 │hero│... │..│ │  │  → Model → Database
│  └────────────────────┘  │  → Model → View
│                          │
│  [Create] [Export] [Import]
└──────────────────────────┘

┌──────────────────────────┐
│  create.blade.php        │  Create Form
│                          │
│  ┌────────────────────┐  │  • Key input
│  │  Translation Key   │  │  • Group selector
│  │  [section.name]    │  │  • English textarea
│  └────────────────────┘  │  • Arabic textarea
│                          │  • Live preview
│  ┌────────────────────┐  │  • Validation
│  │  Group             │  │
│  │  [hero ▼]          │  │  Features:
│  └────────────────────┘  │  • Auto-suggest groups
│                          │  • Live preview panel
│  ┌────────────────────┐  │  • RTL support
│  │  English Value     │  │  • Form validation
│  │  [textarea]        │  │
│  └────────────────────┘  │  Validation:
│                          │  • Key required, unique
│  ┌────────────────────┐  │  • Values required
│  │  Arabic Value      │  │  • Group required
│  │  [textarea] (RTL)  │  │
│  └────────────────────┘  │
│                          │
│  ┌────────────────────┐  │
│  │  Live Preview      │  │
│  │  [EN] │ [AR]       │  │
│  └────────────────────┘  │
│                          │
│  [Cancel] [Create]       │
└──────────────────────────┘

┌──────────────────────────┐
│   edit.blade.php         │  Edit Form
│                          │
│  Same as create.blade.php│  Additional:
│  +                        │  • Pre-filled values
│  ┌────────────────────┐  │  • Metadata display
│  │  Metadata          │  │  • Delete button
│  │  Created: ...      │  │  • Update action
│  │  Updated: ...      │  │
│  └────────────────────┘  │
│                          │
│  [Delete] [Cancel] [Update]
└──────────────────────────┘


┌─────────────────────────────────────────────────────────────────┐
│                       DATA FLOW DIAGRAM                          │
└─────────────────────────────────────────────────────────────────┘

CREATE TRANSLATION:
User → Create Button → create() → Form → Submit → store()
→ Validate → Model::create() → Database → Redirect → index()

READ TRANSLATIONS:
User → Translations Menu → index() → Model::all() → Database
→ Model → View with Data → Render Table

UPDATE TRANSLATION:
User → Edit Button → edit($id) → Model::find() → Form
→ Submit → update() → Validate → Model::update() → Database
→ Redirect → index()

DELETE TRANSLATION:
User → Delete Button → Confirm → destroy($id) → Model::delete()
→ Database → Redirect → index()

SEARCH TRANSLATIONS:
User → Search Input → index(search) → Model::where('like')
→ Database → Filtered Results → View

FILTER BY GROUP:
User → Group Tab → index(group) → Model::where('group')
→ Database → Filtered Results → View

EXPORT TRANSLATIONS:
User → Export Button → export(locale) → Model::all()
→ Format JSON → Download File

IMPORT TRANSLATIONS:
User → Import Button → Upload File → import() → Parse JSON
→ Model::updateOrCreate() → Database → Redirect → index()


┌─────────────────────────────────────────────────────────────────┐
│                    SECURITY ARCHITECTURE                         │
└─────────────────────────────────────────────────────────────────┘

Request → Auth Middleware → Route → Controller
    ↓
    ├─ Check if user is authenticated
    ├─ Verify CSRF token (POST/PUT/DELETE)
    ├─ Validate input data
    ├─ Sanitize user input
    └─ Execute action

Protection Layers:
1. Authentication Middleware (auth)
2. CSRF Protection (Laravel automatic)
3. Input Validation (FormRequest/validate())
4. SQL Injection Prevention (Eloquent ORM)
5. XSS Prevention (Blade {{ }} escaping)
6. File Upload Validation (JSON only)


┌─────────────────────────────────────────────────────────────────┐
│                   FEATURE ARCHITECTURE                           │
└─────────────────────────────────────────────────────────────────┘

SEARCH FEATURE:
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│ Search Input│ →  │  Controller │ →  │   Model     │
│  (Frontend) │    │   Query     │    │  WHERE LIKE │
└─────────────┘    └─────────────┘    └─────────────┘
                                              ↓
                                      ┌─────────────┐
                                      │  Database   │
                                      │   Search    │
                                      └─────────────┘

GROUP FILTER:
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│  Tab Click  │ →  │  Controller │ →  │   Model     │
│  (Frontend) │    │   Filter    │    │ WHERE group │
└─────────────┘    └─────────────┘    └─────────────┘

LIVE PREVIEW:
┌─────────────┐    ┌─────────────┐
│ Text Input  │ →  │ JavaScript  │
│  (Textarea) │    │   Event     │
└─────────────┘    └─────────────┘
                          ↓
                   ┌─────────────┐
                   │ Update DOM  │
                   │  (Preview)  │
                   └─────────────┘

IMPORT/EXPORT:
Export: Model → Format JSON → Response
Import: Upload → Parse JSON → Model → Database


┌─────────────────────────────────────────────────────────────────┐
│                  PERFORMANCE ARCHITECTURE                        │
└─────────────────────────────────────────────────────────────────┘

Optimization Strategies:
1. Pagination (20 items per page)
2. Database Indexing (key, group)
3. Lazy Loading Views
4. Minimal JavaScript
5. CDN for Tailwind CSS
6. Eloquent Query Optimization
7. Route Caching (production)
8. View Caching (production)
9. Config Caching (production)

Database Query Optimization:
• SELECT with WHERE clauses
• LIMIT for pagination
• INDEX on frequently queried columns
• No N+1 queries
• Efficient LIKE queries


┌─────────────────────────────────────────────────────────────────┐
│                    DEPLOYMENT ARCHITECTURE                       │
└─────────────────────────────────────────────────────────────────┘

Development:
1. Run migrations: php artisan migrate
2. Seed data: php artisan db:seed
3. Clear caches: php artisan cache:clear
4. Test locally

Staging:
1. Deploy code
2. Run migrations: php artisan migrate --force
3. Seed if needed
4. Clear caches
5. Test functionality

Production:
1. Deploy code
2. Run migrations: php artisan migrate --force
3. DON'T seed (preserve existing data)
4. Cache routes: php artisan route:cache
5. Cache config: php artisan config:cache
6. Cache views: php artisan view:cache
7. Optimize autoloader: composer dump-autoload -o


┌─────────────────────────────────────────────────────────────────┐
│                      INTEGRATION POINTS                          │
└─────────────────────────────────────────────────────────────────┘

Frontend Integration:
┌─────────────────┐
│ Frontend App    │ → API Endpoint → Controller
│ (React/Vue)     │                     ↓
└─────────────────┘                  Model
                                        ↓
                                    Database
                                        ↓
                                   JSON Response

API Example:
GET /api/translations?locale=en
→ Returns JSON with all translations

Blade Integration:
@php
    $text = \App\Models\Translation::getByKey('hero.title', 'en');
@endphp
<h1>{{ $text }}</h1>


┌─────────────────────────────────────────────────────────────────┐
│                      SYSTEM DEPENDENCIES                         │
└─────────────────────────────────────────────────────────────────┘

Backend Dependencies:
• PHP 8.1+
• Laravel 11.x
• MySQL/PostgreSQL/SQLite

Frontend Dependencies:
• Tailwind CSS (CDN)
• Vanilla JavaScript (no libraries)

Browser Requirements:
• Modern browsers (Chrome, Firefox, Safari, Edge)
• JavaScript enabled
• CSS3 support

No Additional Packages Required:
✓ Uses Laravel built-in features
✓ No third-party packages
✓ Minimal external dependencies


┌─────────────────────────────────────────────────────────────────┐
│                        SCALABILITY                               │
└─────────────────────────────────────────────────────────────────┘

Current Capacity:
• Handles 1000s of translations
• Pagination prevents memory issues
• Database indexes for fast queries

Future Scaling Options:
1. Database Optimization
   • Add more indexes
   • Partition large tables
   • Use read replicas

2. Caching Layer
   • Cache frequently accessed translations
   • Redis/Memcached integration
   • Cache invalidation strategy

3. Load Balancing
   • Multiple app servers
   • Database connection pooling
   • CDN for static assets

4. API Rate Limiting
   • Throttle requests
   • Queue import jobs
   • Background processing


┌─────────────────────────────────────────────────────────────────┐
│                    MAINTENANCE & MONITORING                      │
└─────────────────────────────────────────────────────────────────┘

Maintenance Tasks:
• Regular database backups
• Export translations (backup)
• Monitor database size
• Check error logs
• Update dependencies

Monitoring Points:
• Translation creation rate
• Search query performance
• Import/export usage
• Database query times
• User access patterns

Health Checks:
• Database connectivity
• Migration status
• Seeder execution
• Route availability
• View rendering


This architecture supports a robust, scalable, and maintainable
translation management system that can grow with your application.

---

Created: 2025-10-11
Version: 1.0.0
