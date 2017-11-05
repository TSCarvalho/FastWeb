<?php

namespace com\cminds\maplocations\model;

use com\cminds\maplocations\shortcode\LocationSnippetShortcode;

use com\cminds\maplocations\controller\RouteController;

use com\cminds\maplocations\controller\DashboardController;

use com\cminds\maplocations\model\Category;
use com\cminds\maplocations\App;

class Route extends PostType {
	
	const POST_TYPE = 'cmloc_object';
	
	const META_RATE = '_cmloc_route_rate';
	const META_RATE_USER_ID = '_cmloc_route_rate_user_id';
	const META_RATE_TIME = '_cmloc_route_rate_time';
	
	const META_DISTANCE = '_cmloc_distance';
	const META_DURATION = '_cmloc_duration';
	const META_AVG_SPEED = '_cmloc_avg_speed';
	const META_MAX_ELEVATION = '_cmloc_max_elevation';
	const META_MIN_ELEVATION = '_cmloc_min_elevation';
	const META_ELEVATION_GAIN = '_cmloc_elevation_gain';
	const META_ELEVATION_DESCENT = '_cmloc_elevation_descent';
	const META_DIRECTIONS_RESPONSE = '_cmloc_directions_response';
	const META_ELEVATION_RESPONSE = '_cmloc_elevation_response';
	const META_TRAVEL_MODE = '_cmloc_travel_mode';
	const META_USE_MINOR_LENGTH_UNITS = '_cmloc_use_minor_length_units';
	const META_SHOW_WEATHER_PER_LOCATION = '_cmloc_show_weather_per_location';
	const META_PATH_COLOR = '_cmloc_path_color';
	const META_OVERVIEW_PATH = '_cmloc_overview_path';
	const META_ICON = '_cmloc_icon';
	const META_VIEWS = '_cmloc_views';
	
	const WAYPOINTS_LIMIT = 512;
	
	const DEFAULT_TRAVEL_MODE = 'WALKING';
	
	const TRANSIENT_GEOLOCATION_BY_ADDR_CACHE = 'cmloc_geoloc_by_addr_cache';
	
	static $travelModes = array('WALKING', 'BICYCLING', 'DRIVING', 'DIRECT');
	
	
	static protected $postTypeOptions = array(
		'label' => 'Route',
		'public' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_admin_bar' => true,
		'show_in_menu' => App::PREFIX,
		'hierarchical' => false,
		'supports' => array('title', 'editor'),
		'has_archive' => true,
		'taxonomies' => array(Category::TAXONOMY),
	);
	
	
	protected $locationCache = null;
	protected $categoriesCache = null;
	protected $iconUrlCache = null;
	protected $addressCache = null;
	
	
	static protected function getPostTypeLabels() {
		$singular = ucfirst(Labels::getLocalized('location'));
		$plural = ucfirst(Labels::getLocalized('locations'));
		return array(
			'name' => $plural,
            'singular_name' => $singular,
            'add_new' => sprintf(__('Add %s', App::SLUG), $singular),
            'add_new_item' => sprintf(__('Add New %s', App::SLUG), $singular),
            'edit_item' => sprintf(__('Edit %s', App::SLUG), $singular),
            'new_item' => sprintf(__('New %s', App::SLUG), $singular),
            'all_items' => $plural,
            'view_item' => sprintf(__('View %s', App::SLUG), $singular),
            'search_items' => sprintf(__('Search %s', App::SLUG), $plural),
            'not_found' => sprintf(__('No %s found', App::SLUG), $plural),
            'not_found_in_trash' => sprintf(__('No %s found in Trash', App::SLUG), $plural),
            'menu_name' => App::getPluginName()
		);
	}
	
	
	static function init() {
		static::$postTypeOptions['rewrite'] = array('slug' => Settings::getOption(Settings::OPTION_PERMALINK_PREFIX));
		parent::init();
	}
	
	
	
	/**
	 * Get instance
	 * 
	 * @param WP_Post|int $post Post object or ID
	 * @return com\cminds\maplocations\model\Route
	 */
	static function getInstance($post) {
		return parent::getInstance($post);
	}
	
	
	
