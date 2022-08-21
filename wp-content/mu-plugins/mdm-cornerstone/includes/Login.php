<?php
/**
 * Admin control class
 *
 * @link https://midwestfamilymadison.com
 * @package mdm_wp_cornerstone/classes
 */

namespace Mdm\Cornerstone;

defined( 'ABSPATH' ) || exit;

class Login extends Framework
{
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 */
	public function addActions() : void
	{
		Subscriber::addAction( 'login_enqueue_scripts', [$this, 'enqueueStyles'] );
		Subscriber::addAction( 'login_header', [$this, 'addMarkup'], 0 );
		Subscriber::addAction( 'login_footer', [$this, 'addMarkup'], 999 );
		Subscriber::addFilter( 'login_message', [$this, 'loginMessage'] );

	}
	/**
	 * Enqueue admin assets
	 */
	public function enqueueAsseets() : void
	{

	}
	/**
	 * Enqueue Frontend CSS Files
	 * @see https://developer.wordpress.org/reference/functions/wp_enqueue_style/
	 */
	public function enqueueStyles()  : void
	{
		wp_enqueue_style(
			'mdm_cornerstone_login',
			Plugin::url( 'assets/css/login.css' ),
			[],
			MDM_CORNERSTONE_VERSION,
			'all'
		);
	}

	public function addMarkup()
	{
		$action = current_action();

		if ( $action === 'login_header' )
		{
			echo '<div id="login-page">';
			echo '<div id="login-container">';

			include Plugin::path( 'template-parts/login.php' );

			echo '<div class="login-form-area">';
		}
		elseif ( $action === 'login_footer' )
		{
			echo '</div></div></div>';
		}
	}

	public function loginMessage( $message )
	{
		if ( isset( $_GET['action'] ) ) {
			switch ( $_GET['action'] ) {
				case 'lostpassword':
					$message = '<h2 class="sign-in">Request Password Reset</h2>';
					break;

				default:
					$message = '<h2 class="sign-in">Sign In</h2>';
					break;
			}
		}
		return $message ?: '<h2 class="sign-in">Sign In</h2>';
	}
}