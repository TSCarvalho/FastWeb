<?php

use com\cminds\maplocations\App;

?>
<?php if (App::isPro()): ?>
	<li><kbd>[cmloc-snippet id=location_id featured="one of: image map"]</kbd> - displays location's snippet.</li>
	<li><kbd>[cmloc-location-map id=location_id]</kbd> - displays location's map.</li>
	<li><kbd>[cmloc-locations-map list="one of: none left right bottom compact" limit=5 page=1 category="id or slug"]</kbd>
		- displays the locations map, optionally from chosen category and with the pagination.</li>
	<li><kbd>[cmmrm-cmloc-common-map path=0 categoryfilter=0]</kbd> - displays common map with locations and routes from the CM Map Routes Manager plugin.</li>
	<li><kbd>[cmloc-business category="id or slug" categoryfilter=0]</kbd> - displays map with the CM Business Directory records.</li>
<?php endif; ?>