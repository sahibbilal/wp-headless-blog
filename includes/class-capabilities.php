<?php
/**
 * Capabilities Management
 * 
 * Handles custom capabilities for the headless blog plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP_Headless_Blog_Capabilities {
	
	/**
	 * Add custom capabilities to roles
	 */
	public static function add_capabilities() {
		// Add capability to administrator
		$admin = get_role( 'administrator' );
		if ( $admin ) {
			$admin->add_cap( 'access_headless_blog' );
		}
		
		// Add capability to editor
		$editor = get_role( 'editor' );
		if ( $editor ) {
			$editor->add_cap( 'access_headless_blog' );
		}
		
		// Add capability to author
		$author = get_role( 'author' );
		if ( $author ) {
			$author->add_cap( 'access_headless_blog' );
		}
		
		// Add capability to contributor
		$contributor = get_role( 'contributor' );
		if ( $contributor ) {
			$contributor->add_cap( 'access_headless_blog' );
		}
		
		// Add capability to subscriber (optional - you can remove this if you don't want subscribers to access)
		$subscriber = get_role( 'subscriber' );
		if ( $subscriber ) {
			$subscriber->add_cap( 'access_headless_blog' );
		}
	}
	
	/**
	 * Remove custom capabilities from roles
	 */
	public static function remove_capabilities() {
		$roles = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' );
		
		foreach ( $roles as $role_name ) {
			$role = get_role( $role_name );
			if ( $role ) {
				$role->remove_cap( 'access_headless_blog' );
			}
		}
	}
}

