<?php

use com\cminds\maplocations\model\Labels;

?>
<div class="cmloc-msg cmloc-msg-<?php echo $class; ?>">
	<div class="cmloc-msg-inner">
		<span><?php echo esc_html(Labels::getLocalized($msg)); ?></span>
		<div class="cmloc-msg-extra"><?php echo $extra; ?></div>
	</div>
</div>