<?php
/* metaboxes.php
Deals with the metaboxes on the admin screen for Project URL etc.
-----------------------------------------------------------------------

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
