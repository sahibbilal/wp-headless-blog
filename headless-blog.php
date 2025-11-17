<?php
/**
 * Plugin Name: WordPress Headless Blog
 * Plugin URI: https://example.com/wp-headless-blog
 * Description: A fully headless WordPress blog system with embedded Vue.js frontend. WordPress acts as backend API only, with Vue.js bundled inside the plugin.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-headless-blog
 * Requires at least: 5.8
 * Requires PHP: 8.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'WP_HEADLESS_BLOG_VERSION', '1.0.0' );
define( 'WP_HEADLESS_BLOG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WP_HEADLESS_BLOG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_HEADLESS_BLOG_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Main plugin class
 */
class WP_Headless_Blog {
	
	/**
	 * Single instance of the class
	 */
	private static $instance = null;
	
	/**
	 * Get singleton instance
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * Constructor
	 */
	private function __construct() {
		$this->init();
	}
	
	/**
	 * Initialize plugin
	 */
	private function init() {
		// Load required files
		$this->load_dependencies();
		
		// Register activation/deactivation hooks
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
		
		// Initialize components
		add_action( 'init', array( $this, 'add_rewrite_rules' ) );
		add_action( 'template_redirect', array( $this, 'handle_frontend_route' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );
		
		// REST API enhancements
		add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
		
		// Initialize JWT Auth
		WP_Headless_Blog_JWT_Auth::get_instance();
	}
	
	/**
	 * Load plugin dependencies
	 */
	private function load_dependencies() {
		require_once WP_HEADLESS_BLOG_PLUGIN_DIR . 'includes/class-jwt-auth.php';
		require_once WP_HEADLESS_BLOG_PLUGIN_DIR . 'includes/class-rest-api.php';
		require_once WP_HEADLESS_BLOG_PLUGIN_DIR . 'includes/class-capabilities.php';
	}
	
	/**
	 * Plugin activation
	 */
	public function activate() {
		// Add rewrite rules
		$this->add_rewrite_rules();
		flush_rewrite_rules();
		
		// Create custom capabilities
		WP_Headless_Blog_Capabilities::add_capabilities();
	}
	
	/**
	 * Plugin deactivation
	 */
	public function deactivate() {
		flush_rewrite_rules();
	}
	
	/**
	 * Add custom rewrite rules
	 */
	public function add_rewrite_rules() {
		add_rewrite_rule(
			'^wp-headless-blog/?$',
			'index.php?headless_blog=1',
			'top'
		);
		
		add_rewrite_rule(
			'^wp-headless-blog/([^/]+)/?$',
			'index.php?headless_blog=1&headless_blog_route=$matches[1]',
			'top'
		);
		
		// Add query vars
		add_filter( 'query_vars', function( $vars ) {
			$vars[] = 'headless_blog';
			$vars[] = 'headless_blog_route';
			return $vars;
		} );
	}
	
	/**
	 * Handle frontend route
	 */
	public function handle_frontend_route() {
		$is_headless_blog = get_query_var( 'headless_blog' );
		
		if ( $is_headless_blog ) {
			// Load our custom template
			$this->load_frontend_template();
			exit;
		}
	}
	
	/**
	 * Load frontend template
	 */
	private function load_frontend_template() {
		// Set up basic HTML structure
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta charset="<?php bloginfo( 'charset' ); ?>">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<title><?php bloginfo( 'name' ); ?> - Headless Blog</title>
			<?php wp_head(); ?>
		</head>
		<body <?php body_class(); ?>>
			<div id="wp-headless-blog-app"></div>
			<?php
			// Enqueue Vue app assets
			$this->enqueue_frontend_assets();
			wp_footer();
			?>
		</body>
		</html>
		<?php
	}
	
	/**
	 * Enqueue frontend assets
	 */
	public function enqueue_frontend_assets() {
		// Only enqueue on our custom route
		if ( ! get_query_var( 'headless_blog' ) && ! is_admin() ) {
			return;
		}
		
		$dist_path = WP_HEADLESS_BLOG_PLUGIN_DIR . 'assets/dist/';
		$dist_url = WP_HEADLESS_BLOG_PLUGIN_URL . 'assets/dist/';
		
		// Check if built files exist
		if ( ! file_exists( $dist_path . 'manifest.json' ) ) {
			return;
		}
		
		$manifest = json_decode( file_get_contents( $dist_path . 'manifest.json' ), true );
		
		if ( ! $manifest ) {
			return;
		}
		
		// Find entry point (main.js)
		$main_entry = null;
		$css_files = array();
		
		foreach ( $manifest as $key => $entry ) {
			if ( strpos( $key, 'main.js' ) !== false && isset( $entry['isEntry'] ) && $entry['isEntry'] ) {
				$main_entry = $entry;
				// Get CSS files from entry
				if ( isset( $entry['css'] ) ) {
					$css_files = $entry['css'];
				}
				break;
			}
		}
		
		// Enqueue CSS files
		foreach ( $css_files as $css_file ) {
			wp_enqueue_style(
				'wp-headless-blog-style-' . md5( $css_file ),
				$dist_url . $css_file,
				array(),
				WP_HEADLESS_BLOG_VERSION
			);
		}
		
		// Enqueue JS
		if ( $main_entry && isset( $main_entry['file'] ) ) {
			wp_enqueue_script(
				'wp-headless-blog-app',
				$dist_url . $main_entry['file'],
				array(),
				WP_HEADLESS_BLOG_VERSION,
				true
			);
			
			// Localize script with WordPress data
			wp_localize_script( 'wp-headless-blog-app', 'wpHeadlessBlog', array(
				'apiUrl' => rest_url( 'wp/v2/' ),
				'nonce' => wp_create_nonce( 'wp_rest' ),
				'isAdmin' => is_admin(),
				'currentUser' => wp_get_current_user()->ID ? array(
					'id' => wp_get_current_user()->ID,
					'name' => wp_get_current_user()->display_name,
					'email' => wp_get_current_user()->user_email,
					'roles' => wp_get_current_user()->roles,
				) : null,
				'siteUrl' => home_url(),
				'jwtEndpoint' => rest_url( 'wp-headless-blog/v1/auth/token' ),
			) );
		}
	}
	
	/**
	 * Enqueue admin assets
	 */
	public function enqueue_admin_assets( $hook ) {
		// Only load on our admin page
		if ( 'toplevel_page_wp-headless-blog' !== $hook ) {
			return;
		}
		
		$dist_path = WP_HEADLESS_BLOG_PLUGIN_DIR . 'assets/dist/';
		$dist_url = WP_HEADLESS_BLOG_PLUGIN_URL . 'assets/dist/';
		
		// Check if built files exist
		if ( ! file_exists( $dist_path . 'manifest.json' ) ) {
			return;
		}
		
		$manifest = json_decode( file_get_contents( $dist_path . 'manifest.json' ), true );
		
		if ( ! $manifest ) {
			return;
		}
		
		// Find entry point (main.js)
		$main_entry = null;
		$css_files = array();
		
		foreach ( $manifest as $key => $entry ) {
			if ( strpos( $key, 'main.js' ) !== false && isset( $entry['isEntry'] ) && $entry['isEntry'] ) {
				$main_entry = $entry;
				// Get CSS files from entry
				if ( isset( $entry['css'] ) ) {
					$css_files = $entry['css'];
				}
				break;
			}
		}
		
		// Enqueue CSS files
		foreach ( $css_files as $css_file ) {
			wp_enqueue_style(
				'wp-headless-blog-admin-style-' . md5( $css_file ),
				$dist_url . $css_file,
				array(),
				WP_HEADLESS_BLOG_VERSION
			);
		}
		
		// Enqueue JS
		if ( $main_entry && isset( $main_entry['file'] ) ) {
			wp_enqueue_script(
				'wp-headless-blog-admin-app',
				$dist_url . $main_entry['file'],
				array(),
				WP_HEADLESS_BLOG_VERSION,
				true
			);
			
			// Localize script with WordPress data
			wp_localize_script( 'wp-headless-blog-admin-app', 'wpHeadlessBlog', array(
				'apiUrl' => rest_url( 'wp/v2/' ),
				'nonce' => wp_create_nonce( 'wp_rest' ),
				'isAdmin' => true,
				'currentUser' => wp_get_current_user()->ID ? array(
					'id' => wp_get_current_user()->ID,
					'name' => wp_get_current_user()->display_name,
					'email' => wp_get_current_user()->user_email,
					'roles' => wp_get_current_user()->roles,
				) : null,
				'siteUrl' => home_url(),
				'jwtEndpoint' => rest_url( 'wp-headless-blog/v1/auth/token' ),
			) );
		}
	}
	
	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_menu_page(
			__( 'Headless Blog', 'wp-headless-blog' ),
			__( 'Headless Blog', 'wp-headless-blog' ),
			'access_headless_blog',
			'wp-headless-blog',
			array( $this, 'render_admin_page' ),
			'dashicons-admin-post',
			30
		);
	}
	
	/**
	 * Render admin page
	 */
	public function render_admin_page() {
		// Check user capabilities
		if ( ! current_user_can( 'access_headless_blog' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'wp-headless-blog' ) );
		}
		
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<div id="wp-headless-blog-app"></div>
		</div>
		<?php
	}
	
	/**
	 * Register custom REST API routes
	 */
	public function register_rest_routes() {
		$rest_api = WP_Headless_Blog_REST_API::get_instance();
		$rest_api->register_routes();
	}
}

// Initialize plugin
WP_Headless_Blog::get_instance();

