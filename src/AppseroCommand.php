<?php

namespace Dokan\AppseroLicenseActivator;

use Dokan\AppseroLicenseActivator\Dependencies\Appsero\Client;
use Exception;
use WP_CLI;
use WP_CLI_Command;

/**
 * Manage Appsero license.
 *
 * ## EXAMPLES
 *
 *     # Activate license for a plugin managed by Appsero.
 *     $ wp appsero activate <license_key> --plugin_hash=<plugin_hash> --plugin_file=<plugin_file>
 *     Success: Dokan Pro license: <license_key> activated successfully.
 */
class AppseroCommand extends WP_CLI_Command {

	/**
	 * Activate the license for a plugin that managed by Appsero.
	 *
	 * ## OPTIONS
	 *
	 * <license_key>
	 * : The license key to activate
	 *
	 * [--plugin_file=<plugin_file>]
	 * : The plugin file path respective to the wp-content/plugins directory. Example: dokan-lite/dokan.php
	 *
	 * [--plugin_hash=<plugin_hash>]
	 * : The plugin hash from Appsero.
	 *
	 *  [--option_key=<option_key>]
	 * : The option key to store the license key. E.G: dokan_pro_license
	 *
	 *
	 * ## EXAMPLES
	 *
	 *     wp appsero activate <license_key> --plugin_hash=<plugin_hash> --plugin_file=<plugin_file> --option_key=<option_key>
	 *
	 * @when after_wp_load
	 */
	public function activate( $args, $assoc_args ) {
		try {
			if ( empty( $args[0] ) ) {
				throw new Exception( 'Please provide a license key.' );
			}

			if ( empty( $assoc_args['plugin_hash'] ) ) {
				throw new Exception( 'Please provide a plugin hash.' );
			}

			if ( empty( $assoc_args['plugin_file'] ) ) {
				throw new Exception( 'Please provide a plugin file path.' );
			}

			$client_hash = $assoc_args['plugin_hash'];
			$plugin_file = $assoc_args['plugin_file'];
			$file = WP_PLUGIN_DIR . '/' . $plugin_file;

			if ( ! file_exists( $file ) ) {
				throw new Exception( sprintf( 'Plugin file %s not found.', $file ) );
			}

			$file = realpath( $file );

			$client  = new Client( $client_hash, '', $file );
			$license = $client->license();

			// Set the option key if provided.
			if ( ! empty( $assoc_args['option_key'] ) ) {
				$license->set_option_key( $assoc_args['option_key'] );
			}

			// Call the private method to activate the license.
			$reflection = new \ReflectionClass($license);
			$method = $reflection->getMethod('active_client_license');
			$method->setAccessible(true);
			$method->invoke($license, $args[0]);

			if ( empty( $license->success ) ) {
				throw new Exception( $license->error );
			}

			WP_CLI::success( 'Dokan Pro license: ' . $args[0] . ' activated successfully.' );
		} catch ( Exception $e ) {
			WP_CLI::warning( 'Failed to activate Dokan Pro license: ' . $args[0] . ' . Message: ' . $e->getMessage() );
		}
	}
}