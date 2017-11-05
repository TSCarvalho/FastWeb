<?php

namespace com\cminds\maplocations\controller;

use com\cminds\maplocations\model\Category;

use com\cminds\maplocations\model\Labels;

use com\cminds\maplocations\model\Location;

use com\cminds\maplocations\model\Attachment;

use com\cminds\maplocations\App;

use com\cminds\maplocations\model\Route;

use com\cminds\maplocations\model\Settings;

class DashboardController extends Controller {
	
	const EDITOR_NONCE = 'cmloc_route_editor';
	const DELETE_NONCE = 'cmloc_route_delete';
	const UPDATE_PARAMS_NONCE = 'cmloc_update_params';
	
	static $actions = array(
		'wp_enqueue_scripts' => array('priority' => PHP_INT_MAX),
		'admin_init',
	);
	static $ajax = array('cmloc_get_image_id', 'cmloc_route_params_save');
	static $filters = array(
		array('name' => 'wp_insert_post_data', 'args' => 2),
	);
	
	
	static function indexView(\WP_Query $query) {
		$query = new \WP_Query(array(
			'author' => get_current_user_id(),
			'post_type' => Route::POST_TYPE,
			'posts_per_page' => 9999,
			'post_status' => array('publish', 'draft'),
		));
		$routes = array_filter(array_map(array(App::namespaced('model\Route'), 'getInstance'), $query->posts));
		return self::loadFrontendView('index', compact('routes'));
	}
	
	
	static function wp_enqueue_scripts() {
		if (FrontendController::isDashboard()) {
			
			FrontendController::enqueueStyle();
			wp_enqueue_style('thickbox');
			wp_enqueue_style('cmloc-editor');
			
			wp_enqueue_script('cmloc-utils');
			wp_localize_script('cmloc-utils', 'CMLOC_Utils', array(
				'deleteConfirmText' => Labels::getLocalized('Do you really want to delete?'),
			));
		}
	}
	
	
	static function addView(\WP_Query $query) {
		if (Route::canCreate()) {
			return self::getEditorView(new Route());
		} else {
			return Labels::getLocalized('dashboard_access_denied_msg');
		}
	}
	
	
	static function editView(\WP_Query $query) {
// 		var_dump(Attachment::getByUrl('http://local.cm.brainusers.net/wp-content/uploads/cmdm/1560/serial-300x280.jpg'));exit;
		if ($route = FrontendController::getRoute($query)) {
			if ($route->canEdit()) {
				add_action('wp_footer', array(__CLASS__, 'loadGoogleChart'), PHP_INT_MAX);
				return self::getEditorView($route);
			} else {
				return Labels::getLocalized('dashboard_access_denied_msg');
			}
		} else return Labels::getLocalized('location_not_found');
	}
	
	
	static protected function getEditorView(Route $route = null) {
		
		if (!Settings::getOption(Settings::OPTION_GOOGLE_MAPS_APP_KEY)) {
			return Labels::getLocalized('missing_google_maps_app_key');
		}
		
		remove_action( 'media_buttons', 'media_buttons' );
		
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('media-upload');
		wp_enqueue_script('cmloc-editor');
		wp_localize_script('cmloc-editor', 'CMLOC_Editor_Settings', array(
			'newLocationLabel' => Labels::getLocalized('dashboard_new_location'),
			'defaultLat' => Settings::getOption(Settings::OPTION_EDITOR_DEFAULT_LAT),
			'defaultLong' => Settings::getOption(Settings::OPTION_EDITOR_DEFAULT_LONG),
			'defaultZoom' => Settings::getOption(Settings::OPTION_EDITOR_DEFAULT_ZOOM),
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'updateParamsNonce' => wp_create_nonce(self::UPDATE_PARAMS_NONCE),
		));
		wp_localize_script('cmloc-editor-images', 'CMLOC_Editor_Images', array(
			'title' => Labels::getLocalized('images'),
			'url' => admin_url('media-upload.php?type=image&TB_iframe=true'),
			'ajax_url' => admin_url('admin-ajax.php'),
		));
		
		$nonce = wp_create_nonce(self::EDITOR_NONCE);
		
		if ($route AND $route->getId()) {
			$formUrl = $route->getUserEditUrl();
			$locations = $route->getLocations();
		} else {
			$formUrl = RouteController::getDashboardUrl('add');
			$locations = null;
		}
		
		$out = '';
// 		$_GET['msg'] = 'location_save_success';
		if (!empty($_GET['msg'])) {
			$out .= static::getMessageView($_GET['msg']);
		}
		return $out . self::loadFrontendView('editor', compact('route', 'nonce', 'locations', 'formUrl'));
		
	}
	

