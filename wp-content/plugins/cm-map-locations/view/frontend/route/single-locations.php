<?php

use com\cminds\maplocations\App;

use com\cminds\maplocations\model\Location;

use com\cminds\maplocations\model\Labels;
use com\cminds\maplocations\model\Route;

use com\cminds\maplocations\helper\RouteView;

/* @var $route Route */

$i = 0;

?><div class="cmloc-route-locations">
	<?php foreach ($route->getLocations() as $location): ?>
		<?php /* @var $location Location */ ?>
		<?php if (Location::TYPE_LOCATION == $location->getLocationType()): ?>
			<?php $i++; ?>
			<div class="cmloc-location-details" data-id="<?php echo $location->getId();
				?>" data-lat="<?php echo $location->getLat(); ?>"  data-long="<?php echo $location->getLong(); ?>">
				<?php if ($address = $location->getAddress()): ?>
					<div class="cmloc-address">
						<strong><?php echo Labels::getLocalized('location_address'); ?>:</strong>
						<span><?php echo esc_html($address); ?></span>
					</div>
				<?php endif; ?>
				<?php if ($postalCode = $location->getPostalCode()): ?>
					<div class="cmloc-postal-code">
						<strong><?php echo Labels::getLocalized('location_postal_code'); ?>:</strong>
						<span><?php echo esc_html($postalCode); ?></span>
					</div>
				<?php endif; ?>
				<?php if (App::isPro()): ?>
					<?php if ($phone = $route->getLocation()->getPhoneNumber()): ?>
						<div class="cmloc-route-phone">
							<strong><?php echo Labels::getLocalized('location_phone_number'); ?>:</strong>
							<span><a href="tel:<?php echo esc_attr($phone); ?>"><?php echo $phone; ?></a></span>
						</div>
					<?php endif; ?>
					<?php if ($website = $route->getLocation()->getWebsite()): ?>
						<div class="cmloc-route-website">
							<strong><?php echo Labels::getLocalized('location_website'); ?>:</strong>
							<span><a href="<?php echo esc_attr($website); ?>"><?php echo $website; ?></a></span>
						</div>
					<?php endif; ?>
					<?php if ($email = $route->getLocation()->getEmail()): ?>
						<div class="cmloc-route-email">
							<strong><?php echo Labels::getLocalized('location_email'); ?>:</strong>
							<span><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo $email; ?></a></span>
						</div>
					<?php endif; ?>
				<?php endif; ?>
				
				<?php do_action('cmloc_single_location_before_images', $location); ?>
				<?php if ($images = $location->getImages()):
					RouteView::displayImages($images, 'location', $location->getId());
				endif; ?>
				
				<div class="cmloc-description"><?php echo $location->getContent(); ?></div>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
</div>