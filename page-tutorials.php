<?php
/*
Template Name: Tutorials Page
*/
?>
<?php get_header(); ?>
<div class="row">
	<div class="col-sm-12">
		<h1>Tutorials</h1>
        <div class='categories-container'>
        <?php

				$taxonomies = array(
            'category');
        $args = array(
            'orderby'    => 'name',
            'order'      => 'ASC',
            'hide_empty' => false);
         $terms = get_terms("category");
         if ( !empty( $terms ) && !is_wp_error( $terms ) ){
            $col_counter = 0;
            echo "<div class='row'>";
             foreach ( $terms as $term ) {
                if($col_counter > 2) {
                    $col_counter = 1;
                    echo "</div><div class='row'>";
                } else {
                    $col_counter += 1;
                }
                echo "<div class='col-md-4'>";
                 $illustration = get_field('tutorial_category_illustration', $term);
                echo "<h4> <img width='50px' height='50px' src=".$illustration['url'].">".$term->name."</h4>";
                $categories = get_posts(array(
                    'post_type' => 'e_tutorial',
                    'posts_per_page' => 100,
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'category_name' => $term->slug));
                echo "<ul>";
                foreach($categories as $comp) {
                    echo "<li><a href='".post_permalink($comp->ID)."' />";
                    echo $comp->post_title;
                    echo "</a></li>";
                }
                echo "</ul>";
                echo "</div>";
             }
             echo "</div>";
         }
        ?>
		</div>

	</div>
</div>
<?php get_footer(); ?>
