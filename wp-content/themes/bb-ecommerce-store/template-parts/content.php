<?php
/**
 * The template part for displaying slider
 *
 * @package BB Ecommerce Store
 * @subpackage bb_ecommerce_store
 * @since Ecommerce Store 1.0
 */
?>
  <div id="post-<?php the_ID(); ?>" <?php post_class('inner-service'); ?>>

      <h3 class="ecomercepost-title"><a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php the_title_attribute(); ?>"><?php the_title();?></a></h3>
  	  <div class="metabox">
            <span class="entry-date"><?php echo esc_html( get_the_date() ); ?></span>
            <span class="entry-author"><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' )) ); ?>"><?php the_author(); ?></a></span>
            <span class="entry-comments"> <?php comments_number( __('0 Comment', 'bb-ecommerce-store'), __('0 Comments', 'bb-ecommerce-store'), __('% Comments', 'bb-ecommerce-store') ); ?> </span>
      </div>
      <div class="box-image">
        <?php
          if(has_post_thumbnail()) {
            the_post_thumbnail();
          }
        ?>
      </div>
      <div class="new-text">
        <p><?php the_excerpt();?></p>
        <div class="read-btn">
          <a href="<?php echo esc_url( get_permalink() );?>" class="blogbutton-small hvr-sweep-to-right" title="<?php esc_attr_e( 'Ver mais', 'bb-ecommerce-store' ); ?>"><?php esc_html_e('Ver mais','bb-ecommerce-store'); ?></a>
        </div>
      </div>
      <div class="clearfix"></div>
</div>
