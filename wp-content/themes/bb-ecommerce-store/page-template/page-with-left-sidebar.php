<?php
/**
 * Template Name:Page with Left Sidebar
 */

get_header(); ?>

<?php do_action( 'bb_ecommerce_store_pageleft_header' ); ?>

<div class="container">
    <div class="middle-align">       
		<div class="col-md-3">
			<?php dynamic_sidebar('sidebar-2'); ?>
		</div>		 
		<div class="col-md-9" id="content-tc" >
			<?php while ( have_posts() ) : the_post(); ?>
                
                <h1><?php the_title();?></h1>

                <?php the_content();
                
                wp_link_pages( array(
                    'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'bb-ecommerce-store' ) . '</span>',
                    'after'       => '</div>',
                    'link_before' => '<span>',
                    'link_after'  => '</span>',
                    'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', 'bb-ecommerce-store' ) . ' </span>%',
                    'separator'   => '<span class="screen-reader-text">, </span>',
                ) );

                
                //If comments are open or we have at least one comment, load up the comment template
                	if ( comments_open() || '0' != get_comments_number() )
                    	comments_template();
                ?>
            <?php endwhile; // end of the loop. ?>
        </div>
        <div class="clear"></div>    
    </div>
</div>

<?php do_action( 'bb_ecommerce_store_pageleft_footer' ); ?>


<?php get_footer(); ?>