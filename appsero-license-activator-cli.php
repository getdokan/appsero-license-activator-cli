<?php
/**
 * Plugin Name: Appsero License Activator WP-CLI
 * Description: Activate licence for plugin managed by Appsero.
 * Plugin URI: https://dokan.co
 * Author: Dokan Team
 * Author URI: https://dokan.co
 * Version: 1.0
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */


if ( ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	return;
}

require_once __DIR__ . '/vendor/autoload.php';

// Add the command.
try {
	WP_CLI::add_command( 'appsero', \Dokan\AppseroLicenseActivator\AppseroCommand::class );
} catch ( Exception $e ) {
	WP_CLI::warning( 'Failed to add command. Message: ' . $e->getMessage() );
}
