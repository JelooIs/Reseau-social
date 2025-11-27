# Background Customization Feature - Implementation Summary

## Overview
Users can now customize the background of the entire site with three options:
1. **Light Mode** - Classic white background (default)
2. **Dark Mode** - Dark background for comfortable viewing
3. **Custom** - User's own background image

## Files Created

### 1. Database Migration
- **File**: `migrations/006_create_user_preferences_table.sql`
- **Purpose**: Creates `user_preferences` table to store user theme preferences
- **Columns**: 
  - `user_id` (UNIQUE, FK to users.id)
  - `background_mode` (ENUM: light/dark/custom)
  - `custom_background_image` (VARCHAR for image path)

### 2. Model
- **File**: `models/UserPreferences.php`
- **Methods**:
  - `getPreferences($user_id)` - Get user preferences with default fallback
  - `createDefaultPreferences($user_id)` - Create default prefs for new users
  - `setBackgroundMode($user_id, $mode)` - Change theme
  - `setCustomBackgroundImage($user_id, $image_path)` - Upload custom image
  - `deleteCustomBackgroundImage($user_id)` - Remove custom image

### 3. Controller
- **File**: `controllers/SettingsController.php`
- **Features**:
  - Handle theme selection (light/dark)
  - Process image uploads (max 5MB, supports JPG/PNG/GIF/WebP)
  - Delete custom backgrounds
  - Automatic cleanup of old images

### 4. View
- **File**: `views/settings.view.php`
- **UI Elements**:
  - Three theme cards (Light/Dark/Custom) for easy selection
  - Image upload form with drag-and-drop support
  - Preview of current custom background
  - Delete button for custom images

## CSS Enhancements
**File**: `assets/css/style.css`

### New Theme Classes
- `.bg-light` - Light theme with proper text contrast
- `.bg-dark` - Dark theme with custom color scheme for all elements
- `.bg-custom` - Custom image with semi-transparent overlay for readability

### Dark Theme Features
- Dark card backgrounds (#2d2d2d)
- Light text (#e0e0e0)
- Custom form input styling
- Modal and table styling
- Proper contrast for accessibility

### Custom Image Features
- Semi-transparent overlay (50% black) for text readability
- Blur effect for cards (backdrop-filter)
- Fixed background attachment
- Proper z-index management

## Integration Points

### 1. index.php Updates
- Load UserPreferences model on session start
- Store preferences in `$_SESSION['user_preferences']`
- Add SettingsController route handler

### 2. Views Updated (Dynamic Background Application)
- `index.view.php`
- `subject_detail.view.php`
- `subjects.view.php`
- `pm_inbox.view.php`
- `admin.view.php`
- `reports.view.php`

Each view now includes:
```php
<body class="<?= isset($_SESSION['user_preferences']) ? 'bg-' . htmlspecialchars($_SESSION['user_preferences']['background_mode'], ENT_QUOTES, 'UTF-8') : 'bg-light' ?>" 
      <?php if (isset($_SESSION['user_preferences']) && $_SESSION['user_preferences']['background_mode'] === 'custom' && !empty($_SESSION['user_preferences']['custom_background_image'])): ?>
      style="background-image: url('<?= htmlspecialchars($_SESSION['user_preferences']['custom_background_image'], ENT_QUOTES, 'UTF-8') ?>'); background-size: cover; background-attachment: fixed; background-position: center;"
      <?php endif; ?>>
```

### 3. Navigation Bar
- Added ⚙️ Settings button to navbar
- Link: `index.php?action=settings`
- Accessible to all authenticated users

## User Flow

1. User clicks "⚙️ Paramètres" in navbar
2. Redirected to settings page (`index.php?action=settings`)
3. User chooses:
   - Light/Dark theme via radio buttons → submits → theme applied
   - Custom image via file upload → processes → applies theme
4. Preferences saved to database
5. Loaded automatically on every page for persistent experience

## Image Upload Details
- **Location**: `uploads/backgrounds/` (auto-created)
- **Naming**: `bg_{user_id}_{uniqid}.{extension}`
- **Max Size**: 5MB
- **Allowed Types**: JPG, PNG, GIF, WebP
- **Old Images**: Automatically deleted on new upload

## Special Features
- **Auto-default creation**: New users automatically get light theme
- **Persistent**: Settings saved and loaded across all pages
- **Session-based**: Preferences loaded into session for performance
- **Image cleanup**: Old custom images deleted to prevent storage bloat
- **Responsive**: Works on all screen sizes
- **Accessible**: Proper contrast ratios for all themes

## Browser Compatibility
- Modern browsers supporting CSS variables and backdrop-filter
- Graceful degradation for older browsers (defaults to light theme)
- No JavaScript required for theme switching

## Future Enhancements (Optional)
- Additional color themes (Sepia, Blue, etc.)
- Theme scheduling (light during day, dark at night)
- Per-page theme override
- Social theme sharing
- Community theme templates
