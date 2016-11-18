<?php

/**
 * Hide_Posts_For_Specific_Roles
 *
 * Main Plugin Class File, this file holds the main plugin class.
 *
 * @author Marcelo Torres
 * @since 1.0.0
 *
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hide_Posts_For_Specific_Roles {

	/**
	 * Class Constructor
	 *
	 * Add the menu, the assets and the method to exclude posts
	 *
	 * @author Marcelo Torres
	 * @since 1.0.0
	 *
	 */
	function __construct() {

		add_action( 'admin_menu', array( $this, 'settings_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );
		add_action( 'pre_get_posts', array( $this, 'exclude_these_posts' ) );

	}


	/**
	 * Backend Assets
	 *
	 * This function takes care of enqueueing all the assets we need in admin.
	 *
	 * @author Marcelo Torres
	 * @since 1.0.0
	 *
	 */
	function admin_assets($hook) {
		$plugin_pages = array( 'tools_page_hpfsr-settings' );

		if ( !in_array( $hook, $plugin_pages ) ) {
			return;
		}


		wp_enqueue_style( 'hpfsr-admin'.$hook, plugin_dir_url( dirname( __FILE__ ) ) . '/assets/css/admin.css' );
		wp_enqueue_script( 'postbox' );
		wp_enqueue_script( 'scripts', plugin_dir_url( dirname( __FILE__ ) ) .  '/assets/js/scripts.js', array('jquery', 'postbox') );

	}


	/**
	 * Add Setting Page
	 *
	 * Adds the settings page which contains the settings for the plugin.
	 *
	 * @author Marcelo Torres
	 * @since 1.0.0
	 *
	 */
	function settings_page() {
		add_submenu_page( 'tools.php', __( 'Hide posts for specific roles', 'hpfsr' ), __( 'Hide posts', 'hpfsr' ), 'edit_user', 'hpfsr-settings', array( $this, 'settings_page_content' ));
		add_action( 'admin_init' , array( $this, 'register_settings' ) );
	}


	/**
	 * Register Settings
	 *
	 * Registers plugin-wide settings
	 *
	 * @author Marcelo Torres
	 * @since 1.0.0
	 *
	 */
	function register_settings() {
		 register_setting( 'hpfsr_settings', 'hpfsr_data_settings' );
	}


	/**
	* Settings Page Content
	*
	* The UI for the settings page.
	*
	* @uses get_usable_post_types()
	* @uses get_user_roles()
	* @uses get_usable_post_status()
	* @uses live_search()
	* @author Marcelo Torres
	* @since 1.0.0
	*
	*/
   function settings_page_content() {

   ?>
   <div class="wrap">
	   <h2><?php _e( 'Hide posts for specific roles', 'hpfsr' ) ?></h2>
	   <?php $usable_post_status = self::get_usable_post_status();?>
	   <ul class="subsubsub">
		  	<li class=""><?php _e( 'Filter by: ', 'hpfsr' );?></a></li>
		  	<li class="all"><a href="tools.php?page=hpfsr-settings&post_status=any"><?php _e( 'All', 'hpfsr' );?></a></li>
		  	<?php foreach ($usable_post_status as $key_status => $label_status) {?>
			<li class="<?php echo $key_status;?>">| <a href="tools.php?page=hpfsr-settings&post_status=<?php echo $key_status;?>"><?php echo $label_status;?></a></li>
		  	<?php } ?>
		</ul>
	   	<form method="post" action="options.php" class="hpfsr-form"> 
		  	<legend><?php _e( 'Choose posts that <em class= "red-text"> NOT BE SHOWN </em> in the list for each role', 'hpfsr' ) ?></legend>
		  	<?php submit_button(); ?>
		   	<?php
		   		settings_errors();
				settings_fields( 'hpfsr_settings' );
				do_settings_sections( 'hpfsr_settings' );
				$data_settings = get_option( 'hpfsr_data_settings' );

				/*echo '<pre>';
				print_r($data_settings);
				echo '</pre>';*/

				$usable_post_types = self::get_usable_post_types();            
				$get_user_roles = self::get_user_roles();            
			?>
			<div id="poststuff">
			  	<div id="postbox-container" class="postbox-container">
					<div class="meta-box-sortables ui-sortable" id="normal-sortables">
				  	<?php foreach( $usable_post_types as $post_type => $data ) {?>
				  	<div class="postbox closed" id="<?php echo $post_type; ?>">
						<button type="button" class="handlediv button-link" aria-expanded="true"><span class="screen-reader-text">Toggle panel: <?php _e( $data->label, 'hpfsr' ) ?></span><span class="toggle-indicator" aria-hidden="true"></span></button>
						<h3 class="hndle">
							<span><?php _e( $data->label, 'hpfsr' ) ?></span>
						</h3>
						<div class="inside">
							<?php self::live_search($post_type);?>
							<ul>
							<?php
							$posts = get_posts( array( 'posts_per_page' => -1, 'orderby' => 'title','order' => 'ASC', 'post_type' => $post_type, 'post_status' => $_GET['post_status'] ) );
							foreach ( $posts as $post ){ ?>
								<li class="hpfsr-post-name">
									<?php if( $post->post_type == 'attachment' ){?>
									<div class="attachment-title">
										<?php echo ( $post->post_type == 'attachment' ) ? the_attachment_link( $post->ID, false ) : '' ;?><br /><span><?php echo $post->post_title;?></span>
									</div>
									<?php }else{?>
									<span><?php echo $post->post_title;?></span>
									<?php }?>
									<ul class="hpfsr-select-roles-option">
										<li><label><input type="checkbox" name="select-all" data-selectallpostid="<?php echo $post->ID?>" value="on"><?php echo __( 'Select all', 'hpfsr' ) ?></label></li>
										<li><label><input type="checkbox" name="select-all-less-administrator" data-selectalllapostid="<?php echo $post->ID?>" id="select-all-less-administrator" value="on"><?php echo __( 'Select all <strong>less administrator</strong>', 'hpfsr' )?></label></li>
									</ul>
									<ul class="hpfsr-list-roles">
										<?php foreach ( $get_user_roles as $role ) {
										$data_checked = ( $data_settings ) ? $data_settings[$post_type][$role['role']] : '';
										$selected = (!empty($data_checked) && in_array($post->ID, $data_checked)) ? 'checked' : '';
										?>
										<li>
											<label><input <?php echo $selected; ?> type="checkbox" data-postid="<?php echo $post->ID?>" data-role="<?php echo $role['role']?>" name="hpfsr_data_settings[<?php echo $post_type?>][<?php echo $role['role']?>][]" value="<?php echo $post->ID?>"><?php echo $role['name']?></label>
										</li>
										<?php } ?>
									</ul>
								</li>
					  		<?php } ?>
					  		</ul>
						</div>
				  	</div>
				  	<?php } ?>
					</div>
			  	</div>
			</div>
		   	<?php submit_button(); ?>
	   </form>
   </div>
   <?php
   }

   /**
   * Exclude These Posts
   * 
   * Exclude and block posts from the list of posts that have been marked for their respective roles
   */
	function exclude_these_posts( $query ) {

		if( !is_admin() ) 
			return $query; global $pagenow;

			$user = wp_get_current_user();
			$data_settings = get_option( 'hpfsr_data_settings' );

			if($data_settings)
			foreach ( $data_settings as $post_type => $roles_with_post_id ) {
				foreach ($roles_with_post_id as $role => $post_ids) {
					if( $role == $user->roles[0] &&  ($pagenow == 'edit.php' || $pagenow == 'upload.php') && ( get_query_var('post_type') &&  get_query_var('post_type') == $post_type ) ) {
						$query->set( 'post__not_in', $post_ids );
					}
					if( $role == $user->roles[0] &&  ($pagenow == 'post.php' || $pagenow == 'upload.php') ) {
						if(in_array($_GET['post'], $post_ids)){
							wp_die(__('You are not allowed to edit this post', 'hpfsr'));
						}
					}
				}
			}
	  
	}

   /**
	* Get Usable Post Types
	*
	* Gets public post types that have proper authors
	*
	* @return array usable post types
	* @author Marcelo Torres
	* @since 1.0.0
	*
	*/
   private static function get_usable_post_types() {

	   	$post_types = get_post_types( array( 'public' => true ), 'objects' );
	   	$post_types = apply_filters( 'hpfsr_usable_post_types', $post_types );

	   	return $post_types;
   }

   /**
	* Get Usable Post Status
	*
	* Gets all posts status less 'trash' and 'auto-draft'
	*
	* @return array usable post status
	* @author Marcelo Torres
	* @since 1.0.0
	*
	*/
   	private static function get_usable_post_status() {

		$get_post_stati = get_post_stati($args = array(), $output = 'objects', $operator = 'and');
		$status_ignore = array( 'trash', 'auto-draft' );
		foreach ($get_post_stati as $key_status => $status) {
			if(!in_array($key_status, $status_ignore)){
				$usable_status[$key_status] = $status->label; 
			}
		}

		return $usable_status;
   	}

   /**
	* Get User roles
	*
	* Gets all user roles
	*
	* @return array user roles (role and name(label) )
	* @author Marcelo Torres
	* @since 1.0.0
	*
	*/
   	private static function get_user_roles() {
		global $wp_roles;

		if ( ! isset( $wp_roles ) )
			$wp_roles = new WP_Roles();

		foreach ( $wp_roles->roles as $role => $details ) {
			if($details['capabilities']['edit_posts'] == 1){
				$sub['role'] = $role;
				$sub['name'] = $details['name'];
				$roles[] = $sub;
			}
		}
		return $roles;
   }

   /**
	* Live Search	
	*
	* @param string $post_type slug
	* @return void
	* @author Marcelo Torres
	* @since 1.0.0
	*
	*/
   	private static function live_search($post_type) {
		?>
	    <fieldset id="live-search">
	    	<label for="filter"><?php _e('Search:', 'hpfsr');?></label>
	        <input type="search" class="hpfsr-filter" id="<?php echo $post_type;?>" value="" />
	        <span id="filter-count-<?php echo $post_type;?>"><?php _e('Number of Posts:', 'hpfsr');?> <strong>0</strong></span>
	    </fieldset>
		<?php
   }

}