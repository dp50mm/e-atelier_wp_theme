<?php
/*
Template Name: Workshops Page
*/
?>
<?php get_header(); ?>
<div class="row">
	<div class="col-sm-8">
		<h1>Workshops & Assignments</h1>
        <div class='components-container'>
        <?php 
        $taxonomies = array(
            'workshop_type');
        $args = array(
            'orderby'    => 'name',
            'order'      => 'ASC',
            'hide_empty' => false);
         $terms = get_terms("workshop_types");
         if ( !empty( $terms ) && !is_wp_error( $terms ) ){
             foreach ( $terms as $term ) {
                echo "<div class='col-md-4'>";
                echo "<h3>".$term->name."</h3>";
                $components = get_posts(array(
                    'post_type' => 'e_workshop',
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'workshop_types' => $term->slug));
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
	<div class="col-sm-4">
        <div class='sidebar'>
		  <h2>Active workshops & assignments</h2>
          <p>workshop</p>
          <p>assignment dg 242342</p>
        </div>
	</div>
</div>
<?php get_footer(); ?>
