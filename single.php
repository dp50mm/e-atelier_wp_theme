<?php get_header(); ?>

<?php
//$url = "http://e.lucid.cc/product/get_product_json";
//$response = file_get_contents($url);
//$json = json_decode($response, true);

?>

<div class="row">
	<div class="col-sm-8">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

		
		<?php if (is_singular('e_component')):?>
			<div style='overflow:auto;width:100%;'>
				<?php $illustration = get_field('component_illustration'); ?>
				
				<h1 class='component-header'><?php the_title(); ?></h1>
				<?php if (!empty($illustration)): ?>
					<img width='75px' height='75px' class='header-illustration' src="<?php echo $illustration['url']; ?>" alt="<?php echo $illustration['alt']; ?>" />
				<?php endif; ?>
			</div>
		<?php elseif(is_singular('e_tutorial')):?>
			<h1 class='tutorial-header'><?php the_title(); ?></h1>
			<p>by:<?php the_author_posts_link(); ?> written: <?php the_time('F jS, Y'); ?></p>
		<?php elseif(is_singular('e_workshop')):?>
			<h1 class='tutorial-header'><?php the_title(); ?></h1>
			<p>by:<?php the_author_posts_link(); ?> written: <?php the_time('F jS, Y'); ?></p>
		<?php endif;?>
		
		<div class='post-content'>
		  <?php the_content(); ?>
		</div>

		<?php endwhile; else : ?>
			<p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
		<?php endif; ?>
	</div>
	<div class="col-sm-4">
		<div class="sidebar">
			<?php
			if(have_rows('components')) {  ?>
				<h3>Components</h3>
				<div class="components-table-header">
					<p class="name-header">
						name
					</p>
					<p class="stock">
						stock
					</p>
					<p class="count">
						count
					</p>

				</div>
				<?php
				while(have_rows('components')) : the_row();

					$component = get_sub_field('component');
					$illus = get_field('component_illustration', $component->ID);
					
					?>
					<div class='component-container'>
						<a href='<?php echo post_permalink($component->ID); ?>'/>
						<div class='component-metabox'>
							<img width='40px' height='40px' class='header-illustration' src="<?php echo $illus['url']; ?>" alt="<?php echo $illus['alt']; ?>" />
							<p class='name'>
							<?php echo $component->post_title; ?>
							<?php the_sub_field('value'); ?>
							</p>
							<div class="number-used">
								<p class='multiplier'>
									x
								</p>
								<p class="number">
									<?php the_sub_field('number_used'); ?>
								</p>
							</div>
							
						</div>
						</a>
						<div class="stock-amount">
							<p class="number">
								<?php $rs_number = get_sub_field('rs_number');
								if($rs_number) {
									//echo $json[$rs_number]['stock_amount'];
								} else {
									//echo '<a href="https://www.google.nl/?gfe_rd=cr&ei=v-JQVOXSCNGLOv6dgNgP&gws_rd=ssl#q='.$component->post_title.'"<i class="fa fa-arrow-up"></i></a>';
								}
								?>
							</p>
						</div>
					</div>

				<?php endwhile; 
			}
			?>

			<?php if(have_rows('links')): ?>
				<hr>
				<h3>read more</h3>
				<div class="meta-links">
					<?php while(have_rows('links')) : the_row(); ?>
						<p><a href="<?php the_sub_field('url'); ?>"><?php the_sub_field('link_name'); ?></a></p>
					<?php endwhile; ?>
				</div>

			<?php endif; ?>
		</div>
		
	</div>
</div>

<?php get_footer(); ?>