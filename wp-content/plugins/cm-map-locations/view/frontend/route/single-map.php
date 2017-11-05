<?php

use com\cminds\maplocations\App;

use com\cminds\maplocations\helper\RouteView;

use com\cminds\maplocations\model\Labels;
use com\cminds\maplocations\model\Route;

use com\cminds\maplocations\controller\RouteController;

add_action('wp_footer', array(App::namespaced('controller\\DashboardController'), 'loadGoogleChart'), PHP_INT_MAX);

/* @var $route Route */

?>

<ul class="cmloc-inline-nav cmloc-toolbar">
	<li><a href="<?php echo esc_attr(RouteView::getRefererUrl()); ?>" title="<?php echo esc_attr(Labels::getLocalized('location_backlink'));
		?>" class="dashicons dashicons-controls-back"></a></li>
	<li style="float:right"><a class="cmloc-map-fullscreen-btn dashicons dashicons-editor-expand" href="#" title="<?php echo esc_attr(Labels::getLocalized('show_fullscreen_title')); ?>"></a></li>
</ul>

<div class="cmloc-location-map-canvas-outer" style="display:<?php echo (!isset($atts['map']) OR $atts['map'] == 1) ? 'block' : 'none'; ?>">
	<div id="<?php echo $mapId; ?>" class="cmloc-location-map-canvas"></div>
</div>

<script type="text/javascript">
jQuery(function() {
	var mapId = <?php echo json_encode($mapId); ?>;
	var locations = <?php echo json_encode($route->getJSLocations()); ?>;
	var pathColor = <?php echo json_encode($route->getPathColor()); ?>;
	document.getElementById(mapId).cmloc_route = new CMLOC_Route(mapId, locations, pathColor);
});
</script>

