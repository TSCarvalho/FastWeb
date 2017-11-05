<?php
/**
 * @package BB Ecommerce Store
 * Setup the WordPress core custom header feature.
 *
 * @uses bb_ecommerce_store_header_style()
*/
function bb_ecommerce_store_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'bb_ecommerce_store_custom_header_args', array(
		'default-text-color'     => 'fff',
		'header-text' 			 =>	false,
		'width'                  => 1600,
		'height'                 => 400,
		'wp-head-callback'       => 'bb_ecommerce_store_header_style',
	) ) );
}
add_action( 'after_setup_theme', 'bb_ecommerce_store_custom_header_setup' );

if ( ! function_exists( 'bb_ecommerce_store_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @see bb_ecommerce_store_custom_header_setup().
 */
function bb_ecommerce_store_header_style() {

		$header_text_color = get_header_textcolor();
	?>
		<style type="text/css">
			<?php
				//Check if user has defined any header image.
				if ( get_header_image() ) :
			?>
				.header{
					background: url(<?php echo esc_url(get_header_image()); ?>) no-repeat;
					background-position: center top;
				}
			<?php endif; ?>	
		</style>
	<?php

}
endif; // bb_ecommerce_store_header_style

// bb_ecommerce_store_admin_header_css
add_action( 'admin_head', 'bb_ecommerce_store_admin_header_css' );
function bb_ecommerce_store_admin_header_css() { ?>

		<style>pre{white-space: pre-wrap;}</style>
		
	<?php
}