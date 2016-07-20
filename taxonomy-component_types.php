
<?php get_header(); ?>
    		<div class="row">
    			<div class="col-sm-12">
                    <?php 

                    $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
                    
                    ?>
                    <h1><?php echo $term->slug; ?></h1>
                    <?php
                    $components = get_posts(array(
                        'post_type' => 'e_component',
                        'posts_per_page' => 100,
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'component_types' => $term->slug));
                    echo "<ul>";
                    foreach($components as $comp) {
                        echo "<li><a href='".post_permalink($comp->ID)."' />";
                        echo $comp->post_title;
                        echo "</a></li>";
                    }
                    echo "</ul>";
                    ?>
    				


    				
    			</div>

    		</div>
<?php get_footer(); ?>
