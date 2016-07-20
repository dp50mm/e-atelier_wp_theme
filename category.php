<?php get_header(); ?>
<div class="row">
	<div class="col-sm-8">
		<section id="content" role="main">
		<header class="header">
		<h1 class="entry-title"><small><?php _e( 'Tutorials about ', 'blankslate' ); ?></small><strong><?php single_cat_title(); ?></strong></h1>
		
		</header>
		<div class='categories-container'>
			<?php
			$args = array( 'post_type' => 'e_tutorial', 'cat'=> $_GET['cat'], 'term' => 'php', 'posts_per_page' => 10 );
			$loop = new WP_Query( $args );
			while ( $loop->have_posts() ) : $loop->the_post();
				echo "<h3>";
				echo "<a href=\"";
				the_permalink();
				echo "\">";
				the_title();
				echo "</a>";
				echo "</h3>";
				echo '<div class="entry-content">';
				the_excerpt();
				echo '</div>';
			endwhile;
			?>
		</div>
		</section>
	</div>
	<div class="col-sm-4">
		<div class='sidebar'>
			<h3>About <strong><?php single_cat_title(); ?></strong></h3>
			<?php echo category_description( $_GET['cat']); ?> 
		</div>
	</div>
</div>
<?php get_footer(); ?>