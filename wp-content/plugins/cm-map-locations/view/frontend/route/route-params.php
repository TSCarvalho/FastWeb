<?php

use com\cminds\maplocations\model\Labels;

use com\cminds\maplocations\model\Route;

?><ul class="cmloc-location-params">
	<li class="cmloc-route-distance">
		<strong><?php echo Labels::getLocalized('route_distance'); ?></strong>
		<span><?php echo $route->getFormattedDistance(); ?></span>
	</li>
	<li class="cmloc-route-duration">
		<strong><?php echo Labels::getLocalized('route_duration'); ?></strong>
		<span><?php echo Route::formatTime($route->getDuration()); ?></span>
	</li>
	<li class="cmloc-route-avg-speed">
		<strong><?php echo Labels::getLocalized('route_avg_speed'); ?></strong>
		<span><?php echo Route::formatSpeed($route->getAvgSpeed()); ?></span>
	</li>
	<li class="cmloc-min-elevation">
		<strong><?php echo Labels::getLocalized('route_min_elevation'); ?></strong>
		<span><?php echo Route::formatElevation($route->getMinElevation()); ?></span>
	</li>
	<li class="cmloc-max-elevation">
		<strong><?php echo Labels::getLocalized('route_max_elevation'); ?></strong>
		<span><?php echo Route::formatElevation($route->getMaxElevation()); ?></span>
	</li>
	<li class="cmloc-elevation-gain">
		<strong><?php echo Labels::getLocalized('route_elevation_gain'); ?></strong>
		<span><?php echo Route::formatElevation($route->getElevationGain()); ?></span>
	</li>
	<li class="cmloc-elevation-descent">
		<strong><?php echo Labels::getLocalized('route_elevation_descent'); ?></strong>
		<span><?php echo Route::formatElevation($route->getElevationDescent()); ?></span>
	</li>
</ul>