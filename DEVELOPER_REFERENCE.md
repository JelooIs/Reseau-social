# Background Customization - Quick Reference

## 🎯 Quick Start

### Access the Feature
- **URL**: `index.php?action=settings`
- **Navbar**: Click "⚙️ Paramètres" button
- **Requires**: User to be logged in

### Three Theme Options
1. **Light** - Classic white (default)
2. **Dark** - Dark mode for comfort
3. **Custom** - Upload your own image

---

## 📁 File Structure

```
ReseauSocial/
├── migrations/
│   └── 006_create_user_preferences_table.sql
├── models/
│   └── UserPreferences.php
├── controllers/
│   └── SettingsController.php
├── views/
│   └── settings.view.php
├── assets/css/
│   └── style.css (updated)
└── uploads/
    └── backgrounds/
```

---

## 🔧 Key Classes/Methods

### UserPreferences Model
```php
// Get preferences for user (auto-creates defaults)
$prefs = new UserPreferences();
$preferences = $prefs->getPreferences($user_id);

// Change theme
$prefs->setBackgroundMode($user_id, 'dark');

// Upload custom background
$prefs->setCustomBackgroundImage($user_id, 'path/to/image.jpg');

// Delete custom background
$prefs->deleteCustomBackgroundImage($user_id);
```

### SettingsController
- Handles POST requests for theme changes
- Validates image uploads (type, size)
- Manages file uploads/deletions
- Updates session with new preferences

---

## 🎨 CSS Classes

### Body Classes
```php
// Light theme
<body class="bg-light">

// Dark theme
<body class="bg-dark">

// Custom background
<body class="bg-custom" style="background-image: url(...)">
```

### Component Colors

| Element | Light | Dark |
|---------|-------|------|
| Background | #f8f9fa | #1a1a1a |
| Text | #212529 | #e0e0e0 |
| Cards | #ffffff | #2d2d2d |
| Borders | #dee2e6 | #444444 |

---

## 🌐 Session Variables

```php
// Preferences loaded into session
$_SESSION['user_preferences'] = [
    'id' => 1,
    'user_id' => 5,
    'background_mode' => 'dark',
    'custom_background_image' => 'uploads/backgrounds/bg_5_123abc.jpg',
    'created_at' => '2025-11-27 10:00:00',
    'updated_at' => '2025-11-27 12:00:00'
];
```

---

## 📤 Image Upload Details

### Upload Process
1. User selects file
2. Validated by SettingsController
3. Saved to `uploads/backgrounds/`
4. Path stored in database
5. Old image deleted

### Validation Rules
- **Types**: JPG, PNG, GIF, WebP
- **Size**: Max 5MB
- **Naming**: `bg_{user_id}_{uniqid}.{ext}`

---

## 🔌 Integration Points

### In index.php
```php
// Load preferences on session start
if (isset($_SESSION['user']) && !isset($_SESSION['user_preferences'])) {
    require_once 'models/UserPreferences.php';
    $prefsModel = new UserPreferences();
    $_SESSION['user_preferences'] = $prefsModel->getPreferences($_SESSION['user']['id']);
}

// Add route
if (isset($_GET['action']) && $_GET['action'] === 'settings') {
    $controller = new SettingsController();
    $controller->settings();
    exit();
}
```

### In Every View
```php
<body class="<?= isset($_SESSION['user_preferences']) ? 
    'bg-' . htmlspecialchars($_SESSION['user_preferences']['background_mode'], ENT_QUOTES, 'UTF-8') : 
    'bg-light' ?>"
    <?php if (isset($_SESSION['user_preferences']) && 
        $_SESSION['user_preferences']['background_mode'] === 'custom' && 
        !empty($_SESSION['user_preferences']['custom_background_image'])): ?>
    style="background-image: url('<?= htmlspecialchars($_SESSION['user_preferences']['custom_background_image'], ENT_QUOTES, 'UTF-8') ?>'); 
           background-size: cover; 
           background-attachment: fixed; 
           background-position: center;"
    <?php endif; ?>>
```

