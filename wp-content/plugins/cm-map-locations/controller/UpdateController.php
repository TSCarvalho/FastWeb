<?php

namespace com\cminds\maplocations\controller;

use com\cminds\maplocations\model\Route;

use com\cminds\maplocations\App;

class UpdateController extends Controller {
	
	const OPTION_NAME = 'cmloc_update_methods';

	static function bootstrap() {
		global $wpdb;
		
		if (defined('DOING_AJAX') && DOING_AJAX) return;
		
		$updates = get_option(self::OPTION_NAME);
		if (empty($updates)) $updates = array();
		$count = count($updates);
		
		$methods = get_class_methods(__CLASS__);
		foreach ($methods as $method) {
			if (preg_match('/^update((_[0-9]+)+)$/', $method, $match)) {
				if (!in_array($method, $updates)) {
					call_user_func(array(__CLASS__, $method));
					$updates[] = $method;
				}
			}
		}
		
		if ($count != count($updates)) {
			update_option(self::OPTION_NAME, $updates, $autoload = true);
		}
		
	}
	

	static function update_1_0_2() {
		global $wpdb;
		
		// Update Route's postmeta views
		$routesIds = $wpdb->get_col($wpdb->prepare("SELECT route.ID FROM $wpdb->posts route
			LEFT JOIN $wpdb->postmeta m ON m.post_id = route.ID AND m.meta_key = %s
			WHERE route.post_type = %s AND (m.meta_value IS NULL OR m.meta_value = '')",
			Route::META_VIEWS, Route::POST_TYPE));
		
		foreach ($routesIds as $id) {
			if ($route = Route::getInstance($id)) {
				$route->setViews(0);
			}
			unset($route);
			Route::clearInstances();
		}
	}
	
}
