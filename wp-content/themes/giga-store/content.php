<article class="article-content col-md-4 col-sm-6"> 
	<div <?php post_class( 'row' ); ?>>
		<div class="col-md-12">                             
			<?php if ( has_post_thumbnail() ) : ?>                               
				<a href="<?php the_permalink(); ?>">
					<div class="featured-thumbnail">
						<?php the_post_thumbnail( 'giga-store-home' ); ?>
					</div>
				</a>                                                           
			<?php endif; ?>
		</div>
		<div class="home-header col-md-12"> 
			<header>
				<time class="single-meta-date published">
					<div class="day"><?php the_time( 'd', $post->ID ); ?></div>
					<div class="month"><?php the_time( 'M', $post->ID ); ?></div>
				</time>
				<h2 class="page-header">                                
					<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" rel="bookmark">
						<?php the_title(); ?>
					</a>                            
				</h2> 
			</header>                                                      
			<div class="entry-summary">
				<?php the_excerpt(); ?>
			</div><!-- .entry-summary -->                                                                                                                       
			<div class="clear"></div>                                  
			<p class="text-center">                                      
				<a class="btn btn-default btn-md" href="<?php the_permalink(); ?>">
					<?php esc_html_e( 'Read more', 'giga-store' ); ?> 
				</a>                                  
			</p>                            
		</div>                      
	</div>
	<div class="clear"></div>
</article>
