 <?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Ecommerce Store
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width">
<link rel="profile" href="http://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <div class="toggle"><a class="toggleMenu" href="#"><?php esc_html_e('Menu','bb-ecommerce-store'); ?></a></div>

<div class="header">
  <div class="container">
    <div class="col-md-3">
      <div class="logo">
        <?php bb_ecommerce_store_the_custom_logo(); ?>
        <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>

        <?php
        $description = get_bloginfo( 'description', 'display' );
        if ( $description || is_customize_preview() ) : ?>
            <p class="site-description"><?php echo esc_html( $description ); ?></p>
        <?php endif; ?>
      </div>
	    <div class="clearfix"></div>
    </div>
    <div class="side_search col-md-6 col-sm-6 col-6 offset-md-2">
      <div class="responsive_search">
      </div>
      <div class="search_form">
      <form role="search" method="get" class="woocommerce-product-search" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
       <input type="search" id="woocommerce-product-search-field" class="search-field" placeholder="<?php echo esc_attr_x( 'Busque o que quer comer', 'placeholder', 'bb-ecommerce-store' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label', 'bb-ecommerce-store' ); ?>" /><button type="submit" class="search-submit"><i class="fa fa-search" aria-hidden="true"></i></button>
      </form>
      </div>
    </div>
    <div class="cart-btn-box col-md-3 col-6 col-sm-6">
      <div class="cart_icon"><i class="fa fa-shopping-cart" aria-hidden="true"></i></div>
      <div class="cart_no">
        <div class="cart_txt">Total</div>              
        <a class="cart-contents" href="<?php if(function_exists('wc_get_cart_url')){ echo esc_url(wc_get_cart_url()); } ?>" title="<?php esc_html_e( 'View your shopping cart','bb-ecommerce-store' ); ?>"><?php if(function_exists('get_cart_total')){ echo WC()->cart->get_cart_total(); } ?></a>
      </div>
    </div>
  </div>
  <div class="nav">
		<div class="container">
          <?php wp_nav_menu( array('theme_location'  => 'primary') ); ?>
		</div>
      </div><!-- nav --><div class="clear"></div>
    </div><!-- aligner -->

  </div><!-- header -->