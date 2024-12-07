<?php
/**
 * Woocommerce Class
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

defined( 'ABSPATH' ) || die();

/**
 * Integrations with WooCommerce.
 */
class Woocommerce {

	/**
	 * Hooks code into WordPress.
	 */
	public static function register() {

        add_filter( 'woocommerce_product_data_tabs', __CLASS__ . '::filter_woocommerce_product_data_tabs', 10, 1 );
        add_action( 'woocommerce_product_data_panels', __CLASS__ . '::display_woocommerce_product_data_panels', 10, 1 );
        add_action( 'woocommerce_process_product_meta', __CLASS__ . '::save_custom_product_meta', 10, 2 );

		add_filter( 'post_type_link', __CLASS__ . '::filter_product_post_type_link', 999, 2 );
	}

	/**
	 * Adds the custom product data tab.
	 */
    public static function filter_woocommerce_product_data_tabs( $tabs = array() ) {
        $tabs['ptc_theme'] = array(
            'label'    => 'PTC Theme',
            'target'   => 'ptc_theme',
            'class'    => array(),
            'priority' => 999,
        );
        return $tabs;
    }

	/**
	 * Displays custom product data tabs in the post edit screen.
	 */
    public static function display_woocommerce_product_data_panels() {
        global $post;

        // Get the saved value for the current product.
        $saved_product_url = get_post_meta( $post->ID, '_ptc_theme_product_url', true );

        // Display tab panel content.
        ?>
        <div id="ptc_theme" class="panel woocommerce_options_panel hidden">

            <div class="options_group">
                <?php
                woocommerce_wp_text_input(
                    array(
						'data_type'   => 'url',
						'id'          => '_ptc_theme_product_url',
						'name'        => '_ptc_theme_product_url',
						'label'       => 'Product URL',
						'value'       => $saved_product_url,
						'description' => 'Override the product URL to point to this URL instead. This means you can point this product to its own custom landing page rather than the typical WooCommerce product template page.',
                    )
                );
                ?>
            </div>
        </div>
        <?php
    }

	/**
	 * Saves custom product post meta.
	 *
	 * @param int $post_id The current post being saved.
	 */
    public static function save_custom_product_meta( $post_id ) {
        // Check and save the custom meta value.
        if ( isset( $_POST['_ptc_theme_product_url'] ) ) {
            $product_url = sanitize_text_field( $_POST['_ptc_theme_product_url'] );
            update_post_meta( $post_id, '_ptc_theme_product_url', $product_url );
        }
    }

	/**
	 * Overrides the product's permalink.
	 *
	 * @see wc_product_post_type_link() wp-content/plugins/woocommerce/includes/wc-product-functions.php
	 *
	 * @param  string  $permalink The existing permalink URL.
	 * @param  WP_Post $post WP_Post object.
	 * @return string
	 */
	public static function filter_product_post_type_link( $permalink, $post ) {

		if ( 'product' === $post->post_type ) {
			$product_url = get_post_meta( $post->ID, '_ptc_theme_product_url', true );
			if ( $product_url ) {
				$permalink = $product_url;
			}
		}

		return $permalink;
	}
}
