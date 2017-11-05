<?php

use com\cminds\maplocations\model\Labels;


?><div class="cmloc-location-index-filter cmloc-filter">

	<form action="<?php echo esc_attr($searchFormUrl); ?>" class="cmloc-location-filter-form">
	
		<?php do_action('cmloc_map_filter_before'); ?>
	
		<label class="cmloc-field-search">
			<input type="text" name="s" value="<?php echo esc_attr(isset($_GET['s']) ? $_GET['s'] : '');
				?>" placeholder="<?php echo Labels::getLocalized('search_placeholder'); ?>" class="cmloc-input-search" />
		</label>
		
		<button type="submit" title="<?php echo esc_attr(Labels::getLocalized('search_btn')); ?>"><span class="dashicons dashicons-search"></span></button>
		
		<?php do_action('cmloc_map_filter_after'); ?>
	
	</form>
	
</div>