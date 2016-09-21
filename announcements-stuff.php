<?php
/*
Plugin Name: Announcements Settings
Description: Sets a number of different customizations to help the Announcements Slide functionality look its best.
Version: 0.1
*/

/* Force the Use Title toggle on */
add_filter( 'reveal-js-slide-defaults', function( $retval ) {
    $retval['use-title'] = true;
    return $retval;
} );

/* Disable WordPress Admin Bar from the front end so you can't see it on slides */
add_filter('show_admin_bar', '__return_false');

/* Add support to allow slides to be unpublished https://github.com/humanmade/Unpublish */
$types = array( 'post', 'page', 'slides' );
foreach( $types as $type ) {
    add_post_type_support( $type, 'unpublish' );
}

/* Remove Dashboard widgets, particularly quickpress, so users add slides and not posts */
function remove_dashboard_meta() {
        remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
        remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
        remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');
}
add_action( 'admin_init', 'remove_dashboard_meta' );

/* Add the Announcements taxonomy to the Presentation Taxonomy Type, on the Slides CPT. */

function set_default_object_terms( $post_id, $post ) {
    if ( $post->post_type === 'slides' ) {
        $defaults = array(
                'presentation' => array( 'announcements' )
                );
            $taxonomies = get_object_taxonomies( $post->post_type );
            foreach ( (array) $taxonomies as $taxonomy ) {
                $terms = wp_get_post_terms( $post_id, $taxonomy );
                if ( empty( $terms ) && array_key_exists( $taxonomy, $defaults ) ) {
                    wp_set_object_terms( $post_id, $defaults[$taxonomy], $taxonomy );
                }
            }
      }
}
    add_action( 'save_post', 'set_default_object_terms', 0, 2 );

/* Remove all Dashboard menus for Authors to simplify things further */
function remove_menus(){
  if( !current_user_can('manage_options')){
  remove_menu_page( 'edit.php' );								//Posts
  remove_menu_page( 'upload.php' );							//Media
  remove_menu_page( 'edit-comments.php' );			//Comments
  remove_menu_page( 'profile.php' );						//Comments
  remove_menu_page( 'users.php' );							//Users
  remove_menu_page( 'tools.php' );							//Tools
  remove_menu_page( 'options-general.php' );		//Settings
  remove_menu_page( 'admin.php?page=threewp_broadcast' );    	//Broadcast Menu
} 
}
add_action( 'admin_menu', 'remove_menus', 999);

/* Remove adminbar nodes */
function remove_wp_nodes(){
  if( !current_user_can('manage_options')){
  global $wp_admin_bar;   
  $wp_admin_bar->remove_node( 'new-post' );
  $wp_admin_bar->remove_node( 'new-link' );
  $wp_admin_bar->remove_node( 'new-media' );
  $wp_admin_bar->remove_node('comments');
  $wp_admin_bar->remove_node('new-content');
}
}
add_action( 'admin_bar_menu', 'remove_wp_nodes', 999 );

/* Hide Metaboxes on Slide Edit Page */
function remove_my_post_metaboxes() {
if( !current_user_can('manage_options')){
remove_meta_box( 'presentationdiv','slides','side' );   // Presentation Taxonomy Metabox
remove_meta_box( 'pageparentdiv','slides','side' );     // Parent Attribute Metabox
remove_meta_box( 'slide-settings','slides','normal' );  // Reveal Slide Metabox
remove_meta_box( 'postimagediv','slides','side' );      // Featured Image Metabox
}
}
add_action('do_meta_boxes','remove_my_post_metaboxes');

/* Display custom admin notice */
function hwdsbpres_custom_admin_notice() { ?>
	
	<div class="notice notice-success">
		<p><?php _e('If you are creating a new slide, click the Pres. Slides/Add New menu on the left', 'hwdsbpres'); ?></p>
	</div>
	
<?php }
add_action('admin_notices', 'hwdsbpres_custom_admin_notice');

/* Add Jetpack ShortURL support to the Slides post type */
add_action('init', 'hwdsbshorturl_custom_init');
function hwdsbshorturl_custom_init() {
    add_post_type_support( 'slides', 'shortlinks' );
}