	static function getMessageView($msg, $class = 'info') {
		$extra = '';
		if ('location_save_success' == $msg AND $route = FrontendController::getRoute()) {
			$extra = sprintf('<a href="%s">%s</a>', esc_attr($route->getPermalink()), Labels::getLocalized('menu_view_location') . ' &raquo;');
		}
		return static::loadFrontendView('msg', compact('msg', 'class', 'extra'));
	}
	
	
	static function processRequest() {
		
// 		self::fixLocationsMenuOrder();
		
// 		$routes = get_posts(array('post_type' => Route::POST_TYPE, 'posts_per_page' => -1));
// 		foreach ($routes as $route) {
// 			$route = new Route($route);
// 			$route->setCategories(array());
// 		}
		
		if (!is_admin()) {
			
			// Editor save request
			if (!empty($_POST) AND !empty($_POST[self::EDITOR_NONCE]) AND wp_verify_nonce($_POST[self::EDITOR_NONCE], self::EDITOR_NONCE)) {
				self::processSaveRoute();
			}
			
			// Delete route
			if (FrontendController::isDashboard() AND FrontendController::getDashboardPage() == FrontendController::DASHBOARD_DELETE) {
				if (!empty($_GET['nonce']) AND wp_verify_nonce($_GET['nonce'], DashboardController::DELETE_NONCE)) {
					self::processDeleteRoute();
				}
			}
			
		}
		
	}
	
	
	static function fixLocationsMenuOrder() {
		if (filter_input(INPUT_GET, 'cmtest') == '123') {
			global $wpdb;
			$locations = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type = %s AND menu_order <> 1", Location::POST_TYPE));
			if ($locations) {
				$wpdb->query("UPDATE $wpdb->posts SET menu_order = 1 WHERE ID IN (". implode(',', $locations) .")");
			}
			var_dump($locations);exit;
		}
	}
	
	
	
	static protected function getMaxWaypointsParam() {
		if (isset($_POST['max_waypoints']) AND is_numeric($_POST['max_waypoints'])) {
			$maxWaypoints = $_POST['max_waypoints'];
		} else {
			$maxWaypoints = 0;
		}
		if ($maxWaypoints < 1 OR $maxWaypoints > Route::WAYPOINTS_LIMIT) {
			$maxWaypoints = Route::WAYPOINTS_LIMIT;
		}
		return $maxWaypoints;
	}
	
	
	static protected function processSaveRoute() {
		
		$data = shortcode_atts(array(
			'name' => '',
			'description' => '',
			'images' => '',
			'status' => 'draft',
			'use-minor-length-units' => 1,
			'icon' => '',
		), $_POST);
			
		if (isset($_GET['id'])) {
			$route = Route::getInstance($_GET['id']);
		} else {
			$route = new Route();
		}
		
		$route->setTitle($data['name']);
		$route->setContent($data['description']);
		$route->setStatus($data['status']);
		$route->setAuthor(get_current_user_id());
		
		$id = $route->save();
		
		if ($id) {
			
			do_action('cmloc_clear_cache');
			
			$route->setImages($data['images']);
			$route->setIcon($data['icon']);
			
			self::processSaveRouteLocations($route);
			do_action('cmloc_route_after_save', $route);
		
			wp_redirect(add_query_arg('msg', 'location_save_success', $route->getUserEditUrl()));
		
		}
	}
	
	
	
	static protected function processSaveRouteLocations(Route $route) {
		
		$oldLocationsIds = $route->getLocationsIds();
		$newLocationsIds = array();
		
		if (!empty($_POST['locations']) AND is_array($_POST['locations']) AND !empty($_POST['locations']['id'])) {
			
			foreach ($_POST['locations']['id'] as $i => $id) {
				if ($i > 0) { // ommit the zero-indexed item which is only a placeholder.
					
					if ($id == 0) { // insert new location
						$location = new Location(array(
							'post_parent' => $route->getId(),
							'post_author' => get_current_user_id(),
							'post_type' => Location::POST_TYPE,
							'post_status' => 'inherit',
							'ping_status' => 'closed',
							'comment_status' => 'closed',
						));
					} else { // update location
						$location = Location::getInstance($id);
					}
					
					$location->setTitle($_POST['locations']['name'][$i]);
					$location->setContent($_POST['locations']['description'][$i]);
					$location->setMenuOrder($i);
					$id = $location->save();
					$newLocationsIds[] = $id;
					if ($id) {
						$location->setLat(floatval($_POST['locations']['lat'][$i]));
						$location->setLong(floatval($_POST['locations']['long'][$i]));
						$location->setLocationType($_POST['locations']['type'][$i]);
						$location->setAddress($_POST['locations']['address'][$i]);
						$location->setPostalCode(isset($_POST['locations']['postal-code'][$i]) ? $_POST['locations']['postal-code'][$i] : null);
						$location->setImages(isset($_POST['locations']['images'][$i]) ? $_POST['locations']['images'][$i] : array());
						$location->setPhoneNumber(isset($_POST['locations']['phone-number'][$i]) ? $_POST['locations']['phone-number'][$i] : '');
						$location->setWebsite(isset($_POST['locations']['website'][$i]) ? $_POST['locations']['website'][$i] : '');
						$location->setEmail(isset($_POST['locations']['email'][$i]) ? $_POST['locations']['email'][$i] : '');
					}
					
				}
			}
			
			
			// Remove unused locations
			$toRemove = array_diff($oldLocationsIds, array_filter($newLocationsIds));
			foreach ($toRemove as $id) {
				wp_delete_post($id, $force = true);
			}
			
		}
		
		$route->updateLocationsAltitudes();
		
	}
	
	
	
