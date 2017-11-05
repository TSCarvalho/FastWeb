<?php 
/*
 * The template for displaying the vendor Vendido por on the shop loop
 *
 * Override this template by copying it to yourtheme/wc-vendors/front/
 *
 * @package    WCVendors
 * @version    1.9.6
 * 		
 * Template Variables available 
 *  
 * $vendor_id  : current vendor id for customization 
 * $sold_by_label : Vendido por label 
 * $sold_by : Vendido por 
 *
 *
 */
?>

<small class="wcvendors_sold_by_in_loop"><?php echo apply_filters('wcvendors_sold_by_in_loop', $sold_by_label ); ?> <?php echo $sold_by; ?></small><br />