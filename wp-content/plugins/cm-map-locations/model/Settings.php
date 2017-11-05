<?php

namespace com\cminds\maplocations\model;

use com\cminds\maplocations\shortcode\LocationSnippetShortcode;

use com\cminds\maplocations\App;

class Settings extends SettingsAbstract {
	
	const OPTION_PERMALINK_PREFIX = 'cmloc_permalink_prefix';
	const OPTION_PAGE_TEMPLATE = 'cmloc_page_template';
	const OPTION_PAGE_TEMPLATE_OTHER = 'cmloc_page_template_other';
	const OPTION_TEMPLATE_OVERRIDE_DIR = 'cmloc_template_override_dir';
	const OPTION_PAGINATION_LIMIT = 'cmloc_pagination_limit';
	const OPTION_INDEX_ORDERBY = 'cmloc_index_orderby';
	const OPTION_INDEX_ORDER = 'cmloc_index_order';
	const OPTION_INDEX_TEXT_TOP = 'cmloc_index_text_top';
	const OPTION_UNIT_LENGTH = 'cmloc_unit_length';
	const OPTION_UNIT_TEMPERATURE = 'cmloc_unit_temperature';
	const OPTION_INDEX_ROUTE_PARAMS = 'cmloc_index_route_params';
	const OPTION_SINGLE_ROUTE_PARAMS = 'cmloc_single_route_params';
	const OPTION_GOOGLE_MAPS_APP_KEY = 'cmloc_google_maps_app_key';
	const OPTION_GOOGLE_ELEVATION_API_KEY = 'cmloc_google_elevation_api_key';
	const OPTION_OPENWEATHERMAP_API_KEY = 'cmloc_openweathermap_api_key';
	const OPTION_ACCESS_MAP_CREATE_CAP = 'cmloc_access_map_create_cap';
	const OPTION_ACCESS_MAP_CREATE = 'cmloc_access_map_create';
	const OPTION_ACCESS_MAP_INDEX_CAP = 'cmloc_access_map_index_cap';
	const OPTION_ACCESS_MAP_INDEX = 'cmloc_access_map_index';
	const OPTION_ACCESS_MAP_VIEW_CAP = 'cmloc_access_map_view_cap';
	const OPTION_ACCESS_MAP_VIEW = 'cmloc_access_map_view';
	const OPTION_ROUTE_DEFAULT_IMAGE = 'cmloc_route_default_image';
	const OPTION_INDEX_MAP_MARKER_CLICK = 'cmloc_index_map_marker_click';
	const OPTION_INDEX_ZIP_RADIUS_FILTER_ENABLE = 'cmloc_index_zip_radius_filter_enable';
	const OPTION_INDEX_ZIP_RADIUS_COUNTRY = 'cmloc_index_zip_radius_country';
	const OPTION_INDEX_ZIP_RADIUS_MIN = 'cmloc_index_zip_radius_min';
	const OPTION_INDEX_ZIP_RADIUS_MAX = 'cmloc_index_zip_radius_max';
	const OPTION_INDEX_ZIP_RADIUS_STEP = 'cmloc_index_zip_radius_step';
	const OPTION_INDEX_ZIP_RADIUS_DEFAULT = 'cmloc_index_zip_radius_default';
	const OPTION_INDEX_ZIP_RADIUS_GEOLOCATION = 'cmloc_index_zip_radius_geolocation';
	const OPTION_INDEX_LIST_ITEM_CLICK = 'cmloc_index_list_item_click';
	const OPTION_MAP_DEFAULT_ICON_URL = 'cmloc_map_default_icon_url';
	const OPTION_TOOLTIP_DESCRIPTION_CHARS = 'cmloc_tooltip_description_chars';
	const OPTION_INDEX_MAP_MARKER_LABEL_SHOW = 'cmloc_index_map_marker_label_show';
	const OPTION_ROUTE_MAP_MARKER_LABEL_SHOW = 'cmloc_route_map_marker_label_show';
	const OPTION_ROUTE_INDEX_FEATURED_IMAGE = '_cmloc_route_index_featured_image';
	const OPTION_INDEX_LOCATIONS_LIST_LAYOUT = '_cmloc_index_locations_list_layout';
	const OPTION_EDITOR_DEFAULT_LAT = 'cmloc_editor_default_lat';
	const OPTION_EDITOR_DEFAULT_LONG = 'cmloc_editor_default_long';
	const OPTION_EDITOR_DEFAULT_ZOOM = 'cmloc_editor_default_zoom';
	const OPTION_EDITOR_RICH_TEXT_ENABLE = 'cmloc_editor_rich_text_enable';
	const OPTION_CUSTOM_CSS = 'cmloc_custom_css';
	const OPTION_ROUTE_BACKEND_EDIT_ALLOW = 'cmloc_route_backend_edit_allow';
	const OPTION_MAP_SEARCH_BOX_ENABLED = 'cmloc_map_search_box_enabled';
	const OPTION_MAP_TYPE_DEFAULT = 'cmloc_map_type_default';
	
