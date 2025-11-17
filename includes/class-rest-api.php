<?php
/**
 * REST API Helper Class
 * 
 * Registers custom REST API endpoints
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP_Headless_Blog_REST_API {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		// Constructor
	}
	
	/**
	 * Register custom REST API routes
	 */
	public function register_routes() {
		// JWT Authentication endpoints
		register_rest_route( 'wp-headless-blog/v1', '/auth/token', array(
			'methods' => 'POST',
			'callback' => array( $this, 'generate_token' ),
			'permission_callback' => '__return_true',
		) );
		
		register_rest_route( 'wp-headless-blog/v1', '/auth/validate', array(
			'methods' => 'GET',
			'callback' => array( $this, 'validate_token' ),
			'permission_callback' => array( $this, 'check_authentication' ),
		) );
		
		// User info endpoint
		register_rest_route( 'wp-headless-blog/v1', '/auth/me', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_current_user' ),
			'permission_callback' => array( $this, 'check_authentication' ),
		) );
	}
	
	/**
	 * Generate JWT token
	 */
	public function generate_token( WP_REST_Request $request ) {
		$username = $request->get_param( 'username' );
		$password = $request->get_param( 'password' );
		
		if ( ! $username || ! $password ) {
			return new WP_Error(
				'jwt_auth_empty_credentials',
				__( 'Username and password are required.', 'wp-headless-blog' ),
				array( 'status' => 400 )
			);
		}
		
		// Authenticate user
		$user = wp_authenticate( $username, $password );
		
		if ( is_wp_error( $user ) ) {
			return new WP_Error(
				'jwt_auth_failed',
				__( 'Invalid username or password.', 'wp-headless-blog' ),
				array( 'status' => 401 )
			);
		}
		
		// Generate token
		$jwt_auth = WP_Headless_Blog_JWT_Auth::get_instance();
		$token = $jwt_auth->generate_token( $user->ID, 86400 * 7 ); // 7 days
		
		if ( is_wp_error( $token ) ) {
			return $token;
		}
		
		return new WP_REST_Response( array(
			'token' => $token,
			'user' => array(
				'id' => $user->ID,
				'name' => $user->display_name,
				'email' => $user->user_email,
				'roles' => $user->roles,
			),
		), 200 );
	}
	
	/**
	 * Validate token
	 */
	public function validate_token( WP_REST_Request $request ) {
		$current_user = wp_get_current_user();
		
		return new WP_REST_Response( array(
			'valid' => true,
			'user' => array(
				'id' => $current_user->ID,
				'name' => $current_user->display_name,
				'email' => $current_user->user_email,
				'roles' => $current_user->roles,
			),
		), 200 );
	}
	
	/**
	 * Get current user
	 */
	public function get_current_user( WP_REST_Request $request ) {
		$current_user = wp_get_current_user();
		
		return new WP_REST_Response( array(
			'id' => $current_user->ID,
			'name' => $current_user->display_name,
			'email' => $current_user->user_email,
			'roles' => $current_user->roles,
			'avatar' => get_avatar_url( $current_user->ID ),
		), 200 );
	}
	
	/**
	 * Check if user is authenticated
	 */
	public function check_authentication() {
		return is_user_logged_in();
	}
}

