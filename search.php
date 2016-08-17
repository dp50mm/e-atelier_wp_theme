<?php
/*
Template Name: Search Page
*/
?>
<?php get_header(); ?>
<?php if(have_posts()): ?>
	<h1>SEARCHH</h1>
	<h1 class='entry-title'><?php printf(__('<small>Search Results for:</small> %s', 'blankslate'), get_search_query()); ?></h1>
	<div class='row'>
	<div class='col-md-10'>
	<?php while(have_posts()) : the_post();
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
			endwhile; ?>
	</div>
</div>
<?php else: ?>
	<h2>Sorry, nothing matched your search.</h2>
<?php endif; ?>

<?php get_footer(); ?>