	const ACCESS_GUEST = 'cmloc_guest';
	const ACCESS_USER = 'cmloc_user';
	const ACCESS_CAPABILITY = 'cmloc_capability';
	
	const ACTION_CLICK_REDIRECT = 'redirect';
	const ACTION_CLICK_TOOLTIP = 'tooltip';
	
	const INDEX_LIST_BOTTOM = 'bottom';
	const INDEX_LIST_BOTTOM_CONDENSED = 'bottom-condensed';
	const INDEX_LIST_LEFT = 'left';
	const INDEX_LIST_RIGHT = 'right';
	
	const ORDERBY_TITLE = 'post_title';
	const ORDERBY_CREATED = 'post_date';
	const ORDERBY_VIEWS = 'views';
	
	const ORDER_ASC = 'asc';
	const ORDER_DESC = 'desc';
	
	const UNIT_METERS = 'meters';
	const UNIT_FEET = 'feet';
	const UNIT_TEMP_F = 'temp_f';
	const UNIT_TEMP_C = 'temp_c';
	const FEET_TO_METER = 0.3048;
	const FEET_IN_MILE = 5280;
	
	const DEFAULT_INDEX_MAP_MARKER_CLICK = self::ACTION_CLICK_REDIRECT;
	const DEFAULT_INDEX_LIST_ITEM_CLICK = self::ACTION_CLICK_REDIRECT;
	const DEFAULT_INDEX_ORDERBY = self::ORDERBY_CREATED;
	const DEFAULT_INDEX_ORDER = self::ORDER_DESC;
	const DEFAULT_TOOLTIP_DESCRIPTION_CHARS = 0;
	
	const MAP_TYPE_ROADMAP = 'roadmap';
	const MAP_TYPE_SATELLITE = 'satellite';
	const MAP_TYPE_TERRAIN = 'terrain';
	const MAP_TYPE_HYBRID = 'hybrid';
	
	
	
	public static $categories = array(
		'general' => 'General',
		'appearance' => 'Appearance',
	);
	