	static function cmloc_get_image_id() {
		$response = array('success' => 0, 'msg' => 'Error');
		if (!empty($_POST['url'])) {
			if (Attachment::isYouTubeUrl($_POST['url'])) { // YouTube
				$attachment = Attachment::createYouTube(0, $_POST['url']);
			} else {
				$url = $_POST['url'];
				$attachment = Attachment::getByUrl($url);
				if (empty($attachment)) {
					$url = preg_replace('~(\-[0-9]+x[0-9]+)(\.\w+)~', '$2', $url);
					$attachment = Attachment::getByUrl($url);
				}
			}
			
			if (!empty($attachment)) {
				$response = array(
					'success' => 1,
					'id' => $attachment->getId(),
					'url' => $attachment->isImage() ? $attachment->getImageUrl(Attachment::IMAGE_SIZE_FULL) : $attachment->getUrl(),
					'thumb' => $attachment->getImageUrl(Attachment::IMAGE_SIZE_THUMB),
				);
			} else {
				$response['msg'] = 'Attachment not found.';
			}
			
		}
		
		header('Content-type: application/json');
		echo json_encode($response);
		exit;
		
	}
	
	
	static function processDeleteRoute() {
		if (isset($_GET['id']) AND $route = Route::getInstance($_GET['id']) AND $route->canDelete()) {
			wp_delete_post($_GET['id'], $force = true);
			do_action('cmloc_clear_cache');
			wp_redirect(RouteController::getDashboardUrl('index'));
			exit;
		} else die('error');
	}
	
	
	/**
	 * Create slug from title.
	 * 
	 * @param array $data
	 * @param array $postarr
	 * @return array
	 */
	static function wp_insert_post_data($data, $postarr) {
		if ( $data['post_type'] == Route::POST_TYPE AND !in_array( $data['post_status'], array( 'draft', 'pending', 'auto-draft' ) ) ) {
			$data['post_name'] = sanitize_title( $data['post_title'] );
		}
		return $data;
	}
	
	
	static function admin_init() {
		global $pagenow;
		$post_id = intval(isset($_GET['post']) ? (int) $_GET['post'] : -1);
		if (Route::POST_TYPE == get_post_type($post_id) AND !empty($_GET['action']) AND $_GET['action'] == 'edit') {
			if (!Settings::getOption(Settings::OPTION_ROUTE_BACKEND_EDIT_ALLOW)) {
				wp_redirect(RouteController::getDashboardUrl('edit', array('id' => $post_id)));
			}
		}
		elseif ( isset($_GET['post_type']) AND $_GET['post_type'] == Route::POST_TYPE AND $pagenow == 'post-new.php' ) {
			wp_redirect(RouteController::getDashboardUrl('add'));
		}
	}
	
	
	
	static function loadGoogleChart() {
		?><script>if (typeof google != 'undefined') google.load('visualization', '1', {packages: ['columnchart']});</script><?php
	}
	
	
	static function cmloc_route_params_save() {
		if (!empty($_POST['nonce']) AND wp_verify_nonce($_POST['nonce'], self::UPDATE_PARAMS_NONCE)) {
			if (!empty($_POST['routeId']) AND $route = Route::getInstance($_POST['routeId'])) {
				if (!empty($_POST['distance'])) $route->setDistance($_POST['distance']);
				if (!empty($_POST['duration'])) $route->setDuration($_POST['duration']);
				if (!empty($_POST['minElevation'])) $route->setMinElevation($_POST['minElevation']);
				if (!empty($_POST['maxElevation'])) $route->setMaxElevation($_POST['maxElevation']);
				if (!empty($_POST['elevationGain'])) $route->setElevationGain($_POST['elevationGain']);
				if (!empty($_POST['elevationDescent'])) $route->setElevationDescent($_POST['elevationDescent']);
				if (!empty($_POST['avgSpeed'])) $route->setAvgSpeed($_POST['avgSpeed']);
				if (!empty($_POST['locations']) AND is_array($_POST['locations'])) {
					foreach ($_POST['locations'] as $data) {
						if ($location = Location::getInstance($data['id'])) {
							$location->setAddress($data['addr']);
						}
						Location::clearInstances();
					}
				}
				echo 'ok';
			}
		}
		echo 'error';
		exit;
	}
	
	
}
