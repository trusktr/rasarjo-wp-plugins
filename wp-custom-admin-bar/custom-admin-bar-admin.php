<?php
add_action('admin_init', 'regester_custom_adminbar_settings');
function regester_custom_adminbar_settings(){
	$default_css = "#wpadminbar{}
#wpadminbar a,
#wpadminbar a:hover,
#wpadminbar a img,
#wpadminbar a img:hover{}
#wpadminbar ul,
#wpadminbar ul li{}
#wpadminbar .quicklinks ul{}
#wpadminbar .quicklinks ul li{}
#wpadminbar .quicklinks>ul>li>a{}
#wpadminbar .quicklinks>ul>li:last-child>a{}
#wpadminbar .quicklinks>ul>li:hover>a{}
#wpadminbar .quicklinks a,
#wpadminbar .shortlink-input{}
#wpadminbar .quicklinks a>span{}
#wpadminbar .quicklinks .menupop ul,
#wpadminbar .shortlink-input{}
#wpadminbar .selected .shortlink-input{}
#wpadminbar .quicklinks .menupop ul li{}
#wpadminbar .quicklinks .menupop ul li a,
#wpadminbar .shortlink-input{}
#wpadminbar .shortlink-input{}
#wpadminbar .quicklinks .menupop ul li:hover>a{}
#wpadminbar .quicklinks li:hover>ul,
#wpadminbar .quicklinks li.hover>ul{}
#wpadminbar .quicklinks .menupop li:hover>ul,
#wpadminbar .quicklinks .menupop li.hover>ul{}
#wpadminbar .quicklinks li:hover,
#wpadminbar .quicklinks .selected{}
#wpadminbar .quicklinks .menupop li:hover{}
#wpadminbar .quicklinks .menupop a>span{}
#wpadminbar .quicklinks .menupop ul li a>span{}
#wpadminbar .quicklinks a span#ab-awaiting-mod,
#wpadminbar .quicklinks a span#ab-updates{}
#wpadminbar .quicklinks a:hover span#ab-awaiting-mod,
#wpadminbar .quicklinks a:hover span#ab-updates{}
#wpadminbar .quicklinks li#wp-admin-bar-my-account>a{}
#wpadminbar .quicklinks li#wp-admin-bar-my-account-with-avatar>a{}
#wpadminbar .quicklinks li#wp-admin-bar-my-account-with-avatar>a img{}
#wpadminbar .quicklinks li#wp-admin-bar-my-account-with-avatar ul{}
#wpadminbar .quicklinks .menupop li a img.blavatar{}
#wpadminbar #adminbarsearch{}
#wpadminbar #adminbarsearch .adminbar-input{}
#wpadminbar #adminbarsearch .adminbar-button{}
#wpadminbar #adminbarsearch .adminbar-button:active{}
#wpadminbar #adminbarsearch .adminbar-button:hover{}";
	
	add_settings_section('main_section', 'Main Settings', 'main_section_disp_fnc', __FILE__);

	register_setting( 'custom-adminbar', 'custom-adminbar-enabled' );
	add_settings_field('custom-adminbar-enabled', 'Enable Admin Bar:', 'checkbox_fnc', __FILE__, 'main_section', array( 'name' => 'custom-adminbar-enabled', 'default' => true ) );
	
	register_setting( 'custom-adminbar', 'custom-adminbar-bump' );
	add_settings_field('custom-adminbar-bump', 'Enable Content Bump:', 'checkbox_fnc', __FILE__, 'main_section', array( 'name' => 'custom-adminbar-bump', 'default' => true ) );
	
	cab_register_role_options();
	
	add_settings_section('style_section', 'Custom CSS Styles', 'styles_disp_fnc', __FILE__);
	
	register_setting( 'custom-adminbar', 'custom-adminbar-styles' );
	add_settings_field('custom-adminbar-styles', 'CSS Code:', 'textarea_fnc', __FILE__, 'style_section', array( 'name' => 'custom-adminbar-styles', 'default' => $default_css ) );
}
function main_section_disp_fnc(){
	echo "Note: The 'Content Bump' is the 28px margin added to the top of the page.<br />
	This margin often causes problems with some layouts (<a href='http://wesleytodd.com/2011/02/wp31-admin-bar-removal-styling' target='_blank'>Read This Article</a>).";
}
function user_settings_disp_fnc(){
	echo "<p>Select who can see the admin bar by user level.</p>";
}
function styles_disp_fnc(){
	echo "<p>Define Custom CSS for your admin bar.</p>";
}
function checkbox_fnc($args){
	$val = get_option($args['name'], $args['default']);
	$checked = '';
	if( $val ) { $checked = ' checked="checked" '; }
	echo "<input ".$checked." name='".$args['name']."' type='checkbox' />";
}
function textarea_fnc($args){
	$val = get_option($args['name'], $args['default']);
	echo "<textarea name='".$args['name']."' cols='67' rows='15'>$val</textarea>";
}
/*
 * Register a setting for each avaliable role
 */
function cab_register_role_options(){
	add_settings_section('user_section', 'User Settings', 'user_settings_disp_fnc', __FILE__);

	$roles = cab_get_roles();
	foreach($roles as $role=>$name){
		$field_name = 'custom-adminbar-'.$role;
		$field_label = $name.':';
		register_setting( 'custom-adminbar', $field_name );
		add_settings_field($field_name, $field_label, 'checkbox_fnc', __FILE__, 'user_section', array( 'name' => $field_name, 'default' => true ) );
	}

}
add_action('admin_menu', 'custom_adminbar_menu');
function custom_adminbar_menu() {
  add_options_page('Admin Bar Options', 'Custom Admin Bar', 'manage_options', 'custom-adminbar-options-menu', 'custom_adminbar_options');
}
function custom_adminbar_options() {

  if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }
?>
<div class="wrap">
	<h2>Custom Admin Bar Options</h2>
	<form action="options.php" method="post">
		<?php settings_fields('custom-adminbar');
		do_settings_sections(__FILE__); ?>
		<p class="submit">
			<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
		</p>
	</form>
</div>

<?php
}
//END options page stuff
?>