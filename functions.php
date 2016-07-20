<?php

/**
 * Bootstrap nav menu walker
 */
require_once('wp_bootstrap_navwalker.php');

/* Theme setup */
add_action( 'after_setup_theme', 'wpt_setup' );
    if ( ! function_exists( 'wpt_setup' ) ):
        function wpt_setup() {
            register_nav_menu( 'primary', __( 'Primary navigation', 'wptuts' ) );
        } endif;






/**
 * Add custom post types for e-atelier website
 * Components
 * Tutorials
 */

add_action( 'init', 'create_components_post_type' );
function create_components_post_type() {
	 $labels = array(
    'name'               => _x( 'Components', 'post type general name' ),
    'singular_name'      => _x( 'Component', 'post type singular name' ),
    'add_new'            => _x( 'Add New', 'component' ),
    'add_new_item'       => __( 'Add New Component' ),
    'edit_item'          => __( 'Edit Component' ),
    'new_item'           => __( 'New Component' ),
    'all_items'          => __( 'All Components' ),
    'view_item'          => __( 'View Component' ),
    'search_items'       => __( 'Search Components' ),
    'not_found'          => __( 'No components found' ),
    'not_found_in_trash' => __( 'No components found in the Trash' ),
    'parent_item_colon'  => '',
    'menu_name'          => 'Components'
  );
  $args = array(
    'labels'        => $labels,
    'description'   => 'Components and component data',
    'public'        => true,
    //'menu_position' => 5,
    'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
    'has_archive'   => true,
    'taxonomies' 	=> array('component_types'),
  );
  register_post_type( 'e_component', $args );
}

add_action( 'init', 'create_tutorials_post_type' );
function create_tutorials_post_type() {
	$labels = array(
    'name'               => _x( 'Tutorials', 'post type general name' ),
    'singular_name'      => _x( 'Tutorial', 'post type singular name' ),
    'add_new'            => _x( 'Add New', 'tutorial' ),
    'add_new_item'       => __( 'Add New Tutorial' ),
    'edit_item'          => __( 'Edit Tutorial' ),
    'new_item'           => __( 'New Tutorial' ),
    'all_items'          => __( 'All Tutorials' ),
    'view_item'          => __( 'View Tutorial' ),
    'search_items'       => __( 'Search Tutorials' ),
    'not_found'          => __( 'No tutorials found' ),
    'not_found_in_trash' => __( 'No tutorials found in the Trash' ),
    'parent_item_colon'  => '',
    'menu_name'          => 'Tutorials'
  );
  $args = array(
    'labels'        => $labels,
    'description'   => 'Tutorials according to use-case',
    'public'        => true,
    //'menu_position' => 5,
    'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
    'has_archive'   => true,
    'taxonomies' 	=> array('category'),
  );
  register_post_type( 'e_tutorial', $args );
}


add_action( 'init', 'create_activities_post_type' );
function create_activities_post_type() {
     $labels = array(
    'name'               => _x( 'Activities', 'post type general name' ),
    'singular_name'      => _x( 'Activity', 'post type singular name' ),
    'add_new'            => _x( 'Add New', 'Activity' ),
    'add_new_item'       => __( 'Add New Activity' ),
    'edit_item'          => __( 'Edit Activity' ),
    'new_item'           => __( 'New Activity' ),
    'all_items'          => __( 'All Activities' ),
    'view_item'          => __( 'View Activity' ),
    'search_items'       => __( 'Search activities' ),
    'not_found'          => __( 'No activity found' ),
    'not_found_in_trash' => __( 'No activity found in the Trash' ),
    'parent_item_colon'  => '',
    'menu_name'          => 'Activities'
  );
  $args = array(
    'labels'        => $labels,
    'description'   => 'activities and activity data',
    'public'        => true,
    //'menu_position' => 5,
    'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
    'has_archive'   => true,
    'taxonomies'    => array('activity_types'),
  );
  register_post_type( 'e_activity', $args );
}


/**
 * Adds a meta box to the post editing screen
 */
function prfx_custom_meta() {
    add_meta_box( 'prfx_meta', __( 'Components', 'prfx-textdomain' ), 'prfx_meta_callback', 'e_tutorial','side' );
}
add_action( 'add_meta_boxes', 'prfx_custom_meta' );



/**
 * Outputs the content of the meta box
 */
function prfx_meta_callback( $post ) {
    echo 'Add components here';
}

function component_types_init() {
	// create a new taxonomy
	register_taxonomy(
		'component_types',
		'e_component',
		array(
            'hierarchical' => true,
			'label' => __( 'Component Types' ),
            'show_ui' => true,
			'rewrite' => array( 'slug' => 'component_type' ),
			'capabilities' => array(
			    'manage__terms' => 'edit_posts',
			    'edit_terms' => 'manage_categories',
			    'delete_terms' => 'manage_categories',
			    'assign_terms' => 'edit_posts'
			)
		)
	);
}
add_action( 'init', 'component_types_init' );

