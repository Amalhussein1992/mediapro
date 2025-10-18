# Translation System - Quick Start Guide

## Setup (3 Steps)

```bash
# 1. Run migration
php artisan migrate

# 2. Seed initial translations
php artisan db:seed --class=TranslationSeeder

# 3. Access admin dashboard
# Navigate to: http://your-domain/admin/translations
```

## Access the System

1. Login to admin dashboard
2. Click "Translations" in the left sidebar (with language icon)
3. Start managing translations

## Quick Actions

### Create New Translation
1. Click "Create Translation" button (top right)
2. Fill in:
   - **Key:** `section.name` (e.g., `hero.title`)
   - **Group:** Select or create new (e.g., `hero`, `features`)
   - **English Value:** Your English text
   - **Arabic Value:** Your Arabic text
3. See live preview as you type
4. Click "Create Translation"

### Edit Translation
1. Click the edit icon (pencil) next to any translation
2. Modify values
3. Preview changes in real-time
4. Click "Update Translation"

### Search Translations
1. Use search bar at top
2. Searches across keys and values
3. Click "Clear" to reset

### Filter by Group
1. Click any group tab (All, Hero, Features, etc.)
2. See only translations in that group

### Export Translations
1. Click "Export" button
2. Choose "Export English" or "Export Arabic"
3. Downloads JSON file

### Import Translations
1. Click "Import" button
2. Select JSON file
3. Click "Import"
4. Existing translations will be updated, new ones created

## Use in Code

### In Controllers
```php
use App\Models\Translation;

// Get single translation
$text = Translation::getByKey('hero.title', 'en');

// Get all translations in a group
$heroTexts = Translation::getByGroup('hero', 'en');
```

### In Blade Views
```blade
@php
    $locale = app()->getLocale(); // 'en' or 'ar'
    $title = \App\Models\Translation::getByKey('hero.title', $locale);
@endphp

<h1>{{ $title }}</h1>
```

### In API/Frontend
```php
// In your controller
public function getTranslations($locale = 'en')
{
    $translations = Translation::all();
    $result = [];

    foreach ($translations as $translation) {
        $result[$translation->key] = $locale === 'ar'
            ? $translation->value_ar
            : $translation->value_en;
    }

    return response()->json($result);
}
```

## Pre-loaded Translation Keys

### Hero Section
- `hero.title`
- `hero.subtitle`
- `hero.cta_primary`
- `hero.cta_secondary`

### Features Section
- `features.title`
- `features.subtitle`
- `features.scheduling.title`
- `features.scheduling.description`
- `features.analytics.title`
- `features.analytics.description`
- (and more...)

### Common Elements
- `common.login`
- `common.register`
- `common.dashboard`
- `common.save`
- `common.cancel`
- `common.delete`
- `common.edit`
- `common.search`

### Footer
- `footer.company.title`
- `footer.company.about`
- `footer.product.title`
- `footer.support.title`
- `footer.copyright`
- (and more...)

## JSON Export Format

```json
{
  "hero": {
    "hero.title": "Your English Text"
  },
  "features": {
    "features.title": "Your English Text"
  }
}
```

## JSON Import Format

```json
{
  "hero": {
    "hero.welcome": {
      "en": "Welcome to our platform",
      "ar": "مرحبا بكم في منصتنا"
    }
  },
  "features": {
    "features.new": {
      "en": "New Feature",
      "ar": "ميزة جديدة"
    }
  }
}
```

## Tips

1. Use dot notation for keys: `section.subsection.item`
2. Group related translations together
3. Always provide both English and Arabic values
4. Export translations before major changes (backup)
5. Test Arabic display (RTL layout)
6. Use descriptive key names

## Common Operations

### Bulk Operations
Currently, you can:
- Import multiple translations at once via JSON
- Export all translations as JSON
- Search and filter to find specific translations

### Translation Workflow
1. Create translations in admin panel
2. Export to JSON
3. Share with translators
4. Import updated translations
5. Deploy to production

## Need Help?

- Check the full documentation: `TRANSLATION_SYSTEM_SETUP.md`
- All routes are under `/admin/translations`
- All features require authentication
- UI matches existing admin design

---

For detailed documentation, see: TRANSLATION_SYSTEM_SETUP.md
