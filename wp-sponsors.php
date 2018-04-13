<?php
/*
Plugin Name: WP Sponsors List
Version: 1.0
Description:Plugin for inserting and displaying our sponsors.
Author: Subair T C
Author URI:
Plugin URI:
Text Domain: wp-sponsors-list
Domain Path: /languages
*/

define( 'WP_SPONSORS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'WP_SPONSORS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
register_activation_hook( __FILE__, 'wp_sponsors_activate' );


function add_wp_sponsers_script() {
	wp_register_style( 'our-sponsors', plugins_url( '/css/our-sponsors.css', __FILE__ ) );
	wp_enqueue_style( 'our-sponsors' );
}

add_action( 'wp_enqueue_scripts', 'add_wp_sponsers_script' );

function wp_sponsors_activate() {
	$the_page_title = 'Our Sponsors';
    $the_page_name = 'our-sponsors';

    // the menu entry...
    delete_option("my_plugin_page_title");
    add_option("my_plugin_page_title", $the_page_title, '', 'yes');
    // the slug...
    delete_option("my_plugin_page_name");
    add_option("my_plugin_page_name", $the_page_name, '', 'yes');
    // the id...
    delete_option("my_plugin_page_id");
    add_option("my_plugin_page_id", '0', '', 'yes');

    $the_page = get_page_by_title( $the_page_title );

    if ( ! $the_page ) {
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_content'] = "";
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); 
        $the_page_id = wp_insert_post( $_p );
    }
    else {
        // the plugin may have been previously active and the page may just be trashed...
        $the_page_id = $the_page->ID;
        //make sure the page is not trashed...
        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );
    }
    delete_option( 'my_plugin_page_id' );
    add_option( 'my_plugin_page_id', $the_page_id );
}


//Template fallback
add_action("template_redirect", 'template_redirect_our_sponsors');

function template_redirect_our_sponsors() {
    global $wp;
    $plugindir = dirname( __FILE__ );

   if ($wp->query_vars["pagename"] == 'our-sponsors') {
        $templatefilename = 'our-sponsors.php';
        if (file_exists(TEMPLATEPATH . '/' . $templatefilename)) {
            $return_template = TEMPLATEPATH . '/wp-sponsors/' . $templatefilename;
        } else {
            $return_template = $plugindir . '/templates/' . $templatefilename;
        }
        do_theme_redirect_our_sponsors($return_template);
    }
}

function do_theme_redirect_our_sponsors($url) {
    global $post, $wp_query;
    if (have_posts()) {
        include_once($url);
        die();
    } else {
        $wp_query->is_404 = true;
    }
}


function create_sponsors_posttype() {
	register_post_type( 'sponsors',
		array(
			'labels' => array(
				'name'					=> __( 'Sponsors' ),
				'singular_name' 		=> __( 'sponsor' ),
				'add_new' 				=> 'Add New',
				'add_new_item' 			=> 'Add New sponsor',
				'edit_item'				=> __( 'Edit sponsor' ),
				'new_item'				=> __( 'New sponsor' ),
				'view_item'				=> __( 'View sponsor' ),
				'search_items'			=> __( 'Search sponsor' ),
				'not_found'				=> __( 'No sponsor found.' ),
				'all_items'				=> __( 'All sponsors' ),
				'new_item'				=> __( 'New sponsor' ),
				'not_found'				=> __( 'No sponsor found.' ),
				'not_found_in_trash'	=> 'Nothing found in the Trash',
				'parent_item_colon'		=> ''
			),
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'menu_icon'		=>  WP_SPONSORS_PLUGIN_URL.'/images/Icon-Sponsorship.png',
			'rewrite' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => 81,
			'supports' => array('title','editor','thumbnail')
		)
	);
}
add_action( 'init', 'create_sponsors_posttype' );

/**
 * Add cafe custom fields
 */
function add_sponsors_meta_boxes() {
	add_meta_box("sponsor_contact_meta", "Sponsors Details", "add_sponsor_details_sponsors_meta_box", "sponsors", "normal", "low");
}
function add_sponsor_details_sponsors_meta_box()
{
	global $post;
	$custom = get_post_custom( $post->ID );
 
	?>
	<style>.width99 {width:99%;}</style>
	<p>
		<label>Website:</label><br />
		<input type="text" name="website" value="<?= @$custom["website"][0] ?>" class="width99" />
	</p>
	<p>
		<label>Address:</label><br />
		<textarea rows="5" name="address" class="width99"><?= @$custom["address"][0] ?></textarea>
	</p>

	<p>
		<label>Phone:</label><br />
		<input type="text" name="phone" value="<?= @$custom["phone"][0] ?>" class="width99" />
	</p>

	<?php
}
/**
 * Save custom field data when creating/updating posts
 */
function save_sponsors_custom_fields(){
  global $post;
 
  if ( $post )
  {
    update_post_meta($post->ID, "address", @$_POST["address"]);
    update_post_meta($post->ID, "website", @$_POST["website"]);
    update_post_meta($post->ID, "phone", @$_POST["phone"]);
  }
}
add_action( 'admin_init', 'add_sponsors_meta_boxes' );
add_action( 'save_post', 'save_sponsors_custom_fields' );