	function getEditUrl() {
		return admin_url(sprintf('post.php?action=edit&post=%d',
			$this->getId()
		));
	}
	
	
	function getCategories($fields = TaxonomyTerm::FIELDS_MODEL, $params = array()) {
		$atts = md5(serialize(func_get_args()));
		if (empty($this->categoriesCache[$atts])) {
			$this->categoriesCache[$atts] = Category::getPostTerms($this->getId(), $fields, $params);
		}
		return $this->categoriesCache[$atts];
	}
	
	
	function getTags($fields = TaxonomyTerm::FIELDS_MODEL, $params = array()) {
		return RouteTag::getPostTerms($this->getId(), $fields, $params);
	}
	
	
	function setCategories($categoriesIds) {
		return wp_set_post_terms($this->getId(), $categoriesIds, Category::TAXONOMY, $append = false);
	}
	
	
	function setCategoriesNames($categoriesNames) {
		if (!is_array($categoriesNames)) {
			$categoriesNames = array_filter(array_map('trim', explode(',', $categoriesNames)));
		}
		$ids = array();
		foreach ($categoriesNames as $categoryName) {
			if ($category = Category::getByName($categoryName)) {
				$ids[] = $category->getId();
			}
		}
		return $this->setCategories($ids);
	}
	
	
	
	function importCategoriesNames($categoriesNames) {
		if (!is_array($categoriesNames)) {
			$categoriesNames = array_filter(array_map('trim', explode(',', $categoriesNames)));
		}
		$ids = array();
		foreach ($categoriesNames as $categoryName) {
			if ($category = Category::getByName($categoryName)) {
				$ids[] = $category->getId();
			} else {
				$term = wp_insert_term($categoryName, Category::TAXONOMY);
				if ($term AND !is_wp_error($term) AND is_array($term) AND isset($term['term_id'])) {
					$ids[] = $term['term_id'];
				}
			}
		}
		return $this->setCategories($ids);
	}
	
	
	function addDefaultCategory() {
		$term = get_term('General', Category::TAXONOMY);
		if (empty($term)) {
			$terms = get_terms(array(Category::TAXONOMY), array('hide_empty' => false));
			if (!empty($terms)) {
				$term = reset($terms);
			}
		}
		if (!empty($term)) {
			wp_set_post_terms($this->getId(), $term->term_id, Category::TAXONOMY);
		}
	}
	
	
	function getUserEditUrl() {
		return RouteController::getDashboardUrl('edit', array('id' => $this->getId()));
	}
	
	
	function getUserDeleteUrl() {
		return RouteController::getDashboardUrl('delete', array(
			'id' => $this->getId(),
			'nonce' => wp_create_nonce(DashboardController::DELETE_NONCE),
		));
	}
	
	
	function getImages() {
		if ($id = $this->getId()) {
			return Attachment::getForPost($id);
		} else {
			return array();
		}
	}
	
	
	function getMapThumbUrl($size) {
		$pathParams = array('weight' => 3, 'color' => $this->getPathColor(), 'enc' => $this->getOverviewPath());
		foreach ($pathParams as $name => &$val) {
			$val = $name .':'. $val;
		}
		$pathParams = implode('|', $pathParams);
		return add_query_arg(urlencode_deep(array(
			'size' => $size,
			'maptype' => 'roadmap',
			'key' => Settings::getOption(Settings::OPTION_GOOGLE_MAPS_APP_KEY),
			'path' => $pathParams,
		)), 'https://maps.googleapis.com/maps/api/staticmap');
	}
	
	
	function getImagesIds() {
		if ($id = $this->getId()) {
			return get_posts(array(
				'posts_per_page' => -1,
				'post_type' => Attachment::POST_TYPE,
				'post_status' => 'any',
				'post_parent' => $id,
				'fields' => 'ids',
				'orderby' => 'menu_order',
				'order' => 'asc',
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
			));
		} else {
			return array();
		}
	}
	
	
	function setImages($images) {
		global $wpdb;
		
		if (!is_array($images)) {
			$images = array_filter(explode(',', $images));
		}
		
		$currentIds = $this->getImagesIds();
		$postedImagesIds = array_filter(array_map('intval', array_map('trim', $images)));
		
		$toAdd = array_diff($postedImagesIds, $currentIds);
		$toDelete = array_diff($currentIds, $postedImagesIds);
		
		if (!empty($toAdd)) $wpdb->query("UPDATE $wpdb->posts SET post_parent = ". intval($this->getId()) ." WHERE ID IN (" . implode(',', $toAdd) . ")");
		if (!empty($toDelete)) $wpdb->query("UPDATE $wpdb->posts SET post_parent = 0 WHERE ID IN (" . implode(',', $toDelete) . ")");
		
		// Change the sorting order
		foreach ($images as $i => $id) {
			$wpdb->query("UPDATE $wpdb->posts SET menu_order = ". intval($i) ." WHERE ID = ". intval($id) ." LIMIT 1");
		}
		
	}
	
	
	