function activity_types_init() {
    // create a new taxonomy
    register_taxonomy(
        'activity_types',
        'e_activity',
        array(
            'hierarchical' => true,
            'label' => __( 'activity Types' ),
            'show_ui' => true,
            'rewrite' => array( 'slug' => 'activity_type' ),
            'capabilities' => array(
                'manage__terms' => 'edit_posts',
                'edit_terms' => 'manage_categories',
                'delete_terms' => 'manage_categories',
                'assign_terms' => 'edit_posts'
            )
        )
    );
}
add_action( 'init', 'activity_types_init' );


function remove_menus(){

  //remove_menu_page( 'index.php' );                  //Dashboard
  //remove_menu_page( 'edit.php' );                   //Posts
  //remove_menu_page( 'upload.php' );                 //Media
  //remove_menu_page( 'edit.php?post_type=page' );    //Pages
  remove_menu_page( 'edit-comments.php' );          //Comments
  //remove_menu_page( 'themes.php' );                 //Appearance
  //remove_menu_page( 'plugins.php' );                //Plugins
  //remove_menu_page( 'users.php' );                  //Users
  //remove_menu_page( 'tools.php' );                  //Tools
  //remove_menu_page( 'options-general.php' );        //Settings

}
add_action( 'admin_menu', 'remove_menus' );


function the_breadcrumb() {
    global $post;
    echo '<ul id="breadcrumbs">';
    if (!is_home()) {
        echo '<li><a href="';
        echo get_option('home');
        echo '">';
        echo 'Home';
        echo '</a></li><li class="separator"> / </li>';

        $term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
        echo $term->slug;

        if(is_author()) {
            echo"<li>Author Archive"; echo'</li>';
        }

        if (is_category()) {
            $catTitle = single_cat_title( "", false );
            $cat = get_cat_ID( $catTitle );
            echo "<li>  ". get_category_parents( $cat, TRUE, "  " ) ."</li>";
        }
        if (is_category() || is_single()) {
            echo '<li>';
            $category = get_the_category();
            echo "<a href='".get_category_link($category[0]->term_id)."'>";
            echo $category[0]->name;
            echo "</a>";
            if($post) {
                if(get_post_type($post)=='e_component') {
                    $post_terms = wp_get_object_terms($post->ID,'component_types');
                    if($post_terms) {
                        echo "<a href='".get_permalink( get_page_by_title( 'Components' ) )."'>Components</a></li><li class='ceparator'> / </li><li>";
                        echo "<a href='".get_term_link($post_terms[0]->slug,'component_types')."'>";
                        echo $post_terms[0]->name;
                        echo "</a>";
                    }
                }
                if(get_post_type($post)=='e_activity') {
                    $post_terms = wp_get_object_terms($post->ID,'activity_types');
                    if($post_terms) {
                        echo "<a href='".get_permalink( get_page_by_title( 'activities' ) )."'>Activities</a></li><li class='ceparator'> / </li><li>";
                        echo "<a href='".get_term_link($post_terms[0]->slug,'activity_types')."'>";
                        echo $post_terms[0]->name;
                        echo "</a>";
                    }
                }

            }


            if (is_single()) {
                echo '</li><li class="separator"> / </li><li>';
                the_title();
                echo '</li>';
            }
        } elseif (is_page()) {
            if($post->post_parent){
                $anc = get_post_ancestors( $post->ID );
                $title = get_the_title();
                foreach ( $anc as $ancestor ) {
                    $output = '<li><a href="'.get_permalink($ancestor).'" title="'.get_the_title($ancestor).'">'.get_the_title($ancestor).'</a></li> <li class="separator">/</li>';
                }
                echo $output;
                echo '<strong title="'.$title.'"> '.$title.'</strong>';
            } else {
                echo '<li><strong> '.get_the_title().'</strong></li>';
            }
        }
    }
    elseif (is_tag()) {single_tag_title();}
    elseif (is_day()) {echo"<li>Archive for "; the_time('F jS, Y'); echo'</li>';}
    elseif (is_month()) {echo"<li>Archive for "; the_time('F, Y'); echo'</li>';}
    elseif (is_year()) {echo"<li>Archive for "; the_time('Y'); echo'</li>';}
    elseif (is_author()) {echo"<li>Author Archive"; echo'</li>';}
    elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<li>Blog Archives"; echo'</li>';}
    elseif (is_search()) {echo"<li>Search Results"; echo'</li>';}
    echo '</ul>';
}

function display_post_taxonomies() {

        $args = array( 'public' => true, '_builtin' => false );
        $output = 'objects';
        $operator = 'and';
        $taxonomies = get_taxonomies( $args, $output, $operator );
        if( $taxonomies ) {
            $content .= '<div class="taxonomy_container">';
            foreach( $taxonomies as $taxonomy ) {
                $args = array(
                                'orderby'               => 'name',
                                'echo'                  => false,
                                'taxonomy'              => $taxonomy->name,
                                'title_li'              => '<span class="taxonomy_title">' . __( $taxonomy->labels->name, 'your-themes-text-domain' ) . '</span>',
                                'show_option_none'      => __( 'No ' . $taxonomy->labels->name, 'your-themes-text-domain' )
                            );

                $content .= '<ul>' . wp_list_categories( $args ) . '</ul>';
            }
            $content .= '</div>';
        }

    return $content;
}

?>
