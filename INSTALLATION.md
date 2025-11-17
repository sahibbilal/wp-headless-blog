# Installation Guide

## Prerequisites

- WordPress 5.8 or higher
- PHP 8.0 or higher
- Node.js 16+ and npm (for building frontend)
- WordPress REST API enabled (default)

## Step-by-Step Installation

### 1. Upload Plugin Files

Copy the entire `wp-headless-blog` folder to your WordPress plugins directory:

```
wp-content/plugins/wp-headless-blog/
```

### 2. Install Node.js Dependencies

Open a terminal/command prompt and navigate to the plugin directory:

```bash
cd wp-content/plugins/wp-headless-blog
npm install
```

This will install:
- Vue.js 3
- Vite (build tool)
- TailwindCSS
- Vue Router
- Axios
- And other dependencies

### 3. Build the Frontend

Build the Vue.js application:

```bash
npm run build
```

This creates the `assets/dist/` directory with compiled JavaScript and CSS files.

**Important:** The plugin will not work until you build the frontend!

### 4. Activate the Plugin

1. Log in to your WordPress admin panel
2. Navigate to **Plugins → Installed Plugins**
3. Find **"WordPress Headless Blog"**
4. Click **"Activate"**

### 5. Flush Rewrite Rules

After activation, you must flush rewrite rules:

1. Go to **Settings → Permalinks**
2. Click **"Save Changes"** (you don't need to change anything)

This registers the custom route `/wp-headless-blog`.

### 6. Verify Installation

#### Public Blog Frontend

Visit: `https://yoursite.com/wp-headless-blog`

You should see the Vue.js blog interface.

#### Admin Dashboard

1. Go to WordPress Admin
2. Click **"Headless Blog"** in the admin menu
3. The Vue.js app should load inside the admin panel

## Post-Installation Configuration

### User Roles and Permissions

By default, the following roles can access the admin dashboard:
- Administrator
- Editor
- Author
- Contributor
- Subscriber

To customize permissions, edit `includes/class-capabilities.php` and modify the `add_capabilities()` method.

### JWT Authentication

JWT authentication is automatically configured when the plugin is activated. A secret key is generated and stored in WordPress options.

To regenerate the JWT secret key:
1. Delete the option `wp_headless_blog_jwt_secret` from the database
2. Deactivate and reactivate the plugin

## Troubleshooting

### "Assets not loading" Error

**Solution:**
1. Ensure you've run `npm run build`
2. Check that `assets/dist/manifest.json` exists
3. Verify file permissions on the `assets/dist/` directory
4. Clear any caching plugins

### "Page not found" on `/wp-headless-blog`

**Solution:**
1. Go to Settings → Permalinks
2. Click "Save Changes" to flush rewrite rules
3. If using Apache, check `.htaccess` file permissions
4. If using Nginx, ensure rewrite rules are configured

### "You do not have sufficient permissions" Error

**Solution:**
1. Ensure your user role has the `access_headless_blog` capability
2. Deactivate and reactivate the plugin to regenerate capabilities
3. Check user role in WordPress admin

### Build Errors

**Solution:**
1. Ensure Node.js 16+ is installed: `node --version`
2. Delete `node_modules` folder and `package-lock.json`
3. Run `npm install` again
4. Check for error messages in the terminal

## Development Setup

For development with hot-reload:

```bash
npm run dev
```

The dev server will start on `http://localhost:3000`.

**Note:** For WordPress integration during development, you'll need to build the assets (`npm run build`) or configure Vite to proxy to your WordPress installation.

## Updating the Plugin

1. Replace plugin files with new version
2. Run `npm install` (if dependencies changed)
3. Run `npm run build` to rebuild frontend
4. Clear WordPress cache if using caching plugin

## Uninstallation

1. Deactivate the plugin in WordPress admin
2. Delete the plugin folder: `wp-content/plugins/wp-headless-blog/`
3. (Optional) Delete the JWT secret option from database: `wp_headless_blog_jwt_secret`

## Support

For issues or questions:
1. Check the README.md file
2. Review BUILD.md for build-related issues
3. Check WordPress debug log if WP_DEBUG is enabled

