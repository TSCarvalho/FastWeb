<?php
/*
Plugin Name: Store Locator Plus
Plugin URI: https://www.storelocatorplus.com/
Description: Add a location finder or directory to your site in minutes. Extensive add-on library available!
Author: Store Locator Plus
Author URI: https://www.storelocatorplus.com
License: GPL3
Tested up to: 4.8.2
Version: 4.8.7

Text Domain: store-locator-le
Domain Path: /languages/

Copyright 2012 - 2017  Charleston Software Associates (info@storelocatorplus.com)
*/

if ( defined( 'SLPLUS_VERSION' ) ) return;
defined( 'SLPLUS_VERSION'  ) || define( 'SLPLUS_VERSION'  , '4.8.7' );
defined( 'SLP_LOADER_FILE' ) || define( 'SLP_LOADER_FILE' , __FILE__ );

require_once( 'include/base/loader.php' );

if ( ! slp_passed_requirements() ) return;

slp_setup_environment();
require_once( 'include/SLPlus.php' );