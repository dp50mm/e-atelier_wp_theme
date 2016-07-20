<?php get_header(); ?>
<div class="row">
	<div class="col-sm-8" style='border-right:1px solid grey;'>
		<header class="header">
		<?php the_post(); ?>
		<h1 class="entry-title author"><small><?php _e( 'Author Archives', 'blankslate' ); ?>: </small><?php the_author_link(); ?></h1>
		<?php if ( '' != get_the_author_meta( 'user_description' ) ) echo apply_filters( 'archive_meta', '<div class="archive-meta">' . get_the_author_meta( 'user_description' ) . '</div>' ); ?>
		<?php rewind_posts(); ?>
		</header>
		<div class='row'>
			<div class='col-md-6'>
				<h4>tutorials:</h4>
				<?php
				$args = array( 'post_type' => 'e_tutorial', 'cat'=> $_GET['cat'], 'term' => 'php', 'posts_per_page' => 10, 'author' => get_the_author_meta('ID') );
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
			<div class='col-md-6'>
				<h4>Components:</h4><?php

				$args = array( 'post_type' => 'e_component', 'cat'=> $_GET['cat'], 'term' => 'php', 'posts_per_page' => 10, 'author' => 'author='.get_the_author_meta('ID') );
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
		</div>
	</div>
	<div class="col-sm-4">
		<h4>News Articles</h4>
		<?php while ( have_posts() ) : the_post(); ?>
		<a href="<?php the_permalink();?>">
			<h2><?php the_title();?></h2>
			<p><?php the_excerpt();?></p>
		</a>
		<?php endwhile; ?>
	</div>
</div>


<?php get_footer(); ?>