	public static $subcategories = array(
		'general' => array(
			'navigation' => 'Navigation',
			'appearance' => 'Appearance',
			'api' => 'API Keys',
		),
		'appearance' => array(
			'general' => 'General',
			'index' => 'Index page',
			'location' => 'Location page',
			'editor' => 'Editor',
			'zip' => 'ZIP code radius filter',
			'css' => 'Custom CSS',
		),
		'access' => array(
			'access' => '',
		),
		'labels' => array(
			'other' => 'Other',
		),
	);
	
	
	public static function getOptionsConfig() {
		
		return apply_filters('cmloc_options_config', array(
			
			// Main
			self::OPTION_PERMALINK_PREFIX => array(
				'type' => self::TYPE_STRING,
				'default' => 'map-locations',
				'category' => 'general',
				'subcategory' => 'navigation',
				'title' => 'Permalink prefix',
				'desc' => 'Enter the prefix of the index and locations\' permalinks, eg. <kbd>map-locations</kbd> '
							. 'will give permalinks such as: <kbd>/<strong>map-locations</strong>/paris-trip</kbd>.',
			),
			self::OPTION_ROUTE_BACKEND_EDIT_ALLOW => array(
				'type' => self::TYPE_BOOL,
				'default' => 0,
				'category' => 'general',
				'subcategory' => 'navigation',
				'title' => 'Enable wp-admin edit page for locations',
				'desc' => 'If disabled, when you click the Edit link in the wp-admin you will be redirected to the front-end location edit page.<br />'
							. 'If enabled, the backend edit page will be available for locations.',
			),
			self::OPTION_PAGE_TEMPLATE => array(
				'type' => self::TYPE_SELECT,
				'options' => array(__CLASS__, 'getPageTemplatesOptions'),
				'default' => 'page.php',
				'category' => 'appearance',
				'subcategory' => 'general',
				'title' => 'Page template',
				'desc' => 'Choose the page template of the current theme or set default.',
			),
			self::OPTION_PAGE_TEMPLATE_OTHER => array(
				'type' => self::TYPE_STRING,
				'category' => 'appearance',
				'subcategory' => 'general',
				'title' => 'Other page template file',
				'desc' => 'Enter the other name of the page template if your template is not on the list above. '
				. 'This option have priority over the selected page template. Leave blank to reset.',
			),
			self::OPTION_PAGINATION_LIMIT => array(
				'type' => self::TYPE_INT,
				'default' => 10,
				'category' => 'appearance',
				'subcategory' => 'index',
				'title' => 'Locations per page',
				'desc' => 'Limit the locations visible on each page.',
			),
			self::OPTION_INDEX_TEXT_TOP => array(
				'type' => self::TYPE_TEXTAREA,
				'category' => 'appearance',
				'subcategory' => 'index',
				'title' => 'Text on top',
				'desc' => 'You can enter text which will be displayed on the top of the index page, below the page title.',
			),
			self::OPTION_MAP_TYPE_DEFAULT => array(
				'type' => self::TYPE_RADIO,
				'options' => array(
					self::MAP_TYPE_ROADMAP => 'road map',
					self::MAP_TYPE_TERRAIN => 'terrain',
					self::MAP_TYPE_SATELLITE => 'pure satellite without labels',
					self::MAP_TYPE_HYBRID => 'hybrid: satellite + labels',
				),
				'default' => self::MAP_TYPE_ROADMAP,
				'category' => 'appearance',
				'subcategory' => 'general',
				'title' => 'Default map view',
			),
			self::OPTION_UNIT_LENGTH => array(
				'type' => self::TYPE_RADIO,
				'options' => array(self::UNIT_METERS => 'meters', self::UNIT_FEET => 'feet'),
				'default' => self::UNIT_METERS,
				'category' => 'appearance',
				'subcategory' => 'general',
				'title' => 'Length units',
				'desc' => 'Used to display the trail\'s length or the location\'s altitude.',
			),
			self::OPTION_INDEX_ROUTE_PARAMS => array(
				'type' => self::TYPE_MULTICHECKBOX,
				'options' => self::getRouteParamsNames(),
				'default' => array_keys(self::getRouteParamsNames()),
				'category' => 'appearance',
				'subcategory' => 'index',
				'title' => 'Information visible on the index page',
				'desc' => 'Check which route parameters will be displayed on the index page on the route\'s snippet.',
			),
			self::OPTION_SINGLE_ROUTE_PARAMS => array(
				'type' => self::TYPE_MULTICHECKBOX,
				'options' => self::getRouteParamsNames(),
				'default' => array_keys(self::getRouteParamsNames()),
				'category' => 'appearance',
				'subcategory' => 'location',
				'title' => 'Information visible on the location\'s page',
				'desc' => 'Check which location parameters will be displayed on the single location\'s page.',
			),
			self::OPTION_ROUTE_INDEX_FEATURED_IMAGE => array(
				'type' => self::TYPE_RADIO,
				'default' => LocationSnippetShortcode::FEATURED_IMAGE,
				'options' => array(
					LocationSnippetShortcode::FEATURED_IMAGE => 'First gallery image',
					LocationSnippetShortcode::FEATURED_ICON => 'Icon',
					LocationSnippetShortcode::FEATURED_NONE => 'None',
				),
				'category' => 'appearance',
				'subcategory' => 'index',
				'title' => 'Location featured image',
				'desc' => 'Choose what kind of featured image to display on the index page.',
			),
			self::OPTION_ROUTE_DEFAULT_IMAGE => array(
				'type' => self::TYPE_STRING,
				'default' => App::url('asset/img/world-map-small.png'),
				'category' => 'appearance',
				'subcategory' => 'index',
				'title' => 'Location default image',
				'desc' => 'Enter the URL of the default featured image of the location map.',
			),
			self::OPTION_INDEX_MAP_MARKER_LABEL_SHOW => array(
				'type' => self::TYPE_BOOL,
				'default' => true,
				'category' => 'appearance',
				'subcategory' => 'index',
				'title' => 'Show label below marker',
				'desc' => 'Show text labels with location name below the location marker on the index page map.',
			),
			self::OPTION_ROUTE_MAP_MARKER_LABEL_SHOW => array(
				'type' => self::TYPE_BOOL,
				'default' => true,
				'category' => 'appearance',
				'subcategory' => 'location',
				'title' => 'Show label below marker',
				'desc' => 'Show text labels with location name below the location marker on the route page map.',
			),
			self::OPTION_EDITOR_DEFAULT_LAT => array(
				'type' => self::TYPE_STRING,
				'default' => '51',
				'category' => 'appearance',
				'subcategory' => 'editor',
				'title' => 'Editor default location\'s latitude',
				'desc' => 'Enter the latitude of the default location shown in the editor.',
			),
			self::OPTION_EDITOR_DEFAULT_LONG => array(
				'type' => self::TYPE_STRING,
				'default' => 0,
				'category' => 'appearance',
				'subcategory' => 'editor',
				'title' => 'Editor default location\'s longitude',
				'desc' => 'Enter the longitude of the default location shown in the editor.',
			),
			self::OPTION_EDITOR_DEFAULT_ZOOM => array(
				'type' => self::TYPE_SELECT,
				'options' => array_combine(range(0, 18), range(0, 18)),
				'default' => 5,
				'category' => 'appearance',
				'subcategory' => 'editor',
				'title' => 'Editor default zoom',
				'desc' => 'Greater zoom number = closer'
			),
			self::OPTION_EDITOR_RICH_TEXT_ENABLE => array(
				'type' => self::TYPE_BOOL,
				'default' => false,
				'category' => 'appearance',
				'subcategory' => 'editor',
				'title' => 'Enable rich text editor',
				'desc' => 'Allow users to use WYSIWYG editor when creating the location description. If disabled then simple textarea will be displayed.',
			),
			self::OPTION_CUSTOM_CSS => array(
				'type' => self::TYPE_TEXTAREA,
				'category' => 'appearance',
				'subcategory' => 'css',
				'title' => 'Custom CSS',
				'desc' => 'You can enter a custom CSS which will be embeded on every page that contains a CM Map Locations interface.',
			),
				
			// API
			self::OPTION_GOOGLE_MAPS_APP_KEY => array(
				'type' => self::TYPE_STRING,
				'category' => 'general',
				'subcategory' => 'api',
				'title' => 'Google Maps App Key',
				'desc' => 'Enter the Google Maps server app key.<br /><a target="_blank" '
					. 'href="https://console.developers.google.com/flows/enableapi?apiid=maps_backend&keyType=CLIENT_SIDE&reusekey=true">Get the API key from here</a>.',
			),
			
			
		));
		
	}
	
	
	static function getRouteParamsNames() {
		return array(
			'address' => 'Address',
			'postal_code' => 'Postal code',
			'created' => 'Created date',
		);
	}
	
	
	static function getAccessOptionsWithoutGuest() {
		return static::getAccessOptions(false);
	}
	
	
	static function getAccessOptions($guests = true) {
		if ($guests) {
			$result = array(self::ACCESS_GUEST => 'Everyone including guests');
		} else {
			$result = array();
		}
		return array_merge($result, array(
			self::ACCESS_USER => 'Only logged in users',
		),
		self::getRolesOptions(),
		array(
			self::ACCESS_CAPABILITY => 'Custom capability...',
		));
	}
	
	

