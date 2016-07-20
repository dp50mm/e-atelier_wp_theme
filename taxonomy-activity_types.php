
<?php get_header(); ?>
<div class="row">
    			<div class="col-sm-8">

                    <?php

                    $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );

                    ?>
                    <h1><?php echo $term->slug; ?></h1>
                    <?php
                    $activities = get_posts(array(
                        'post_type' => 'e_activity',
                        'orderby' => 'name',
                        'order' => 'ASC',
                        'activity_types' => $term->slug));
                    echo "<ul>";
                    foreach($activities as $activity) {
                        echo "<li><a href='".post_permalink($activity->ID)."' />";
                        echo $activity->post_title;
                        echo "</a></li>";
                    }
                    echo "</ul>";
                    ?>




    			</div>
    			<div class="col-sm-4">
                    <div class='sidebar'>
    				<h2>Active activities & assignments</h2>
    				<p>Activated assignments and activities appear here</p>
                    </div>
    			</div>
    		</div>

<?php get_footer(); ?>
