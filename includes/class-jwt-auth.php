<?php
/**
 * JWT Authentication Handler
 * 
 * Handles JWT token generation, validation, and authentication
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP_Headless_Blog_JWT_Auth {
	
	private static $instance = null;
	
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	private function __construct() {
		add_filter( 'determine_current_user', array( $this, 'determine_current_user' ), 20 );
		add_filter( 'rest_authentication_errors', array( $this, 'rest_authentication_errors' ) );
	}
	
	/**
	 * Determine current user from JWT token
	 */
	public function determine_current_user( $user_id ) {
		// If already authenticated, return
		if ( $user_id ) {
			return $user_id;
		}
		
		// Get token from header
		$token = $this->get_token_from_header();
		
		if ( ! $token ) {
			return $user_id;
		}
		
		// Validate and decode token
		$payload = $this->validate_token( $token );
		
		if ( ! $payload || ! isset( $payload['user_id'] ) ) {
			return $user_id;
		}
		
		return (int) $payload['user_id'];
	}
	
	/**
	 * Handle REST authentication errors
	 */
	public function rest_authentication_errors( $result ) {
		if ( ! empty( $result ) ) {
			return $result;
		}
		
		// If user is already authenticated, no need to check JWT
		if ( is_user_logged_in() ) {
			return $result;
		}
		
		$token = $this->get_token_from_header();
		
		if ( ! $token ) {
			return $result;
		}
		
		$payload = $this->validate_token( $token );
		
		if ( ! $payload || ! isset( $payload['user_id'] ) ) {
			return new WP_Error(
				'jwt_auth_invalid_token',
				__( 'Invalid token.', 'wp-headless-blog' ),
				array( 'status' => 401 )
			);
		}
		
		return $result;
	}
	
	/**
	 * Get JWT token from Authorization header
	 */
	private function get_token_from_header() {
		$headers = $this->get_authorization_header();
		
		if ( ! $headers ) {
			return false;
		}
		
		// Extract token from "Bearer TOKEN"
		if ( preg_match( '/Bearer\s+(.*)$/i', $headers, $matches ) ) {
			return $matches[1];
		}
		
		return false;
	}
	
	/**
	 * Get Authorization header
	 */
	private function get_authorization_header() {
		if ( isset( $_SERVER['HTTP_AUTHORIZATION'] ) ) {
			return sanitize_text_field( wp_unslash( $_SERVER['HTTP_AUTHORIZATION'] ) );
		}
		
		if ( isset( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) ) {
			return sanitize_text_field( wp_unslash( $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ) );
		}
		
		return false;
	}
	
	/**
	 * Generate JWT token
	 */
	public function generate_token( $user_id, $expiration = 86400 ) {
		$secret_key = $this->get_secret_key();
		
		if ( ! $secret_key ) {
			return new WP_Error(
				'jwt_auth_no_secret_key',
				__( 'JWT secret key not configured.', 'wp-headless-blog' ),
				array( 'status' => 500 )
			);
		}
		
		$issued_at = time();
		$expire = $issued_at + $expiration;
		
		$payload = array(
			'iss' => get_bloginfo( 'url' ),
			'iat' => $issued_at,
			'exp' => $expire,
			'user_id' => $user_id,
		);
		
		$header = array(
			'alg' => 'HS256',
			'typ' => 'JWT',
		);
		
		$base64_header = $this->base64url_encode( json_encode( $header ) );
		$base64_payload = $this->base64url_encode( json_encode( $payload ) );
		
		$signature = hash_hmac( 'sha256', $base64_header . '.' . $base64_payload, $secret_key, true );
		$base64_signature = $this->base64url_encode( $signature );
		
		$token = $base64_header . '.' . $base64_payload . '.' . $base64_signature;
		
		return $token;
	}
	
	/**
	 * Validate JWT token
	 */
	public function validate_token( $token ) {
		$secret_key = $this->get_secret_key();
		
		if ( ! $secret_key ) {
			return false;
		}
		
		$parts = explode( '.', $token );
		
		if ( count( $parts ) !== 3 ) {
			return false;
		}
		
		list( $base64_header, $base64_payload, $base64_signature ) = $parts;
		
		// Verify signature
		$signature = $this->base64url_decode( $base64_signature );
		$expected_signature = hash_hmac( 'sha256', $base64_header . '.' . $base64_payload, $secret_key, true );
		
		if ( ! hash_equals( $expected_signature, $signature ) ) {
			return false;
		}
		
		// Decode payload
		$payload = json_decode( $this->base64url_decode( $base64_payload ), true );
		
		if ( ! $payload ) {
			return false;
		}
		
		// Check expiration
		if ( isset( $payload['exp'] ) && $payload['exp'] < time() ) {
			return false;
		}
		
		return $payload;
	}
	
	/**
	 * Get secret key for JWT
	 */
	private function get_secret_key() {
		$key = get_option( 'wp_headless_blog_jwt_secret' );
		
		if ( ! $key ) {
			// Generate a new key if none exists
			$key = wp_generate_password( 64, true, true );
			update_option( 'wp_headless_blog_jwt_secret', $key );
		}
		
		return $key;
	}
	
	/**
	 * Base64 URL encode
	 */
	private function base64url_encode( $data ) {
		return rtrim( strtr( base64_encode( $data ), '+/', '-_' ), '=' );
	}
	
	/**
	 * Base64 URL decode
	 */
	private function base64url_decode( $data ) {
		return base64_decode( strtr( $data, '-_', '+/' ) );
	}
}

