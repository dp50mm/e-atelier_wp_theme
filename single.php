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
		<?php elseif(is_singular('e_activity')):?>
			<h1 class='tutorial-header'><?php the_title(); ?></h1>
			<p>by:<?php the_author_posts_link(); ?> written: <?php the_time('F jS, Y'); ?></p>
		<?php endif;?>

		<div class='post-content'>
		  <?php the_content(); ?>
			<?php
			$defaults = array(
					'before'           => '<p>' . __( 'Pages:' ),
					'after'            => '</p>',
					'link_before'      => '',
					'link_after'       => '',
					'next_or_number'   => 'number',
					'separator'        => ' ',
					'nextpagelink'     => __( 'Next page' ),
					'previouspagelink' => __( 'Previous page' ),
					'pagelink'         => '%',
					'echo'             => 1
				);

			        wp_link_pages( $defaults );
			?>
		</div>

		<?php endwhile; else : ?>
			<p><?php _e( 'Sorry, no posts matched your criteria.' ); ?></p>
		<?php endif; ?>
	</div>
	<div class="col-sm-4">
		<div class="sidebar">
			<?php
			if(have_rows('e-lucid_components')) {  ?>
				<h3>E-Lucid components</h3>
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
				require_once 'lassieAPI/LassieApi.php';

				$lassie_model_api = new LassieApi(array(
					'host' => 'http://lassie.lucid.cc',
					'api_key' => '121032d28981c2f663bd076afa97ca64',
					'api_secret' => '5bba0f6e8de2d9f110bbfd463a1d055f',
				));

				$group_arr = $lassie_model_api->get('model', array(
					'name' => 'transaction_model',
					'method' => 'get_elucid_products',
					'format' => 'json',
				));
				while(have_rows('e-lucid_components')) : the_row();

					$component = get_sub_field('component');
					$rs_number = get_sub_field('rs_number');
					$product = $group_arr->$rs_number;
					$illus = get_field('component_illustration', $component->ID);
					$lassie_illustration_url = "http://lassie.lucid.cc/uploads/".$product->{"image_path"};
					$amount_used = get_sub_field("amount_used");


					?>
					<div class='component-container'>
						<?php if($component): ?> <a href='<?php echo post_permalink($component->ID); ?>'/> <?php endif; ?>
						<div class='component-metabox'>
							<img width='40px' height='40px' class='header-illustration' src="<?php echo $lassie_illustration_url; ?>" alt="<?php echo $illus['alt']; ?>" />
							<p class='name'>
							<?php echo $product->{"name"}; ?>
							</p>
							<div class="number-used">
								<p class='multiplier'>
									x
								</p>
								<p class="number <?php if ($amount_used < $product->{"quantity_properties"}->{"total"}) { echo "enough-in-stock-highlight"; } ?>">
									<?php the_sub_field('amount_used'); ?>
								</p>
							</div>

						</div>
						</a>
						<div class="stock-amount">
							<p class="number">
								<?php echo $product->{"quantity_properties"}->{"total"}; ?>
							</p>
						</div>
					</div>

				<?php endwhile;
			}
			?>

			<?php if (have_rows('other_components')): ?>
				<h3>Other components</h3>
				<div class="component-container">
					<?php while(have_rows('other_components')) : the_row();
						$component = get_sub_field('component');
						?>
						<?php if ($component): ?>
							<a href='<?php echo post_permalink($component->ID); ?>'/>
						<?php elseif(get_sub_field("url")): ?>
							<a href='<?php the_sub_field("url"); ?>'/>
						<?php endif; ?>
						<div class='component-metabox'>
							<p class='name'>
							<?php the_sub_field("name"); ?>
							</p>
							<div class="number-used">
								<p class='multiplier'>
									x
								</p>
								<p class="number no-elucid-stock-product">
									<?php the_sub_field('number_used'); ?>
								</p>
							</div>

						</div>
						</a>
					<?php endwhile; ?>
				</div>
			<?php endif; ?>

			<?php if(have_rows('links')): ?>
				<hr>
				<h3>read more</h3>
				<div class="meta-links">
					<?php while(have_rows('links')) : the_row(); ?>
						<p><a href="<?php the_sub_field('url'); ?>"><?php the_sub_field('link_name'); ?></a></p>
					<?php endwhile; ?>
				</div>

			<?php endif; ?>

			<?php if(get_field("in_this_course")): ?>
				<h3>In this course</h3>
				<div class="in-this-course">
					<?php the_field("in_this_course"); ?>
				</div>
			<?php endif; ?>
			<?php if(get_field("read_more")): ?>
				<h3>Read more</h3>
				<div class="read-more">
					<?php the_field("read_more"); ?>
				</div>
			<?php endif; ?>
		</div>

	</div>
</div>

<?php get_footer(); ?>
