<?php
/**
 * Ecommerce Store Theme Customizer
 *
 * @package Ecommerce Store
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function bb_ecommerce_store_customize_register( $wp_customize ) {	

	//add home page setting pannel
	$wp_customize->add_panel( 'bb_ecommerce_store_panel_id', array(
	    'priority' => 10,
	    'capability' => 'edit_theme_options',
	    'theme_supports' => '',
	    'title' => __( 'Theme Settings', 'bb-ecommerce-store' ),
	    'description' => __( 'Description of what this panel does.', 'bb-ecommerce-store' ),
	) );
	
	//Layouts
	$wp_customize->add_section( 'bb_ecommerce_store_left_right', array(
    	'title'      => __( 'Layout Settings', 'bb-ecommerce-store' ),
		'priority'   => 30,
		'panel' => 'bb_ecommerce_store_panel_id'
	) );

	// Add Settings and Controls for Layout
	$wp_customize->add_setting('bb_ecommerce_store_theme_options',array(
	        'default' => '',
	        'sanitize_callback' => 'bb_ecommerce_store_sanitize_choices'	        
	    )
    );

	$wp_customize->add_control('bb_ecommerce_store_theme_options',
	    array(
	        'type' => 'radio',
	        'label' => 'Change Layouts',
	        'section' => 'bb_ecommerce_store_left_right',
	        'choices' => array(
	            'Left Sidebar' => __('Left Sidebar','bb-ecommerce-store'),
	            'Right Sidebar' => __('Right Sidebar','bb-ecommerce-store'),
	            'One Column' => __('One Column','bb-ecommerce-store'),
	            'Three Columns' => __('Three Columns','bb-ecommerce-store'),
	            'Four Columns' => __('Four Columns','bb-ecommerce-store'),
	            'Grid Layout' => __('Grid Layout','bb-ecommerce-store')
	        ),
	    )
    );

    //home page slider
	$wp_customize->add_section( 'bb_ecommerce_store_slidersettings' , array(
    	'title'      => __( 'Slider Settings', 'bb-ecommerce-store' ),
		'priority'   => 30,
		'panel' => 'bb_ecommerce_store_panel_id'
	) );

	for ( $count = 1; $count <= 4; $count++ ) {

		// Add color scheme setting and control.
		$wp_customize->add_setting( 'bb_ecommerce_store_slidersettings-page-' . $count, array(
				'default'           => '',
				'sanitize_callback' => 'absint'
		) );

		$wp_customize->add_control( 'bb_ecommerce_store_slidersettings-page-' . $count, array(
			'label'    => __( 'Select Slide Image Page', 'bb-ecommerce-store' ),
			'section'  => 'bb_ecommerce_store_slidersettings',
			'type'     => 'dropdown-pages'
		) );

	}

	//OUR services
	$wp_customize->add_section('bb_ecommerce_store_product',array(
		'title'	=> __('Products','bb-ecommerce-store'),
		'description'=> __('This section will appear below the slider.','bb-ecommerce-store'),
		'panel' => 'bb_ecommerce_store_panel_id',
	));
	
	
	$wp_customize->add_setting('bb_ecommerce_store_sec1_title',array(
		'default'=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('bb_ecommerce_store_sec1_title',array(
		'label'	=> __('Section Title','bb-ecommerce-store'),
		'section'=> 'bb_ecommerce_store_product',
		'setting'=> 'bb_ecommerce_store_sec1_title',
		'type'=> 'text'
	));	

	for ( $count = 0; $count <= 0; $count++ ) {

		$wp_customize->add_setting( 'bb_ecommerce_store_servicesettings-page-' . $count, array(
			'default'           => '',
			'sanitize_callback' => 'absint'
		));
		$wp_customize->add_control( 'bb_ecommerce_store_servicesettings-page-' . $count, array(
			'label'    => __( 'Select Page', 'bb-ecommerce-store' ),
			'section'  => 'bb_ecommerce_store_product',
			'type'     => 'dropdown-pages'
		));
	}
	
	//footer
	$wp_customize->add_section('bb_ecommerce_store_footer_section',array(
		'title'	=> __('Footer Text','bb-ecommerce-store'),
		'description'	=> __('Add some text for footer like copyright etc.','bb-ecommerce-store'),
		'panel' => 'bb_ecommerce_store_panel_id'
	));
	
	$wp_customize->add_setting('bb_ecommerce_store_footer_copy',array(
		'default'	=> '',
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('bb_ecommerce_store_footer_copy',array(
		'label'	=> __('Copyright Text','bb-ecommerce-store'),
		'section'	=> 'bb_ecommerce_store_footer_section',
		'type'		=> 'text'
	));
	
}
add_action( 'customize_register', 'bb_ecommerce_store_customize_register' );	

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function bb_ecommerce_store_customize_preview_js() {
	wp_enqueue_script( 'bb_ecommerce_store_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'bb_ecommerce_store_customize_preview_js' );


/**
 * Singleton class for handling the theme's customizer integration.
 *
 * @since  1.0.0
 * @access public
 */
final class bb_ecommerce_store_customize {

	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self;
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Constructor method.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Sets up initial actions.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function setup_actions() {

		// Register panels, sections, settings, controls, and partials.
		add_action( 'customize_register', array( $this, 'sections' ) );

		// Register scripts and styles for the controls.
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ), 0 );
	}

	/**
	 * Sets up the customizer sections.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  object  $manager
	 * @return void
	 */
	public function sections( $manager ) {

		// Load custom sections.
		load_template( trailingslashit( get_template_directory() ) . '/inc/section-pro.php' );

		// Register custom section types.
		$manager->register_section_type( 'bb_ecommerce_store_customize_Section_Pro' );

		// Register sections.
		$manager->add_section(
			new bb_ecommerce_store_customize_Section_Pro(
				$manager,
				'example_1',
				array(
					'title'    => esc_html__( 'BB Ecommerce Store Pro', 'bb-ecommerce-store' ),
					'pro_text' => esc_html__( 'Go Pro',         'bb-ecommerce-store' ),
					'pro_url'  => 'http://www.themeshopy.com/premium/ecommerce-store-wordpress-theme/'
				)
			)
		);
	}

	/**
	 * Loads theme customizer CSS.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_control_scripts() {

		wp_enqueue_script( 'bb-ecommerce-store-customize-controls', trailingslashit( get_template_directory_uri() ) . '/js/customize-controls.js', array( 'customize-controls' ) );

		wp_enqueue_style( 'bb-ecommerce-store-customize-controls', trailingslashit( get_template_directory_uri() ) . '/css/customize-controls.css' );
	}
}

// Doing this customizer thang!
bb_ecommerce_store_customize::get_instance();