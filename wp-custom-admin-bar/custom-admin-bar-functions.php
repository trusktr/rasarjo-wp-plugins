<?php
/*
 * Logic to determine what to do on each page request:
 * - Always start/get a session
 * - Check if requesting to temp hide
 * - Run through possiable setting situations and run necessary functions
 */
session_start();
if( isset($_REQUEST['cab_hide_page']) ){
	cab_add_disabled_page($_REQUEST['cab_hide_page']);
}
if( isset($_REQUEST['cab_hide_site']) ){
	$_SESSION['cab_hide_site'] = true;
}

if( cab_is_disabled() || isset($_SESSION['cab_hide_site']) ) {
	cab_disable_adminbar();
	add_action('admin_head', 'cab_unshow_options');
}else{
	add_action('after_setup_theme', 'cab_hide_by_users');
	add_action('wp', 'cab_hide_by_page');
	add_action('admin_bar_menu', 'cab_adminbar_menu', 1000);
	cab_define_callback();
}
/*
 * Support for custom Roles
 */
function cab_get_roles(){
	global $wp_roles;
	if ( ! isset( $wp_roles ) ) {
	    $wp_roles = new WP_Roles();
	}
	$roles = $wp_roles->get_names();
	return $roles;
}

/*
 * Add a menu to the Admin Bar
 */
function cab_adminbar_menu(){
	global $wp_admin_bar;
	$wp_admin_bar->add_menu(
		array(	'id' => 'cab-menu',
				'title' => __( 'Admin Bar' ),
				'href' => get_admin_url(null, 'options-general.php?page=custom-adminbar-options-menu')
		)
	);

	$wp_admin_bar->add_menu(
		array(	'parent' => 'cab-menu',
				'id' => 'cab_hide_site',
				'title' => __( 'Hide Site Wide' ),
				'href' => parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH)."?cab_hide_site=true"
		)
	);
		
	//because for now the functionality here only works with pretty permalinks
	global $wp_rewrite;
	if( $wp_rewrite->using_permalinks() ) {
		$current_object = get_queried_object();
		if ( empty($current_object) )
			return;
		$wp_admin_bar->add_menu(
			array(	'parent' => 'cab-menu',
					'id' => 'cab_hide_page',
					'title' => __( 'Hide For This Page' ),
					'href' => parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH)."?cab_hide_page=".$current_object->ID
			)
		);
	}
}
/*
 * Functions to check settings
 */
function cab_is_disabled(){
	if(get_option('custom-adminbar-enabled') != 'on') {
		return true;
	}else{
		return false;
	}
}

function cab_bump_is_disabled(){
	if(get_option('custom-adminbar-bump') != 'on') {
		return true;
	}else{
		return false;
	}
}
/*
 * Callback functions to display styles
 */
function cab_custom_callback(){
	echo '<style>';
	if(!cab_bump_is_disabled()){
		echo "html { margin-top: 28px !important; }
* html body { margin-top: 28px !important; }";
	}
	echo get_option('custom-adminbar-styles');
	echo '</style>';
}
//Thanks to Dario for pointing out that the css wasn't displaying in the back-end
function cab_custom_callback_admin(){
	echo '<style>';
	echo get_option('custom-adminbar-styles');
	echo '</style>';
}

function cab_bump_override(){
?>
<style>
	html { margin-top: 0 !important; }
	* html body { margin-top: 0 !important; }
</style>
<?php
}
/*
 * Hides the admin bar based on the current page/post
 */
function cab_hide_by_page(){
	//Thanks John P. Bloch [http://www.johnpbloch.com/]
	if( did_action( 'wp' ) > 1 )
		return false;
	
	$pages = cab_get_disabled_pages();
	$current_object = get_queried_object();
	if ( empty($current_object) )
		return;
	if ( is_array($pages) ){
		if ( in_array($current_object->ID, $pages) ) {
			cab_unshow_adminbar();
			add_action( 'wp_head', 'cab_bump_override' );
		}
	}
}
/*
 * Is there a session variable called 'cab_disabled_pages'?
 * If there is, return the list of pages on which the admin bar is disabled
 */
function cab_get_disabled_pages(){
	//Thanks John P. Bloch [http://www.johnpbloch.com/]
	$disabled_pages = isset($_SESSION['cab_disabled_pages']) ? $_SESSION['cab_disabled_pages'] : null;
	return $disabled_pages;
}
/*
 * Add a page/post to the list of pages on which the admin bar is disabld
 */
