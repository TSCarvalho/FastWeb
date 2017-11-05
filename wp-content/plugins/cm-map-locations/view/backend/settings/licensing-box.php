<?php

use com\cminds\maplocations\App;

?>
<div class="cm-licensing-box"><?php

if (App::isPro()) {
	echo do_shortcode('[cminds_free_ads id=cmloc]');
} else {
	echo do_shortcode('[cminds_free_registration id="'. App::PREFIX .'"]');
}

?></div>