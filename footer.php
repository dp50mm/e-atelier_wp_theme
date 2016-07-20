		</div>
    	<div class="footer">
            <hr>
            <div class='row sitemap'>
                <div class='col-sm-3'>
                    <h4><a href="<?php echo get_permalink( get_page_by_title('Components')->ID ); ?>">Components</a></h4>
                    <div style="overflow-y:scroll;height:200px;">
                    <?php
                    $taxonomies = array(
                        'component_type');
                    $args = array(
                        'orderby'    => 'name',
                        'order'      => 'ASC',
                        'hide_empty' => false);
                     $terms = get_terms("component_types");
                     if ( !empty( $terms ) && !is_wp_error( $terms ) ){
                         foreach ( $terms as $term ) {
                            echo "<p><a href='".get_term_link($term)."'>".$term->name."</a></p>";
                         }
                     }
                    ?>
                    </div>
                </div>
                <div class='col-sm-3'>
                    <h4>Tutorials</h4>
                    <?php
                    $categories = get_categories(array(
                        'type'=>'e_tutorials'
                    ));

                    foreach ($categories as $cat) {
                        if ($cat->name != 'Uncategorized') {
                            echo "<p><a href='".get_term_link($cat)."'>".$cat->name."</a></p>";
                        }
                    }
                    ?>
                </div>
                <div class='col-sm-3'>
                    <h4>Activities</h4>
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
                            echo "<p><a href='".get_term_link($term)."'>".$term->name."</a></p>";
                         }
                     }
                    ?>
                </div>
                <div class='col-sm-3'>
                </div>
            </div>
            <p class='disclaimer'>
                Copyright ONS 2014
Commisioned by Industrial Design Department Technical University Eindhoven.
In cooperation with study association Industrial Design LUCID.
            </p>
    	</div>


    </div>



    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?php echo get_stylesheet_directory_uri(); ?>/js/bootstrap.min.js"></script>
  </body>
</html>
