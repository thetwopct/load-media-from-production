<?php
/**
 * Load Media from Production
 *
 * @package     WordPress
 * @author      James Hunt
 * @license     GPL-3.0+
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Validate nonce.
if ( isset( $_POST['submit'] ) && ! isset( $_POST['lmfp-settings'] ) ) {
	echo '<div class="notice notice-error"><p>Nonce verification failed</p></div>';
	exit;
}

if ( isset( $_POST['submit'] ) && ! wp_verify_nonce( $_POST['lmfp-settings'], 'load-media-from-production' ) ) {
	echo '<div class="notice notice-error"><p>Nonce verification failed</p></div>';
	exit;
}

$notify = '';

// if the page has just been submitted.
if ( isset( $_POST['production_url'] ) ) :

	if ( $_POST['production_url'] == '' ) {
		// if the production_url is empty, it must be cleared.
		delete_option( 'lmfp_production_url' );
		$notify = 'updated';
	} else {
		// check any submitted URL.
		$host = $this->lmfp_sanitize_trim_check( sanitize_text_field( wp_unslash( $_POST['production_url'] ) ) );

		// if the URL is good, then update.
		if ( $host ) {
			update_option( 'lmfp_production_url', $host );
			$notify = 'updated';
		} else {
			$notify = 'error';
		}
	}
endif;

// setup variables for the settings form.
$production_url = $this->lmfp_get_production_url();

$wp_home_class     = '';
if ( defined( 'LOAD_MEDIA_FROM_PRODUCTION_URL' ) ) {
	$wp_home_class = ' disabled';
	echo "<div class=\"notice notice-success\"><p>Defined constant is set - <code>define ( 'LOAD_MEDIA_FROM_PRODUCTION_URL', '" . esc_url( LOAD_MEDIA_FROM_PRODUCTION_URL ) . "' );</code>. Settings cannot be updated.</p></div>";
	echo '<div class="notice notice-error"><p>Settings cannot be updated.</p></div>';
}
?>

<div class="wrap">
	<h1>Load Media From Production</h1>
	<?php if ( 'updated' === $notify ) : ?>
	<div class="notice notice-success is-dismissible">
		<p>Production URL was updated.</p>
	</div>
	<?php endif; ?>
	<?php if ( 'error' === $notify ) : ?>
	<div class="notice notice-error is-dismissible">
		<p>There was a problem. Please check the URL and try again.</p>
	</div>
	<?php endif; ?>

	<form method="POST">
		<?php wp_nonce_field( 'load-media-from-production', 'lmfp-settings' ); ?>
		<p>Enter the base URL of your production WordPress install to load the images from it.</p>
		<p>If you have WordPress installed in the root of your production domain, enter the base URL e.g. <code>https://production-domain.com</code></p>
		<p>If you have WordPress installed in a sub-directory, like /wp, then enter e.g. <code>https://production-domain.com/wp</code></p>
		<p>There is no need to add the content/media path, as the plugin does this automatically.</p>

		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="production-url">Base Production	URL</label>
					</th>
					<td>
						<input type="text" name="production_url" id="production-url"
							size="50"
							value="<?php echo esc_url( $production_url ); ?>"
							<?php disabled( defined( 'LOAD_MEDIA_FROM_PRODUCTION_URL' ) ); ?> class="regular-text code<?php echo esc_html( $wp_home_class ); ?>" />
							<br />
							<p>e.g. <code>https://production-domain.com</code></p>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" value="Save" class="button button-primary button-large" <?php disabled( defined( 'LOAD_MEDIA_FROM_PRODUCTION_URL' ) ); ?>></p>
	</form>
</div>

<?php