### In Navigation
```php
<a class="btn btn-outline-secondary btn-sm btn-spacing" 
   href="index.php?action=settings">⚙️ Paramètres</a>
```

---

## 🐛 Debugging

### Check Database
```sql
SELECT * FROM user_preferences WHERE user_id = 5;
```

### Check Session
```php
echo '<pre>' . print_r($_SESSION['user_preferences'], true) . '</pre>';
```

### Common Issues
- No preferences loaded: Clear browser cache, re-login
- Image not showing: Check file path, verify image exists
- Theme not applying: Check session is set, verify CSS loaded

---

## ✅ Validation Rules

### Image Upload
```php
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$max_size = 5242880; // 5MB
if (!in_array($file['type'], $allowed_types)) {
    // Error: Invalid type
}
if ($file['size'] > $max_size) {
    // Error: Too large
}
```

### Theme Selection
```php
$allowed_modes = ['light', 'dark', 'custom'];
if (in_array($_POST['background_mode'], $allowed_modes)) {
    // Valid theme
}
```

---

## 📊 Database Queries

### Get User Preferences
```sql
SELECT * FROM user_preferences WHERE user_id = ?;
```

### Insert Default Preferences
```sql
INSERT INTO user_preferences (user_id, background_mode) 
VALUES (?, 'light');
```

### Update Theme
```sql
UPDATE user_preferences 
SET background_mode = ? 
WHERE user_id = ?;
```

### Update Custom Background
```sql
UPDATE user_preferences 
SET custom_background_image = ?, background_mode = 'custom' 
WHERE user_id = ?;
```

---

## 🎬 User Workflow

```
1. User logs in
   ↓ Preferences auto-loaded to $_SESSION
2. User clicks ⚙️ Settings
   ↓ Redirected to /views/settings.view.php
3. User selects theme or uploads image
   ↓ Form submitted to SettingsController
4. Controller validates and saves
   ↓ Database updated, session refreshed
5. User redirected back to settings
   ↓ Flash message shows success
6. Theme applied to all pages
   ↓ Body class updated to selected theme
```

---

## 🚀 Performance Notes

- **No database hit per page**: Preferences cached in session
- **CSS-based**: No runtime processing
- **Lazy images**: Browser handles background loading
- **Session key**: `user_preferences` (simple lookup)

---

## 🔐 Security Notes

- HTML escaping: All output with ENT_QUOTES, UTF-8
- File validation: Type and size checks
- Database: Prepared statements (no SQL injection)
- User isolation: Each user has unique preferences
- Cleanup: Old images deleted to prevent bloat

---

## 📱 Responsive Breakpoints

```css
/* Large screens */
.col-md-4 { /* 3 columns */ }

/* Medium screens */
@media (max-width: 768px) { 
    /* Stack to 2 columns */ 
}

/* Small screens */
@media (max-width: 576px) { 
    /* Stack to 1 column */ 
}
```

---

## 💾 File Locations

| File | Location | Purpose |
|------|----------|---------|
| Uploaded Images | `uploads/backgrounds/` | Store user images |
| Settings Page | `views/settings.view.php` | UI for theme selection |
| Theme Styles | `assets/css/style.css` | CSS for all themes |
| Model | `models/UserPreferences.php` | Data access layer |
| Controller | `controllers/SettingsController.php` | Business logic |
| Migration | `migrations/006_...sql` | Database schema |

---

## 🔗 Related Code

- **User Model**: `models/User.php` (stores user data)
- **Session**: `index.php` (manages $_SESSION)
- **Navigation**: `views/_nav.php` (Settings button)
- **CSS**: `assets/css/style.css` (theme definitions)

---

## 📞 Quick Help

**Q: How do I add a new theme?**
A: Add CSS class to style.css (e.g., `.bg-sepia`) and option to settings page

**Q: How do I change file upload size?**
A: Edit `SettingsController.php` line with `5242880` (5MB in bytes)

**Q: How do I delete old images?**
A: `unlink()` function already handles this automatically

**Q: How do I modify dark theme colors?**
A: Edit `.bg-dark` section in `assets/css/style.css`

---

**Last Updated**: November 27, 2025
**Version**: 1.0
**Status**: Production Ready
