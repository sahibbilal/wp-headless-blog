=== WordPress Headless Blog ===
Contributors: yourname
Tags: headless, vue, rest-api, jwt
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A fully headless WordPress blog system with embedded Vue.js frontend. WordPress acts as backend API only, with Vue.js bundled inside the plugin.

== Description ==

WordPress Headless Blog is a complete headless CMS solution packaged as a single WordPress plugin. It provides:

* Vue.js 3 frontend bundled inside the plugin
* JWT authentication for secure API access
* REST API integration
* Public blog frontend at /wp-headless-blog
* Admin dashboard accessible from WordPress admin
* Infinite scroll, search, and category filtering
* Protected routes and user authentication

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/wp-headless-blog` directory
2. Navigate to the plugin directory: `cd wp-content/plugins/wp-headless-blog`
3. Install dependencies: `npm install`
4. Build the Vue.js frontend: `npm run build`
5. Activate the plugin through the 'Plugins' menu in WordPress
6. Visit Settings > Permalinks and click "Save Changes" to flush rewrite rules

== Frequently Asked Questions ==

= How do I build the frontend? =

Navigate to the plugin directory and run:
```
npm install
npm run build
```

= Where is the frontend accessible? =

The public blog is available at: `yoursite.com/wp-headless-blog`
The admin dashboard is available in WordPress admin under "Headless Blog" menu.

= How do I configure JWT authentication? =

JWT authentication is automatically configured when the plugin is activated. The secret key is generated automatically and stored in WordPress options.

= Can I customize the Vue.js frontend? =

Yes! Edit the files in `assets/src/` and rebuild with `npm run build`.

== Changelog ==

= 1.0.0 =
* Initial release
* Vue.js 3 frontend with Vite
* JWT authentication
* REST API integration
* Public blog frontend
* Admin dashboard
* Search and filtering
* Infinite scroll

== Upgrade Notice ==

= 1.0.0 =
Initial release of WordPress Headless Blog plugin.

