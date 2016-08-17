<?php
/*
Template Name: Activities Page
*/
?>
<?php get_header(); ?>
<div class="row">
	<div class="col-sm-12">
		<h1>Activities</h1>
        <div class='components-container'>
        <?php
        $taxonomies = array(
            'activity_type');
        $args = array(
            'orderby'    => 'name',
            'order'      => 'ASC',
            'hide_empty' => false);
         $terms = get_terms("activity_types");
         if ( !empty( $terms ) && !is_wp_error( $terms ) ){
             foreach ( $terms as $term ) {
                echo "<div class='col-md-4'>";
                echo "<h3>".$term->name."</h3>";
                $components = get_posts(array(
                    'post_type' => 'e_activity',
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'activity_types' => $term->slug));
                echo "<ul>";
                foreach($components as $comp) {
                    echo "<li><a href='".post_permalink($comp->ID)."' />";
                    echo $comp->post_title;
                    echo "</a></li>";
                }
                echo "</ul>";
                echo "</div>";
             }
         }
        ?>
		</div>

	</div>

</div>
<?php get_footer(); ?>