	function getLocationsIds() {
		if ($id = $this->getId()) {
			return get_posts(array(
				'fields' => 'ids',
				'post_type' => Location::POST_TYPE,
				'post_parent' => $id,
				'post_status' => 'any',
				'posts_per_page' => -1,
				'orderby' => 'menu_order',
				'order' => 'asc',
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
			));
		} else return array();
	}
	
	
	function getLocations() {
		if ($id = $this->getId()) {
			return array_map(array(App::namespaced('model\Location'), 'getInstance'), get_posts(array(
				'post_type' => Location::POST_TYPE,
				'post_parent' => $id,
				'post_status' => 'any',
				'posts_per_page' => -1,
				'orderby' => 'menu_order',
				'order' => 'asc',
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
			)));
		} else return array();
	}
	
	
	
	function getJSLocations() {
		$route = $this;
		return array_map(function(Location $location) use ($route) {
			return array(
				'id' => $location->getId(),
				'name' => $route->getTitle(),
				'lat' => $location->getLat(),
				'long' => $location->getLong(),
				'description' => $location->getContent(),
				'type' => $location->getLocationType(),
				'address' => $location->getAddress(),
				'postal_code' => $location->getPostalCode(),
				'phone_number' => $location->getPhoneNumber(),
				'website' => $location->getWebsite(),
				'email' => $location->getEmail(),
				'icon' => $location->getRoute()->getIconUrl(),
				'images' => array_map(function(Attachment $image) {
					return array(
						'id' => $image->getId(),
						'url' => $image->getImageUrl(Attachment::IMAGE_SIZE_FULL),
						'thumb' => $image->getImageUrl(Attachment::IMAGE_SIZE_THUMB)
					);
				}, $location->getImages())
			);
		}, $this->getLocations());
	}
	
	
	static function getIndexMapJSLocations(\WP_Query $query) {
		global $wpdb;
		
// 		var_dump($query->request);
// 		var_dump($query->is_main_query());
// 		$query->get_posts();
// 		var_dump($query->is_main_query());
// 		var_dump($query->request);exit;
		
		$joinString = $wpdb->prepare("
			JOIN $wpdb->posts l ON l.post_parent = $wpdb->posts.ID AND l.post_type = %s
			JOIN $wpdb->postmeta lm_lat ON lm_lat.post_id = l.ID AND lm_lat.meta_key = %s
			JOIN $wpdb->postmeta lm_lon ON lm_lon.post_id = l.ID AND lm_lon.meta_key = %s
			LEFT JOIN $wpdb->postmeta rm_pc ON rm_pc.post_id = $wpdb->posts.ID AND rm_pc.meta_key = %s
			LEFT JOIN $wpdb->postmeta rm_op ON rm_op.post_id = $wpdb->posts.ID AND rm_op.meta_key = %s",
			Location::POST_TYPE,
			Location::META_LAT,
			Location::META_LONG,
			Route::META_PATH_COLOR,
			Route::META_OVERVIEW_PATH
		);
		
		$selectString = "SELECT SQL_CALC_FOUND_ROWS $wpdb->posts.ID AS id, $wpdb->posts.post_title AS name,
				lm_lat.meta_value AS lat, lm_lon.meta_value AS `long`,
				rm_pc.meta_value AS `pathColor`, rm_op.meta_value AS `path`";
		
		$sql = $query->request;
// 		var_dump($sql);
		$sql = preg_replace('~LIMIT [0-9]+, [0-9]+~i', '', $sql);
		$sql = preg_replace('~^SELECT SQL_CALC_FOUND_ROWS .+ FROM \w+ ~i', $selectString . " FROM $wpdb->posts ", $sql);
		$sql = str_replace('WHERE 1=1', $joinString . PHP_EOL . "WHERE 1=1", $sql);
// 		var_dump($sql);
		$routes = $wpdb->get_results($sql, ARRAY_A);
		
		/* $locQuery = new \WP_Query(array_merge($query->query, array(
			'post_type' => Route::POST_TYPE,
			'fields' => 'ids',
			'posts_per_page' => -1,
		)));
		$postsIds = $locQuery->get_posts();
		
		if (empty($postsIds)) {
			return array();
		}
		
		$sql = $wpdb->prepare("SELECT r.ID AS id, r.post_title AS name,
				lm_lat.meta_value AS lat, lm_lon.meta_value AS `long`,
				rm_pc.meta_value AS `pathColor`, rm_op.meta_value AS `path`
			FROM $wpdb->posts r
			JOIN $wpdb->posts l ON l.post_parent = r.ID AND l.post_type = %s AND l.menu_order = 1
			JOIN $wpdb->postmeta lm_lat ON lm_lat.post_id = l.ID AND lm_lat.meta_key = %s
			JOIN $wpdb->postmeta lm_lon ON lm_lon.post_id = l.ID AND lm_lon.meta_key = %s
			LEFT JOIN $wpdb->postmeta rm_pc ON rm_pc.post_id = r.ID AND rm_pc.meta_key = %s
			LEFT JOIN $wpdb->postmeta rm_op ON rm_op.post_id = r.ID AND rm_op.meta_key = %s
			WHERE r.ID IN (" . implode(',', $postsIds) . ")
			",
			Location::POST_TYPE,
			Location::META_LAT,
			Location::META_LONG,
			Route::META_PATH_COLOR,
			Route::META_OVERVIEW_PATH
		);
		
		$routes = $wpdb->get_results($sql, ARRAY_A); */
		
		foreach ($routes as $i => $row) {
			/* @var $route Route */
			$route = Route::getInstance($row['id']);
			$routes[$i]['permalink'] = $route->getPermalink();
			$routes[$i]['type'] = Location::TYPE_LOCATION;
			$routes[$i]['icon'] = $route->getIconUrl();
			$routes[$i]['infowindow'] = RouteController::getInfoWindowView($route);
			Route::clearInstances();
		}
		
		return $routes;
		
	}
	
	
	function canEdit($userId = null) {
		if (is_null($userId)) $userId = get_current_user_id();
		return (user_can($userId, 'manage_options') OR ($userId == $this->getAuthorId() AND self::canCreate($userId)));
	}
	
	
	static function canCreate($userId = null) {
		$access = Settings::getOption(Settings::OPTION_ACCESS_MAP_CREATE);
		if (empty($access)) $access = Settings::ACCESS_USER;
		return self::checkAccess(
			$access,
			$capability = Settings::getOption(Settings::OPTION_ACCESS_MAP_CREATE_CAP),
			$userId
		);
	}
	
	
	function canView($userId = null) {
		$access = Settings::getOption(Settings::OPTION_ACCESS_MAP_VIEW);
		if (empty($access)) $access = Settings::ACCESS_GUEST;
		return self::checkAccess(
			$access,
			$capability = Settings::getOption(Settings::OPTION_ACCESS_MAP_VIEW_CAP),
			$userId
		);
	}
	
	
	static function canViewIndex($userId = null) {
		$access = Settings::getOption(Settings::OPTION_ACCESS_MAP_INDEX);
		if (empty($access)) $access = Settings::ACCESS_GUEST;
		return self::checkAccess(
			$access,
			$capability = Settings::getOption(Settings::OPTION_ACCESS_MAP_INDEX_CAP),
			$userId
		);
	}
	
	
	function canDelete($userId = null) {
		return $this->canEdit($userId);
	}
	
	
	function getRate() {
		global $wpdb;
		return $wpdb->get_var($wpdb->prepare("SELECT SUM(meta_value)/COUNT(*) FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s",
			$this->getId(),
			self::META_RATE
		));
	}
	
	
	function canRate() {
		$userId = is_user_logged_in();
		return !empty($userId);
	}
	
	
	function didUserRate() {
		global $wpdb;
		$userId = get_current_user_id();
		if (empty($userId)) return null;
		$sql = $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->postmeta WHERE post_id = %d AND meta_key LIKE %s AND meta_value = %d",
			$this->getId(),
			self::META_RATE_USER_ID .'%',
			$userId
		);
		$count = $wpdb->get_var($sql);
		return ($count > 0);
	}
	
	
	function rate($rate) {
		$id = add_post_meta($this->getId(), self::META_RATE, $rate, $unique= false);
		if ($id) {
			add_post_meta($this->getId(), self::META_RATE_TIME .'_'. $id, time());
			add_post_meta($this->getId(), self::META_RATE_USER_ID .'_'. $id, get_current_user_id());
			return $id;
		}
	}
	
	
	function getVotesNumber() {
		global $wpdb;
		return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s",
			$this->getId(),
			self::META_RATE
		));
	}
	
	
	function getRelatedRoutes($limit = 5) {
		return array_map(array(get_called_class(), 'getInstance'), get_posts(array(
			'posts_per_page' => $limit,
			'post_type' => static::POST_TYPE,
			'post_status' => 'publish',
			'orderby' => 'id',
			'order' => 'desc',
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
// 			'category' => implode(',', $this->getCategories(Category::FIELDS_ID_SLUG)),
			'exclude' => $this->getId(),
			'tax_query' => array(
				array(
					'taxonomy' => Category::TAXONOMY,
					'field' => 'id',
					'terms' => $this->getCategories(Category::FIELDS_IDS),
					'include_children' => false,
				),
				array(
					'taxonomy' => Tag::TAXONOMY,
					'field' => 'id',
					'terms' => $this->getTags(Tag::FIELDS_IDS),
				),
				'relation' => 'OR',
			),
		)));
	}
	
	
	function updateLocationsAltitudes() {
		return;
		$locations = $this->getLocations();
		if (!empty($locations)) {
			$result = Location::downloadEvelations($locations);
			foreach ($locations as $i => $location) {
				if (isset($result['results'][$i]) AND $location->getAltitude() != $result['results'][$i]['elevation']) {
					$location->setAltitude($result['results'][$i]['elevation']);
				}
			}
		}
	}
	
	
	
	static function checkAccess($access, $capability, $userId = null) {
		if (is_null($userId)) $userId = get_current_user_id();
		
		if (user_can($userId, 'manage_options')) {
			return true;
		}
		
		switch ($access) {
			case Settings::ACCESS_GUEST:
				return true;
				break;
			case Settings::ACCESS_USER:
				return !empty($userId);
				break;
			case Settings::ACCESS_CAPABILITY:
				return (!empty($userId) AND user_can($userId, $capability));
			default:
				if (!empty($userId) AND $user = get_userdata($userId)) {
					return in_array($access, $user->roles);
				}
				break;
		}
		return false;
	}
	
	
	function getDistance() {
		return intval(get_post_meta($this->getId(), self::META_DISTANCE, $single = true));
	}
	
	
	
	function getShortDescription($maxlen = 100) {
		$content = preg_replace('/[\s\n\r\t]+/', ' ', strip_tags($this->getContent()));
		if (strlen($content) > $maxlen) {
			$content = substr($content, 0, $maxlen) . '...';
		}
		return $content;
	}
	
	
	function getFormattedDistance() {
		
		$dist = $this->getDistance();
		$useMinor = $this->useMinorLengthUnits();
		
		if (Settings::UNIT_FEET == Settings::getOption(Settings::OPTION_UNIT_LENGTH)) {
			$num = $dist/Settings::FEET_TO_METER;
			if (!$useMinor AND $num > Settings::FEET_IN_MILE) {
				return number_format(round($num/Settings::FEET_IN_MILE)) .' miles';
			} else {
				return number_format(floor($num)) .' ft';
			}
		} else {
			if (!$useMinor AND $dist > 2000) {
				return round($dist/1000) .' km';
			} else {
				return $dist .' m';
			}
		}
		
	}
	
	
	static function formatLength($dist) {
		if (Settings::UNIT_FEET == Settings::getOption(Settings::OPTION_UNIT_LENGTH)) {
			$num = $dist/Settings::FEET_TO_METER;
			if ($num > Settings::FEET_IN_MILE) {
				return number_format(round($num/Settings::FEET_IN_MILE)) .' miles';
			} else {
				return number_format(floor($num)) .' ft';
			}
		} else {
			if ($dist > 2000) {
				return round($dist/1000) .' km';
			} else {
				return $dist .' m';
			}
		}
	}
	
	
	static function formatElevation($dist) {
		if (Settings::UNIT_FEET == Settings::getOption(Settings::OPTION_UNIT_LENGTH)) {
			$num = round($dist/Settings::FEET_TO_METER);
			return number_format($num) .' ft';
		} else {
			return $dist .' m';
		}
	}
	
	
	static function formatSpeed($meterPerSec) {
		if (Settings::UNIT_FEET == Settings::getOption(Settings::OPTION_UNIT_LENGTH)) {
			return round($meterPerSec/Settings::FEET_TO_METER/Settings::FEET_IN_MILE*3600) . ' mph';
		} else {
			return round($meterPerSec * 3.6) . ' km/h';
		}
	}
	
	
	static function formatTime($sec) {
		$num = $sec;
		$label = round($num) .' s';
		if ($num > 60) {
			$num /= 60;
			$label = round($num) .' min';
		}
		if ($num > 60) {
			$label = floor($num/60) .' h '. ($num%60) .' min ';
		}
		return $label;
	}
	
	function setDistance($dist) {
		return update_post_meta($this->getId(), self::META_DISTANCE, $dist);
	}
	
	
	function getDuration() {
		return intval(get_post_meta($this->getId(), self::META_DURATION, $single = true));
	}
	
	function setDuration($durationSec) {
		return update_post_meta($this->getId(), self::META_DURATION, $durationSec);
	}
	
	function getOverviewPath() {
		return get_post_meta($this->getId(), self::META_OVERVIEW_PATH, $single = true);
	}
	
	function setOverviewPath($path) {
		return update_post_meta($this->getId(), self::META_OVERVIEW_PATH, $path);
	}
	
	function getAvgSpeed() {
		return intval(get_post_meta($this->getId(), self::META_AVG_SPEED, $single = true));
	}
	
	/**
	 * Set average speed.
	 * 
	 * @param float $speed AVG speed in meters per second.
	 */
	function setAvgSpeed($meterPerSec) {
		return update_post_meta($this->getId(), self::META_AVG_SPEED, $meterPerSec);
	}
	
	function getMaxElevation() {
		return intval(get_post_meta($this->getId(), self::META_MAX_ELEVATION, $single = true));
	}
	
	function setMaxElevation($maxElevation) {
		return update_post_meta($this->getId(), self::META_MAX_ELEVATION, $maxElevation);
	}
	
	function getMinElevation() {
		return intval(get_post_meta($this->getId(), self::META_MIN_ELEVATION, $single = true));
	}
	
	function setMinElevation($minElevation) {
		return update_post_meta($this->getId(), self::META_MIN_ELEVATION, $minElevation);
	}
	
	function getElevationGain() {
		return intval(get_post_meta($this->getId(), self::META_ELEVATION_GAIN, $single = true));
	}
	
	function setElevationGain($elevationGain) {
		return update_post_meta($this->getId(), self::META_ELEVATION_GAIN, $elevationGain);
	}
	
	
	function getElevationDescent() {
		return intval(get_post_meta($this->getId(), self::META_ELEVATION_DESCENT, $single = true));
	}
	
	function setElevationDescent($elevationDescent) {
		return update_post_meta($this->getId(), self::META_ELEVATION_DESCENT, $elevationDescent);
	}
	
	
	function setDirectionResponse($response) {
		$val = array('json' => $response, 'time' => time());
		return add_post_meta($this->getId(), self::META_DIRECTIONS_RESPONSE, $val, $unique = false);
	}
	
	
	function setElevationResponse($response) {
		$val = array('json' => $response, 'time' => time());
		return add_post_meta($this->getId(), self::META_ELEVATION_RESPONSE, $val, $unique = false);
	}
	
	function getTravelMode() {
		$val = get_post_meta($this->getId(), self::META_TRAVEL_MODE, $single = true);
		if (empty($val)) $val = self::DEFAULT_TRAVEL_MODE;
		return $val;
	}
	
	function setTravelMode($mode) {
		return update_post_meta($this->getId(), self::META_TRAVEL_MODE, $mode);
	}
	
	
	function useMinorLengthUnits() {
		return (1 == $this->getPostMeta(self::META_USE_MINOR_LENGTH_UNITS));
	}
	
	
	function setMinorLengthUnits($use) {
		return $this->setPostMeta(self::META_USE_MINOR_LENGTH_UNITS, intval($use));
	}

	function getPathColor() {
		$val = $this->getPostMeta(self::META_PATH_COLOR);
		return (strlen($val) > 0 ? $val : '#3377FF');
	}
	
	
	function setPathColor($value) {
		return $this->setPostMeta(self::META_PATH_COLOR, $value);
	}
	
	
	function getIcon() {
		return $this->getPostMeta(self::META_ICON);
	}
	
	
	function getIconUrl() {
		if (empty($this->iconUrlCache)) {
			$icon = $this->getPostMeta(self::META_ICON);
			if (empty($icon)) {
				if ($categories = $this->getCategories() AND $category = reset($categories)) {
					/* @var $category Category */
					$icon = $category->getIcon();
				}
			}
			if (empty($icon)) $icon = null;
			$this->iconUrlCache = apply_filters('cmloc_route_icon_url', $icon, $this);
		}
		return $this->iconUrlCache;
	}
	
	
	function setIconUrlCache($url) {
		$this->iconUrlCache = $url;
		return $this;
	}
	
	
	function setIcon($value) {
		return $this->setPostMeta(self::META_ICON, $value);
	}
	
	
	function showWeatherPerLocation() {
		return (1 == $this->getPostMeta(self::META_SHOW_WEATHER_PER_LOCATION));
	}
	
	
	function setWeatherPerLocation($val) {
		return $this->setPostMeta(self::META_SHOW_WEATHER_PER_LOCATION, intval($val));
	}
	
	
	static function getPaginationLimit() {
		return Settings::getOption(Settings::OPTION_PAGINATION_LIMIT);
	}
	
	
	function getPostMetaKey($name) {
		return $name;
	}
	
	
	function getAddress() {
		if (empty($this->addressCache)) {
			if ($location = $this->getLocation()) {
				$this->addressCache = $location->getAddress();
			}
		}
		return $this->addressCache;
	}
	
	
	/**
	 * Returns the location instance.
	 * 
	 * @return Location
	 */
	function getLocation() {
		if (empty($this->locationCache)) {
			$locations = $this->getLocations();
			if ($location = reset($locations)) {
				$this->setLocationCache($location);
			} else {
				return null;
			}
		}
		return $this->locationCache;
	}
	
	
	function setLocationCache(Location $location) {
		$this->locationCache = $location;
		return $this;
	}
	
	
	function getPostalCode() {
		if ($location = $this->getLocation()) {
			return $location->getPostalCode();
		}
	}
	
	
	function setViews($val) {
		update_post_meta($this->getId(), self::META_VIEWS, $val);
		return $this;
	}
	
	
	function getViews() {
		return get_post_meta($this->getId(), self::META_VIEWS, $single = true);
	}
	
	
	function incrementViews() {
		$this->setViews($this->getViews() + 1);
	}
	
	
	function save() {
		$id = $this->getId();
		$result = parent::save();
		if (!$id) {
			$this->setViews(0);
		}
		return $result;
	}
	
	
	
	static function registerQueryOrder(\WP_Query $query, $orderby = null, $order = null) {
		$orderby = Settings::getIndexOrderBy();
		$order = Settings::getIndexOrder();
		switch ($orderby) {
			case Settings::ORDERBY_VIEWS:
				$query->set('meta_key', self::META_VIEWS);
				$orderby = 'meta_value_num';
				break;
		}
		$query->set('orderby', $orderby);
		$query->set('order', $order);
	}
	
	
	static function findLocationByAddress($address) {
	
		if (empty($address)) return array();
		
		$cache = get_transient(static::TRANSIENT_GEOLOCATION_BY_ADDR_CACHE);
		if (is_array($cache) AND isset($cache[$address])) {
			return $cache[$address];
		}
	
		$url = 'http://maps.googleapis.com/maps/api/geocode/json';
	
		$url = add_query_arg(urlencode_deep(array(
			'address' => $address,
		)), $url);

		$opts = array('http' => array(
			'timeout' => 10,
		));
		$context  = stream_context_create($opts);
		$result = @file_get_contents($url, false, $context, -1, 1024*50);
		$result = json_decode($result, true);

		if (is_array($result) AND !empty($result['results']) AND !empty($result['status']) AND $result['status'] == 'OK') {
			$coords = array($result['results'][0]['geometry']['location']['lat'], $result['results'][0]['geometry']['location']['lng']);
			$cache[$address] = $coords;
			set_transient(static::TRANSIENT_GEOLOCATION_BY_ADDR_CACHE, $cache);
			return $coords;
		}
	
	}
	
	
	function getPermalink() {
		return site_url('/' . Settings::getOption(Settings::OPTION_PERMALINK_PREFIX) . '/' . $this->post->post_name);
	}
	
}
