<?php
/*
Plugin Name: Developer Portfolio
Plugin URI:  http:/arcticanaudio.com
Description: A portfolio plugin designed for developers
Version:     0.0.1
Author:      Arctican Audio
Author URI:  http:/arcticanaudio.com
License:     GPL2

Developer Portfolio is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Developer Portfolio is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Developer Portfolio. If not, see http://www.gnu.org/licenses/gpl.html
*/


/*
* Create our custom CPT
*/

function create_custom_post_type() {

	// UI labels
	$labels = array(
		'name'                => _x( 'Projects', 'Post Type General Name', 'twentythirteen' ),
		'singular_name'       => _x( 'Project', 'Post Type Singular Name', 'twentythirteen' ),
		'menu_name'           => __( 'Projects', 'twentythirteen' ),
		'parent_item_colon'   => __( 'Parent Movie', 'twentythirteen' ),
		'all_items'           => __( 'All Projects', 'twentythirteen' ),
		'view_item'           => __( 'View Project', 'twentythirteen' ),
		'add_new_item'        => __( 'Add New Project', 'twentythirteen' ),
		'add_new'             => __( 'Add New', 'twentythirteen' ),
		'edit_item'           => __( 'Edit Project', 'twentythirteen' ),
		'update_item'         => __( 'Update Project', 'twentythirteen' ),
		'search_items'        => __( 'Search Projects', 'twentythirteen' ),
		'not_found'           => __( 'Not Found', 'twentythirteen' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'twentythirteen' ),
	);

	$args = array(
		'label'               => __( 'projects', 'twentythirteen' ),
		'description'         => __( 'Develpoer projects', 'twentythirteen' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions'
	),
		//'taxonomies'          => array( 'genres' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
		'rewrite' => array('with_front' => false, 'slug' => 'projects'),
	);

	register_post_type( 'projects', $args );
}
add_action( 'init', 'create_custom_post_type' );



// Register the custom taxonomies: Tools & Languages
function create_taxonomies() {
    register_taxonomy(
        'languages',
        'projects',
        array(
            'hierarchical' => false,
            'label' => 'Programming Languages',
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'languages',
                'with_front' => false
            )
        )
    );

	register_taxonomy(
        'tools',
        'projects',
        array(
            'hierarchical' => false,
            'label' => 'Tools & Technologies',
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'tools-technologies',
                'with_front' => false
            )
        )
    );


	register_taxonomy(
        'platform',
        'projects',
        array(
            'hierarchical' => true,
            'label' => 'Platform',
            'query_var' => true,
            'rewrite' => array(
                'slug' => 'platform',
                'with_front' => false
            )
        )
    );
}
add_action( 'init', 'create_taxonomies');


// Add the CSS style to the site
function register_portfolio_styles()
{
    wp_register_style( 'portfolio-style', plugins_url( 'style.css', __FILE__ ));
    wp_enqueue_style( 'portfolio-style' );
}
add_action( 'wp_enqueue_scripts', 'register_portfolio_styles' );



/** Hook into content filter to show the tags */
function my_the_content_filter($content)
{

	if (get_post_type() == 'projects' && is_single())
  		return render_portfolio_tags(false) . "<hr>" . $content;
	else
		return $content;
}
add_filter( 'the_content', 'my_the_content_filter' );



/** Renders the portfolio tags */
function render_portfolio_tags($printTags = true)
{

	$tagsContent = "<div class='portfolio-tags-container'>";

	$tagsContent .= "<p class='portfolio-tags'>";
	$languages = get_the_terms($post, 'languages');
	if (!empty($languages))
	{
		$tagsContent .= "<span class='portfolio-tags-title'>Languages & Technologies</span><br>";
		foreach ($languages as $language)
			$tagsContent .= "<span class='portfolio-tag portfolio-tag-language'>$language->name</span> ";
	}
//	$tagsContent .= "</p>";


//	$tagsContent .= "<p class='portfolio-tags'>";
	$tools = get_the_terms($post, 'tools');
	if (!empty($tools))
	{
		$tagsContent .= "<br>";
//		$tagsContent .= "<span class='portfolio-tags-title'>Technologies</span><br>";
		foreach ($tools as $tool)
			$tagsContent .= "<span class='portfolio-tag portfolio-tag-tools'>$tool->name</span> ";
	}
	$tagsContent .= "</p></div>";

	if ($printTags == true)
		echo $tagsContent;
	else
		return $tagsContent;



}


/* Display the post meta box. */
function ATC_developer_portfolio_show_meta_box( $object, $box )
{
	 wp_nonce_field( basename( __FILE__ ), 'ATC_developer_portfolio_nonce' ); ?>

  	<p>
    	<label for="ATC_DP_project_URL">Project URL</label>

		<br />

		<input class="widefat" type="text" name="ATC_DP_project_URL" id="ATC_DP_project_URL" value="<?php echo esc_attr( get_post_meta( $object->ID, 'ATC_DP_project_URL', true ) ); ?>" size="30" />
  </p>
<?php }

/* Create one or more meta boxes to be displayed on the post editor screen. */
function ATC_add_developer_portfolio_meta_boxes()
{

  add_meta_box(
    'ATC_developer_portfolio_meta',
    'Project URL',
    'ATC_developer_portfolio_show_meta_box',
    'projects',
    'side',
    'default'
  );
}



/* Save the meta box's post metadata. */
function ATC_DP_save_metabox( $post_id, $post )
{

  /* Verify the nonce before proceeding. */
  if ( !isset( $_POST['ATC_developer_portfolio_nonce'] ) || !wp_verify_nonce( $_POST['ATC_developer_portfolio_nonce'], basename( __FILE__ ) ) )
    return $post_id;

  /* Get the post type object. */
  $post_type = get_post_type_object( $post->post_type );

  /* Check if the current user has permission to edit the post. */
  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;

  /* Get the posted data and sanitize it as a preoper URL. */
  $new_meta_value = ( isset( $_POST['ATC_DP_project_URL'] ) ? esc_url( $_POST['ATC_DP_project_URL'] ) : '' );

  /* Get the meta key. */
  $meta_key = 'ATC_DP_project_URL';

  /* Get the meta value of the custom field key. */
  $meta_value = get_post_meta( $post_id, $meta_key, true );

  /* If a new meta value was added and there was no previous value, add it. */
  if ( $new_meta_value && '' == $meta_value )
    add_post_meta( $post_id, $meta_key, $new_meta_value, true );

  /* If the new meta value does not match the old value, update it. */
  elseif ( $new_meta_value && $new_meta_value != $meta_value )
    update_post_meta( $post_id, $meta_key, $new_meta_value );

  /* If there is no new meta value but an old value exists, delete it. */
  elseif ( '' == $new_meta_value && $meta_value )
    delete_post_meta( $post_id, $meta_key, $meta_value );
}


/* Meta box setup function. */
function ATC_setup_developer_portfolio_meta_boxes()
{

  /* Add meta boxes on the 'add_meta_boxes' hook. */
  add_action( 'add_meta_boxes', 'ATC_add_developer_portfolio_meta_boxes' );

  /* Save post meta on the 'save_post' hook. */
 	add_action( 'save_post', 'ATC_DP_save_metabox', 10, 2 );
}

add_action( 'load-post.php', 'ATC_setup_developer_portfolio_meta_boxes' );
add_action( 'load-post-new.php', 'ATC_setup_developer_portfolio_meta_boxes' );



?>
