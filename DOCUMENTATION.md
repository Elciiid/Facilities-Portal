# La Rose Noire Facilities Portal Documentation

## 1. Overview
The **La Rose Noire Facilities Portal** is a centralized web application designed to streamline access to various facility management tools and applications. It features a modern, responsive user interface with a public-facing portal for employees and a secure admin panel for management.

---

## 2. User Portal Guide

### 2.1 Dashboard
The main interface presents a grid of applications.
- **Application Grid**: Apps are displayed as cards with icons and descriptions.
- **Grid Density**: Users can toggle between 3, 5, or 8 columns per row using the buttons at the top right of the grid section.
- **Folders**: Related applications can be grouped into folders. Clicking a folder opens an overlay displaying its contents.

### 2.2 Navigation & Sidebar
- **Left Sidebar**:
  - **Company Branding**: Displays the LRN logo.
  - **Weather Widget**: Real-time weather updates for Mabalacat City.
  - **Admin Login**: Quick access to the administrative login page.
  - **Announcements**: Displays important notices and images managed by admins.
- **Right Sidebar**:
  - **Calendar**: A monthly calendar view.
  - **Holidays**: A list of upcoming holidays.

### 2.3 Announcement Banner
A dismissible scrolling banner at the top of the screen displays critical alerts or welcome messages.

---

## 3. Admin Portal Guide

### 3.1 Access
- **URL**: `/admin/admin_login.php`
- **Authentication**: Requires a valid administrator username and password.
- **Session**: Sessions expire after 24 hours of inactivity.

### 3.2 Management Modules
The admin dashboard is divided into four main tabs:

#### **Announcements**
Control the top banner notification.
- **Toggle**: Enable or disable the banner visibility.
- **Content**: Edit the title and message text.
- **Save**: Persist changes immediately.

#### **Left Panel**
Manage the content of the user portal's left sidebar.
- **General Settings**:
  - Toggle **Weather Widget**.
  - Toggle **2D Floating Background** animation.
- **Panel Announcements**:
  - Add **Text** or **Image** announcements.
  - Upload images (JPG, PNG, GIF, max 5MB).
  - Edit or delete existing side announcements.

#### **Apps**
Manage the applications displayed on the main grid.
- **Add App**: create a new application entry.
  - **Title**: Display name.
  - **Folder**: Assign to an existing folder or create a new one.
  - **Icon**: Select from a library of FontAwesome icons.
  - **Color**: Choose a theme color (Pink, Rose, Blue, Green, etc.).
  - **Link**: The URL where the app redirects (internal or external).
  - **Description**: A brief summary of the tool.
- **Edit/Delete**: Modify or remove existing apps.
- **Toggle**: Quickly enable/disable an app without deleting it.

#### **Folders**
Organize apps into logical groups.
- **Create**: Add a new folder.
- **Manage**: Rename folders or toggle their visibility.
- **App Management**: View, add, or remove apps directly from the folder edit view.

---

## 4. Technical Documentation

### 4.1 System Architecture
- **Frontend**: HTML5, TailwindCSS (CDN), Vanilla JavaScript.
- **Backend**: Native PHP.
- **Data Storage**: JSON flat files (No SQL database required for portal configuration).
- **Libraries**:
  - `FontAwesome 6.0`: Icons.
  - `GSAP 3.12`: Animations.
  - `Three.js`: 3D effects (referenced in head).

### 4.2 File Structure
```
/
├── portal.php               # Main user entry point
├── DOCUMENTATION.md         # This documentation
├── admin/
│   ├── admin.php            # Main admin dashboard
│   ├── admin_login.php      # Login page
│   ├── admin_auth.php       # Authentication handler
│   └── admin_logout.php     # Session destroyer
├── api/                     # PHP endpoints for data manipulation
│   ├── save_app.php         # Create/Update apps
│   ├── delete_app.php       # Delete apps
│   ├── save_folder.php      # Create/Update folders
│   ├── delete_folder.php    # Delete folders
│   ├── save_announcement.php # Update top banner
│   ├── upload_announcement.php # Handle sidebar image uploads
│   ├── toggle_announcement.php # Enable/Disable sidebar items
│   └── save_weather.php     # Toggle weather/background settings
├── data/                    # JSON data stores
│   ├── apps.json            # Array of app objects
│   ├── folders.json         # Array of folder objects
│   ├── announcements.json   # Top banner configuration
│   └── left_panel.json      # Sidebar settings and items
└── assets/                  # Static images and resources
```

### 4.3 Data Models

#### **App Object (`apps.json`)**
```json
{
  "id": "app_123456789",
  "title": "IT Helpdesk",
  "folder": "Support",
  "description": "Submit ticket requests",
  "icon": "fa-headset",
  "color": "blue",
  "link": "../support/index.php",
  "enabled": true,
  "order": 999
}
```

#### **Folder Object (`folders.json`)**
```json
{
  "name": "Support",
  "enabled": true
}
```

#### **Sidebar Announcement (`left_panel.json`)**
```json
{
  "id": "ann_123",
  "type": "image",
  "title": "Event",
  "subtitle": "Join us",
  "image": "uploads/image.jpg",
  "enabled": true
}
```

### 4.4 Setup & Installation
1.  **Requirements**: PHP 7.4+ enviornment (e.g., XAMPP, Apache).
2.  **Permissions**: Ensure the `data/` and `uploads/` directories are writable by the web server.
3.  **Deployment**:
    - Place the project folder in the web root (e.g., `htdocs`).
    - Access via `http://localhost/portal-lrn-folder/portal.php`.

### 4.5 Troubleshooting
-   **Changes not saving**: Check file permissions on `data/*.json`.
-   **Images not showing**: Ensure `uploads/` folder exists and is writable. Check console for 404 errors.
-   **Login fails**: Verify credentials in `admin_auth.php` (if hardcoded) or database connection.

---
*Last Updated: February 2026*