	public static function getPageTemplate() {
		if ($template = Settings::getOption(Settings::OPTION_PAGE_TEMPLATE_OTHER)) {
			return $template;
		} else {
			$template = Settings::getOption(Settings::OPTION_PAGE_TEMPLATE);
			$available = Settings::getPageTemplatesOptions();
			if (!empty($template) AND isset($available[$template])) {
				return $template;
			} else {
				return 'page.php';
			}
		}
	}
	
	
	public static function getIndexMapMarkerClick() {
		$val = Settings::getOption(Settings::OPTION_INDEX_MAP_MARKER_CLICK);
		if (empty($val)) $val = self::DEFAULT_INDEX_MAP_MARKER_CLICK;
		return $val;
	}
	
	
	public static function getIndexListItemClick() {
		$val = Settings::getOption(Settings::OPTION_INDEX_LIST_ITEM_CLICK);
		if (empty($val)) $val = self::DEFAULT_INDEX_LIST_ITEM_CLICK;
		return $val;
	}
	
	
	public static function getIndexOrderBy() {
		$val = Settings::getOption(Settings::OPTION_INDEX_ORDERBY);
		if (empty($val)) $val = self::DEFAULT_INDEX_ORDERBY;
		return $val;
	}
	
	
	public static function getIndexOrder() {
		$val = Settings::getOption(Settings::OPTION_INDEX_ORDER);
		if (empty($val)) $val = self::DEFAULT_INDEX_ORDER;
		return $val;
	}
	
	
	public static function getTooltipDescriptionCharsNumber() {
		$val = Settings::getOption(Settings::OPTION_TOOLTIP_DESCRIPTION_CHARS);
		if (empty($val)) $val = self::DEFAULT_TOOLTIP_DESCRIPTION_CHARS;
		return $val;
	}
	
	
	
}