function cab_add_disabled_page($page){
	if(!isset($_SESSION['cab_disabled_pages']))
		$_SESSION['cab_disabled_pages'] = array();
	
	if(!in_array($page, $_SESSION['cab_disabled_pages']))
		array_push($_SESSION['cab_disabled_pages'], $page);
}
/*
 * Hide the admin bar based on the list of user roles
 */
function cab_hide_by_users(){
	$users = cab_get_disabled_users();
	foreach($users as $user){
		if ( current_user_can( $user ) ) {
			cab_unshow_adminbar();
			add_action('admin_head', 'cab_unshow_options');
		}
	}
}
/*
 * Get the list of users for which the admin bar is disabled
 */
function cab_get_disabled_users(){
	
	$roles = cab_get_roles();
	$users = array();
	
	foreach($roles as $role => $name){
		$option = get_option('custom-adminbar-'.$role);
		if($option != 'on')
			array_push($users, $role);
	}	
	return $users;
}
/*
 * Hides the setting in a users profile for the admin bar
 * Note:The profile options page is static.  This makes this the only
 * 		way to hide that options without hacking the core.
 */
function cab_unshow_options(){
	echo "<style>.show-admin-bar{display:none;}</style>";
}
/*
 * Adds theme support for the admin bar
 * Note:This is the only way to change the callback function which displays the content bump
 */
function cab_define_callback(){
	add_theme_support( 'admin-bar', array( 'callback' => 'cab_custom_callback') );
	add_action('admin_head','cab_custom_callback_admin');
}
/*
 * Completely disable the admin bar
 * thanks to the Admin Bar Removal plugin [http://wordpress.org/extend/plugins/wp-admin-bar-removal/]
 */
function cab_disable_adminbar(){
	//thanks to the complete admin bar disabling plugin
	remove_action('init','wp_admin_bar_init');
	remove_filter('init','wp_admin_bar_init');
	foreach(array('wp_footer','wp_admin_bar_render')as$filter)
		add_action($filter,'wp_admin_bar_render',1000);
	foreach(array('wp_footer','wp_admin_bar_render')as$filter)
		add_action($filter,'wp_admin_bar_render',1000);
	remove_action('wp_head','wp_admin_bar_render',1000);
	remove_filter('wp_head','wp_admin_bar_render',1000);
	remove_action('wp_footer','wp_admin_bar_render',1000);
	remove_filter('wp_footer','wp_admin_bar_render',1000);
	remove_action('admin_head','wp_admin_bar_render',1000);
	remove_filter('admin_head','wp_admin_bar_render',1000);
	remove_action('admin_footer','wp_admin_bar_render',1000);
	remove_filter('admin_footer','wp_admin_bar_render',1000);
	remove_action('wp_before_admin_bar_render','wp_admin_bar_me_separator',10);
	remove_action('wp_before_admin_bar_render','wp_admin_bar_my_account_menu',20);
	remove_action('wp_before_admin_bar_render','wp_admin_bar_my_blogs_menu',30);
	remove_action('wp_before_admin_bar_render','wp_admin_bar_blog_separator',40);
	remove_action('wp_before_admin_bar_render','wp_admin_bar_bloginfo_menu',50);
	remove_action('wp_before_admin_bar_render','wp_admin_bar_edit_menu',100);
	remove_action('wp_head','wp_admin_bar_css');
	remove_action('wp_head','wp_admin_bar_dev_css');
	remove_action('wp_head','wp_admin_bar_rtl_css');
	remove_action('wp_head','wp_admin_bar_rtl_dev_css');
	remove_action('admin_head','wp_admin_bar_css');
	remove_action('admin_head','wp_admin_bar_dev_css');
	remove_action('admin_head','wp_admin_bar_rtl_css');
	remove_action('admin_head','wp_admin_bar_rtl_dev_css');
	remove_action('wp_footer','wp_admin_bar_js');
	remove_action('wp_footer','wp_admin_bar_dev_js');
	remove_action('admin_footer','wp_admin_bar_js');
	remove_action('admin_footer','wp_admin_bar_dev_js');
	remove_action('wp_ajax_adminbar_render','wp_admin_bar_ajax_render');
	remove_action('personal_options',' _admin_bar_preferences');
	remove_filter('personal_options',' _admin_bar_preferences');
	remove_action('personal_options',' _get_admin_bar_preferences');
	remove_filter('personal_options',' _get_admin_bar_preferences');
	remove_filter('locale','wp_admin_bar_lang');
	add_filter('show_admin_bar','__return_false');
}
/*
 * Hides the admin bar
 */
function cab_unshow_adminbar(){
	add_filter('show_admin_bar','__return_false');
}
?>