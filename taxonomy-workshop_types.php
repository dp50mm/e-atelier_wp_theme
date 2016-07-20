
<?php get_header(); ?>
<div class="row">
    			<div class="col-sm-8">
    				
                    <?php 

                    $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
                    
                    ?>
                    <h1><?php echo $term->slug; ?></h1>
                    <?php
                    $workshops = get_posts(array(
                        'post_type' => 'e_workshop',
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'workshop_types' => $term->slug));
                    echo "<ul>";
                    foreach($workshops as $workshop) {
                        echo "<li><a href='".post_permalink($workshop->ID)."' />";
                        echo $workshop->post_title;
                        echo "</a></li>";
                    }
                    echo "</ul>";
                    ?>
    				


    				
    			</div>
    			<div class="col-sm-4">
                    <div class='sidebar'>
    				<h2>Active workshops & assignments</h2>
    				<p>Activated assignments and workshops appear here</p>
                    </div>
    			</div>
    		</div>

<?php get_footer(); ?>