=== Plugin Name ===

Contributors:      Wesley Todd
Plugin Name:       WP Custom Admin Bar
Plugin URI:        http://wesleytodd.com/?custom-plugin=admin-bar-control
Tags:              Admin Bar, Customize Admin Bar, User Controls, removal, hide, disable, User Role, Roles
Author URI:        http://wesleytodd.com
Author:            Wesley Todd
Requires at least: 3.1
Tested up to:      3.1.1
Stable tag:        trunk
Version:           1.3.2
Donate link:	   http://wesleytodd.com/contact

== Description ==

A really simple and easy to use plugin to help gain control of the new Admin Bar.
This gives you options to change who sees the Admin Bar based on their user role, 
change or override the default styling or remove the Admin Bar altogether.
It adds a menu to the Admin Bar which gives you the ability to disable it on a 
single page or sitewide for a single browser session.

WP Custom Admin Bar now supports Custom Roles!

== Installation ==

1. Extract 'wp-custom-admin-bar' and transfer it to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Visit the setting page to customize your admin bar (be sure to save your settings!)

== Frequently Asked Questions ==

= Can I disable the Admin Bar for just one browsing session or on just one spicific page? =

Yes you can! As of version 1.0 there is a menu item in the admin bar to disable the 
bar on a page by page basis or for the entire site.  This setting is for a single browser 
session, so if you close your browser the settings reset.

= When I disable the Admin Bar for a given User Role, does it hide the options in their profile page? =

Yes.  Thanks to Rick for the suggestion.

= Does this plugin support custom roles? =

Yes!  This feature was added in version 1.3.0

For any other questions:
[Contact Me](http://wesleytodd.com/contact)

== Upgrade Notice ==


== Screenshots ==

1. The Administration Options Page
2. The Admin Bar Menu
3. Styled Admin Bar used on [heyNordyne.com](http://heynordyne.com)
4. Styled Admin Bar used on the [Hot Tub Things Blog](http://blog.hottubthings.com)

== Changelog ==

= 1.3.2 =
- The styles applied using the plugin now apply in the admin area

= 1.3.1 =
- Bug fix: Sometimes $wp_roles is not created yet, so check for that and create it if it isn't

= 1.3.0 =
- Added support for custom roles
- Added a description of the "Content Bump" in the admin menu

= 1.2.1 =
- Bug fix: margin was still applied if Admin Bar was hidden on a single page

= 1.2 =
- Hide Admin Bar options on the profile page for everyone if the Admin Bar is completely disabled
- Bug fixes
- Changed 'cab_hide_by_page' to fire on the 'wp' action 

= 1.1 =
- Hide the Admin Bar options on the profile page for users who have the admin bar disabled by the plugin
- Check on SESSION['cab_disabled_pages'] to make sure it is an array before disabling pages

= 1.0 =
- Added Admin Bar Menu to Admin Bar
- Session based disabling for whole site or single page


== Donations ==

[Contact Me](http://wesleytodd.com/contact)
