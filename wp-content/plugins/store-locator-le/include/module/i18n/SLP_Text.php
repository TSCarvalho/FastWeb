<?php
if ( ! class_exists( 'SLP_Text' ) ) {

	/**
	 * Methods to help manage the SLP text strings in an i18n compatible fashion.
	 *
	 * @property-read    SLP_Web_Link_Manager Web_Link_Manager
	 *
	 * @var     array       text_strings       Named array of i18n strings, in 2 dimensions.
	 *                                              key = function area slug
	 *                                              value = name array of gettext strings
	 *                                                  key = string slug
	 *                                                  value = gettext string.
	 */
	class SLP_Text extends SLP_Object_With_Objects {
		protected $objects = array(
			'Web_Link_Manager' => array(
				'subdir' => 'include/manager/',
				'object' => null,
			),
		);

		private $text_strings;

		/**
		 * Allow add-on packs to extend the text strings array via the slp_text_strings filter.
		 */
		private function extend_text_strings() {

			/**
			 * FILTER: slp_text_strings
			 *
			 * @params  array   $text_strings   empty by default
			 *
			 * @return  array                   new text_strings named array in the <string-slug> => __( 'text' , 'textdomain' ) format.
			 */
			$new_strings = apply_filters( 'slp_text_strings', array() );

			if ( ! empty( $new_strings ) ) {
				foreach ( $new_strings as $slug => $string ) {
					if ( ! is_array( $slug ) ) {
						error_log( sprintf( __( '%s::%s slug %s is not using smart text strings', 'store-locator-le' ), __CLASS__, __FUNCTION__, $slug ) );

						return;
					}
					$this->text_strings[ $slug[0] ][ $slug[1] ] = $string;
				}
			}
		}

		/**
		 * Get a basic text string
		 *
		 * @param string||array $slug The slug representing the text string we want to fetch.
		 *
		 * @return string The text string for the specified slug.
		 */
		public function get_text_string( $slug ) {
			if ( ! is_array( $slug ) ) {
				$slug = array( 'general', strtolower( $slug ) );
			}

			if ( count( $slug ) < 2 ) {
				error_log( sprintf( '%s::%s the request for slug [%s] is missing the second slug', __CLASS__, __FUNCTION__, $slug[0] ) );
				return $slug[0];
			}

			// Initialize the strings for this functional area
			if ( ! isset ( $this->text_strings[ $slug[0] ] ) ) {
				$this->init_strings( $slug );
			}

			// Get the text for the specific slug
			if ( ! isset ( $this->text_strings[ $slug[0] ] ) || ! isset( $this->text_strings[ $slug[0] ][ $slug[1] ] ) ) {
				$no_default_string = true;
				$this->text_strings[ $slug[0] ][ $slug[1] ] = $slug[1];
			} else {
				$no_default_string = false;
			}

			/**
			 * FILTER: slp_get_text_string
			 *
			 * @param string $text The current text for the slug.
			 * @param string $slug The slug being processed
			 *
			 * @return  string      The modified text for the slug.
			 */
			$text = $this->text_strings[ $slug[0] ][ $slug[1] ];
			$text =  apply_filters( 'slp_get_text_string', $text , $slug );

			// Log if there is no default text and a filter did not set new text.
			//
			if ( ( $text === $slug[1] ) && ( $no_default_string ) ) {
				error_log( sprintf( '%s::%s does not have an entry for [%s][%s]', __CLASS__, __FUNCTION__, $slug[0], $slug[1] ) );
			}

			return $text;
		}

		/**
		 * Send a set of parameters to a printf on one of the Text Manager's registered printf strings.
		 *
		 * @param   string $slug   The printf text slug
		 * @param   mixed  $params The parameters for the sprintf replacements
		 *
		 * @return  string              the i18n text
		 */
		public function get_text_with_variables_replaced( $slug, $params ) {
			if ( ! is_array( $params ) ) {
				$params = (array) $params;
			}

			return vsprintf( $this->get_text_string( array( 'printf', $slug ) ), $params );
		}

		/**
		 * Get an entry from our URLs list.
		 *
		 * @param string $slug
		 *
		 * @return string
		 */
		public function get_url( $slug ) {
			$this->instantiate( 'Web_Link_Manager' );

			return $this->Web_Link_Manager->get_url( $slug );
		}

		/**
		 * Get the specified web page HTML with embedded hyperlinks.
		 *
		 * @param   string  $slug       Which web page link to fetch.
		 * @param   string  $option     Option (such as a product slug) for further processing.
		 *
		 * @return  SLP_Web_Link A web link object.
		 */
		public function get_web_link( $slug , $option = '' ) {
			$this->instantiate( 'Web_Link_Manager' );
			return $this->Web_Link_Manager->get( $slug , $option );
		}

		/**
		 * Initialize strings in subsets as needed.
		 *
		 * @param array $slug
		 */
		private function init_strings( $slug ) {
			$area_slug = $slug[0];
			if ( ! empty( $this->text_strings[ $area_slug ] ) ) {
				return;
			}

			switch ( $area_slug ) {
				case 'general':
					$this->text_strings[ $area_slug ][ 'actions'     ]                  = __( 'Actions'     , 'store-locator-le' );
					$this->text_strings[ $area_slug ][ 'add'     ]                      = __( 'Add'         , 'store-locator-le' );
					$this->text_strings[ $area_slug ][ 'address'     ]                  = __( 'Address'     , 'store-locator-le' );
					$this->text_strings[ $area_slug ][ 'address2'    ]                  = __( 'Address 2'   , 'store-locator-le' );
					$this->text_strings[ $area_slug ][ 'cancel'      ]                  = __( 'Cancel'      , 'store-locator-le' );
					$this->text_strings[ $area_slug ][ 'city'        ]                  = __( 'City'        , 'store-locator-le' );
					$this->text_strings[ $area_slug ][ 'country'     ]                  = __( 'Country'     , 'store-locator-le' );
					$this->text_strings[ $area_slug ][ 'description' ]                  = __( 'Description' , 'store-locator-le' );
					$this->text_strings[ $area_slug ][ 'distance'    ]                  = __( 'Distance'    , 'store-locator-le' );
					$this->text_strings[ $area_slug ][ 'location'    ]                  = __( 'Location'    , 'store-locator-le' );
					$this->text_strings[ $area_slug ][ 'map_marker'  ]                  = __( 'Map Marker'  , 'store-locator-le' );
					$this->text_strings[ $area_slug ][ 'name'        ]                  = __( 'Name'        , 'store-locator-le' );
					$this->text_strings[ $area_slug ][ 'private'     ]                  = __( 'Private'     , 'store-locator-le' );
					$this->text_strings[ $area_slug ][ 'rank'  ]                        = __( 'Rank'        , 'store-locator-le' );
					$this->text_strings[ $area_slug ][ 'save'        ]                  = __( 'Save'        , 'store-locator-le' );
					$this->text_strings[ $area_slug ][ 'state'       ]                  = __( 'State'       , 'store-locator-le' );
					$this->text_strings[ $area_slug ][ 'zip'         ]                  = __( 'Zip'         , 'store-locator-le' );
					break;

				case 'admin':
					$this->text_strings[ $area_slug ]['are_you_sure']                  = __( 'Are you sure you want to ', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['Documentation']                 = __( 'Documentation', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['google_browser_key']            = __( 'Google Browser Key', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['help_header']                   = __( 'More Info', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['help_subheader']                = __( 'Hover over a setting to learn what it does.', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['location_not_added']            = __( 'Location not added.', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['location_skipped']              = __( 'Location skipped.', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['location_not_updated']          = __( 'Location not updated.', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['location_form_incorrect']       = __( 'The add location form on your server is not rendering properly. ', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['plugin_style_active_addons']    = __( 'These add-ons are helping you get the most of this plugin style: ', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['plugin_style_inactive_addons']  = __( 'This style works best if the following add-ons are active: ', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['premium_member_support']        = __( 'Premier Members get priority support and access to real-time product update information. ', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['premium_members']               = __( 'Subscription Accounts', 'store-locator-le' );

					$extra_help  = __( 'You can find this information under "My Account" in the Premier Subscription Info section. ' , 'store-locator-le' );
					$extra_help .= __( 'Login to your Store Locator Plus account to get these settings. ' , 'store-locator-le' );

					$this->text_strings[ $area_slug ]['premium_subscription_id_label'] = __( 'Subscription ID ', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['premium_subscription_id_help']  = __( 'Your Premier Subscription ID. ', 'store-locator-le' ) . $extra_help;

					$this->text_strings[ $area_slug ]['premium_user_id_label']         = __( 'User ID', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['premium_user_id_help']          = __( 'Your Premier User ID. ', 'store-locator-le' ) . $extra_help;

					$this->text_strings[ $area_slug ]['this_setting']                  = __( 'this setting', 'store-locator-le' );
					break;

				case 'description':
					$this->text_strings[ $area_slug ]['google_server_key']              = __( 'A Google Browser API key is required to use Google Maps on your site. ' , 'store-locator-le' ) .
						                                                                  __( 'You can get one for free from Google. ' , 'store-locator-le' ) ;
                    $this->text_strings[ $area_slug ]['dropdown_style'       ]          = __( 'Use this to set the style of the locator interactive jQuery elements like autocomplete drop downs. ', 'store-locator-le' ) .
                                                                                          __( 'Premier Members with an active subscription have more style options available. ', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['style_id']                       = __( 'The Style ID.', 'store-locator-le' );
					break;

				case 'error' :
					$this->text_strings[ $area_slug ]['slp_get_location_failed']   = __( 'Get location failed.', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['slp_invalid_id']            = __( 'Location id is invalid.', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['slp_invalid_option_name']   = __( 'Option name is invalid.', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['slp_location_not_updated']  = __( 'Location was not updated.', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['slp_missing_location_data'] = __( 'Missing location data.', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['slp_no_such_location']      = __( 'No such location.', 'store-locator-le' );
					break;

				case 'label' :
					$this->text_strings[ $area_slug ]['csv_import'              ]  = __( 'CSV Import'               , 'store-locator-le' );
                    $this->text_strings[ $area_slug ]['dropdown_style'          ]  = __( 'Search Form Style'               , 'store-locator-le' );
                    $this->text_strings[ $area_slug ]['slp_get_location_failed' ]  = __( 'Get location failed.'     , 'store-locator-le' );
					$this->text_strings[ $area_slug ]['google_server_key'       ]  = __( 'Google Browser Key'       , 'store-locator-le' );
					$this->text_strings[ $area_slug ]['plugin_style'            ]  = __( 'Plugin Style'             , 'store-locator-le' );
					$this->text_strings[ $area_slug ]['radius_behavior'         ]  = __( 'Radius Behavior'          , 'store-locator-le' );
					$this->text_strings[ $area_slug ]['style_id'                ]  = __( 'Style ID'                 , 'store-locator-le' );
					break;

				case 'link_text':
					$this->text_strings[ $area_slug ]['documentation_plugin_styles'] = __( 'plugin styles', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['experience']                  = __( 'Experience', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['get_started_google_key']      = __( 'Getting Started Guide', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['google_browser_key']          = __( 'Google Browser Key', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['import_suggestion']           = __( 'Power', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['more_options_suggestion']     = __( 'Add Ons', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['new_addon_versions']          = __( 'versions', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['power']                       = __( 'Power', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['premier']                     = __( 'Premier', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['premier_member_updates']      = __( 'Premier Plugin', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['premier_subscription']        = __( 'Premier Subscription', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['premier-subscription']        = $this->text_strings[ $area_slug ]['premier_subscription'];
					$this->text_strings[ $area_slug ]['pro']                         = __( 'Pro Pack', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['rate_slp']                    = '★★★★★';
					$this->text_strings[ $area_slug ]['slp-contact-extender']        = __( 'Contact Extender'      , 'store-locator-le' );
					$this->text_strings[ $area_slug ]['slp-directory-builder']       = __( 'Directory Builder'      , 'store-locator-le' );
					$this->text_strings[ $area_slug ]['slp-enhanced-map']            = __('Enhanced Map', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['slp-enhanced-results']        = __('Enhanced Results', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['slp-enhanced-search']         = __('Enhanced Search', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['slp-pages']                   = __( 'Pages'      , 'store-locator-le' );
					$this->text_strings[ $area_slug ]['slp-tagalong']                = __( 'Tagalong'   , 'store-locator-le' );
					$this->text_strings[ $area_slug ]['slp-widgets']                 = __( 'Widgets'   , 'store-locator-le' );
					$this->text_strings[ $area_slug ]['upgrade_wp_version']          = __( 'Upgrade'    , 'store-locator-le' );

					$this->text_strings[ $area_slug ]['slp-experience']              = $this->text_strings[ $area_slug ]['experience'];
					$this->text_strings[ $area_slug ]['slp-power']                   = $this->text_strings[ $area_slug ]['power'];
					$this->text_strings[ $area_slug ]['slp-premier']                 = $this->text_strings[ $area_slug ]['premier'];
					$this->text_strings[ $area_slug ]['slp-pro']                     = $this->text_strings[ $area_slug ]['pro'];

					break;

				case
					'option_default':
					$this->text_strings[ $area_slug ]['address_placeholder']   = '';
					$this->text_strings[ $area_slug ]['google_map_style']      = '';
					$this->text_strings[ $area_slug ]['instructions']          = __( 'Enter an address or zip code and click the find locations button.', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['invalid_query_message'] = __( 'We did not receive a valid JSONP response.', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['label_directions']      = __( 'Directions', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['label_fax']             = __( 'Fax', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['label_for_find_button'] = __( 'Find Locations', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['label_email']           = __( 'Email', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['label_hours']           = __( 'Hours', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['label_image']           = __( 'Image', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['label_phone']           = __( 'Phone', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['label_radius']          = __( 'Within', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['label_search']          = __( 'Address / Zip', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['label_website']         = __( 'Website', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['message_no_results']    = __( 'No locations found.', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['message_no_api_key']    = __( 'This site most likely needs a Google Maps API key.', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['results_box_title']     = '';
					break;

				case 'printf' :
					$this->text_strings[ $area_slug ]['check_website_for_upgrades']  = __( 'Check the %s website for upgrade options. ', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['docs_for_']                   = __( 'View the documentation for %s.', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['documentation_plugin_styles'] = __( 'View %s documentation.', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['error_code']                  = __( 'Error Code : %s', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['get_started_google_key']      = __( 'Please view our %s for details on how to get the right Google API Key. ', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['google_browser_key']          = __( 'Enter your %s here. ', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['import_provided_by']          = __( 'Location import is provided by the %s add on.', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['import_suggestion']           = __( 'If you have many locations to add, check out the %s add on with bulk import options.', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['installed_version']           = __( 'You have version %s installed.', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['latest_version']              = __( 'The latest version available is %s.', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['more_options_suggestion']     = __( 'Even more settings are available via our %s. ', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['new_addon_versions']          = __( 'Most add ons have new %s available for download.', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['premier_member_updates']      = __( 'The %s add-on requires an active subscription to receive the latest updates. ', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['rate_slp']                    = __( 'If you like Store Locator Plus please give us a %s rating. ', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['successfully_completed']      = __( '%s successfully.', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['upgrade_wp_version']          = __( '%s to the latest version to restore functionality. ', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['visit_site_for_addons']       = __( 'Visit the %s site for a full list of add ons that provide additional locator features. ', 'store-locator-le' );
					break;

				case 'settings_group':
				case 'settings_group_header':
					$this->text_strings[ $area_slug ]['data']                     = __( 'Add On Data Extensions', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['experience']               = __( 'Experience', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['extendeddata']             = __( 'Extended Data', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['javascript']               = __( 'JavaScript', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['locations']                = __( 'Locations', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['packaged_data_extensions'] = __( 'Add On Data Extensions', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['power']                    = __( 'Power', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['security']                 = __( 'Security', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['settings']                 = __( 'Settings', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['style']                    = __( 'Style', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['url_control']              = __( 'URL Controls', 'store-locator-le' );
					break;

				case 'settings_section':
					$this->text_strings[ $area_slug ]['admin']          = __( 'Admin', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['data']           = __( 'Data', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['server']         = __( 'Server', 'store-locator-le' );
					$this->text_strings[ $area_slug ]['user_interface'] = __( 'User Interface', 'store-locator-le' );
					break;
			}

		}

		/**
		 * Initialize this object.
		 *
		 * Setup the starting text strings.
		 */
		public function initialize() {
			$this->class_prefix = 'SLP_';
			$this->dir          = SLPLUS_PLUGINDIR;
			$this->extend_text_strings();
		}
	}

	/**
	 * @var SLPlus $slplus
	 */
	global $slplus;
	if ( is_a( $slplus, 'SLPlus' ) ) {
		$slplus->add_object( new SLP_Text() );
	}
}