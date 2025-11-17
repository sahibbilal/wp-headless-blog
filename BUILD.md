# Build Instructions

## Quick Start

1. **Navigate to plugin directory:**
   ```bash
   cd wp-content/plugins/wp-headless-blog
   ```

2. **Install dependencies:**
   ```bash
   npm install
   ```

3. **Build for production:**
   ```bash
   npm run build
   ```

4. **Activate the plugin in WordPress:**
   - Go to WordPress Admin → Plugins
   - Find "WordPress Headless Blog"
   - Click "Activate"

5. **Flush rewrite rules:**
   - Go to Settings → Permalinks
   - Click "Save Changes" (no need to change anything)

## Development

For development with hot-reload:

```bash
npm run dev
```

**Note:** The dev server runs on `http://localhost:3000`. For WordPress integration during development, you'll need to:

1. Build the assets first: `npm run build`
2. Or configure Vite proxy to point to your WordPress installation

## Build Output

After running `npm run build`, the following files will be generated in `assets/dist/`:

- `manifest.json` - Vite build manifest
- `js/main.js` - Main JavaScript bundle
- `css/style.css` - Compiled CSS with TailwindCSS
- Additional chunk files as needed

## Troubleshooting

### Build fails

- Ensure Node.js 16+ is installed
- Delete `node_modules` and `package-lock.json`, then run `npm install` again
- Check that all dependencies in `package.json` are compatible

### Assets not loading in WordPress

1. Verify `assets/dist/manifest.json` exists
2. Check file permissions on `assets/dist/` directory
3. Clear WordPress cache if using a caching plugin
4. Check browser console for 404 errors

### Route not working

1. Go to Settings → Permalinks
2. Click "Save Changes" to flush rewrite rules
3. Check `.htaccess` file permissions (Apache) or server configuration (Nginx)

## Production Deployment

1. Build the assets: `npm run build`
2. Ensure `assets/dist/` directory is included in your deployment
3. Activate the plugin on the production server
4. Flush rewrite rules

## File Structure After Build

```
wp-headless-blog/
├── assets/
│   └── dist/
│       ├── manifest.json
│       ├── js/
│       │   └── main.js
│       └── css/
│           └── style.css
└── ...
```

