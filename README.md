# WordPress Headless Blog Plugin

A fully headless WordPress blog system with embedded Vue.js frontend. This plugin transforms WordPress into a headless CMS backend while providing a modern Vue.js frontend bundled directly inside the plugin.

## 🚀 Features

- **Vue.js 3 Frontend** - Modern, reactive UI built with Vue 3 and Vite
- **JWT Authentication** - Secure token-based authentication
- **REST API Integration** - Full WordPress REST API support
- **Public Blog Route** - Accessible at `/wp-headless-blog`
- **Admin Dashboard** - Integrated into WordPress admin panel
- **Infinite Scroll** - Smooth pagination with infinite scroll
- **Search & Filters** - Real-time search and category filtering
- **Protected Routes** - Authentication-required pages
- **TailwindCSS** - Beautiful, responsive design

## 📁 Project Structure

```
wp-headless-blog/
├── headless-blog.php          # Main plugin file
├── package.json               # Node.js dependencies
├── vite.config.js            # Vite build configuration
├── tailwind.config.js        # TailwindCSS configuration
├── includes/
│   ├── class-jwt-auth.php    # JWT authentication handler
│   ├── class-rest-api.php    # REST API endpoints
│   └── class-capabilities.php # User capabilities
└── assets/
    ├── dist/                 # Built files (generated)
    └── src/                  # Vue.js source files
        ├── main.js           # Vue app entry point
        ├── App.vue           # Root component
        ├── pages/            # Page components
        ├── components/       # Reusable components
        ├── services/         # API services
        └── composables/      # Vue composables
```

## 🛠️ Installation

### 1. Install Plugin Files

Upload the plugin to your WordPress installation:
```
wp-content/plugins/wp-headless-blog/
```

### 2. Install Dependencies

Navigate to the plugin directory and install Node.js dependencies:

```bash
cd wp-content/plugins/wp-headless-blog
npm install
```

### 3. Build Frontend

Build the Vue.js frontend:

```bash
npm run build
```

This will create the `assets/dist/` directory with compiled JavaScript and CSS files.

### 4. Activate Plugin

1. Go to WordPress Admin → Plugins
2. Find "WordPress Headless Blog"
3. Click "Activate"

### 5. Flush Rewrite Rules

1. Go to Settings → Permalinks
2. Click "Save Changes" (no need to change anything)

This ensures the custom route `/wp-headless-blog` is registered.

## 🎯 Usage

### Public Blog Frontend

Visit your site at:
```
https://yoursite.com/wp-headless-blog
```

### Admin Dashboard

1. Log in to WordPress admin
2. Click "Headless Blog" in the admin menu
3. The Vue.js app will load inside the admin panel

### Development Mode

For development with hot-reload:

```bash
npm run dev
```

Then access the frontend at `http://localhost:3000` (or the port shown in terminal).

**Note:** For development, you'll need to configure Vite proxy or use the built files in WordPress.

## 🔐 Authentication

### Login

Users can log in through the `/wp-headless-blog/login` route. The plugin uses JWT tokens stored in localStorage.

### JWT Endpoints

- **POST** `/wp-json/wp-headless-blog/v1/auth/token` - Generate token
- **GET** `/wp-json/wp-headless-blog/v1/auth/validate` - Validate token
- **GET** `/wp-json/wp-headless-blog/v1/auth/me` - Get current user

### Using JWT Token

Include the token in API requests:

```javascript
headers: {
  'Authorization': 'Bearer YOUR_TOKEN_HERE'
}
```

## 🎨 Customization

### Styling

Edit `tailwind.config.js` to customize the design system.

### Components

All Vue components are in `assets/src/`:
- `pages/` - Page components (BlogList, SinglePost, Login, Dashboard)
- `components/` - Reusable components (PostCard, SearchBox, CategoryFilter)

### API Configuration

The API base URL and endpoints are configured in:
- `assets/src/services/api.js`
- `assets/src/main.js` (via `wpHeadlessBlog` global object)

## 📝 Building for Production

```bash
npm run build
```

This will:
1. Compile Vue.js components
2. Bundle JavaScript with Vite
3. Process TailwindCSS
4. Generate manifest.json for WordPress asset loading
5. Output files to `assets/dist/`

## 🔧 Configuration

### User Roles

By default, all logged-in users can access the admin dashboard. To restrict access, edit `includes/class-capabilities.php` and modify the `add_capabilities()` method.

### JWT Secret Key

The JWT secret key is automatically generated on first activation and stored in WordPress options. To regenerate:

1. Delete the option `wp_headless_blog_jwt_secret` from the database
2. Reactivate the plugin

## 🐛 Troubleshooting

### Assets Not Loading

1. Ensure `npm run build` has been executed
2. Check that `assets/dist/manifest.json` exists
3. Verify file permissions on `assets/dist/`

### Route Not Working

1. Go to Settings → Permalinks
2. Click "Save Changes" to flush rewrite rules
3. Check `.htaccess` file permissions (if using Apache)

### JWT Authentication Failing

1. Verify the secret key exists in WordPress options
2. Check that tokens are being stored in localStorage
3. Review browser console for API errors

## 📚 API Reference

### WordPress REST API

The plugin uses standard WordPress REST API endpoints:

- `GET /wp-json/wp/v2/posts` - Get posts
- `GET /wp-json/wp/v2/posts/{id}` - Get single post
- `GET /wp-json/wp/v2/categories` - Get categories

### Custom Endpoints

- `POST /wp-json/wp-headless-blog/v1/auth/token` - Login
- `GET /wp-json/wp-headless-blog/v1/auth/me` - Current user
- `GET /wp-json/wp-headless-blog/v1/auth/validate` - Validate token

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

GPL v2 or later

## 🙏 Credits

Built with:
- Vue.js 3
- Vite
- TailwindCSS
- Axios
- Vue Router

