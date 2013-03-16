=== Plugin Readme to Post ===

Contributors: Zane Matthew
Donate link: http://zanematthew.com/blog/plugins/plugin-readme-to-post/
Tags: parse readme, embed, markdown, parser, shortcode, plugin readme, readme, readme creator, readme generator
Requires at least: Latest
Tested up to: Latest
Stable tag: Trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
Automattically imports your Plugin readme.txt into a Post or Page in a tabbed format similar to that shown on WordPress.org.

If your WordPress Plugin is on the same install as the Plugin Readme to Post Plugin the readme content will be derived for you, i.e., Post title "my awesome plugin", plugin name "my awesome plugin". If not you can assign the URL to the readme.txt file.

*Note, displaying of screenshots is only supported if they are on the same domain as your plugins*

*Thanks too [Michel Fortin](http://michelf.ca/projects/php-markdown/) for porting Markdown to PHP.*

== Installation ==
1. Install the plugin via WordPress admin or download and upload the plugin to `wp-content/plugins/`
1. Activate the Plugin
1. Add the following shortcode `[parse_readme]` to a Post or Page where you want the readme tabs to show


== Frequently Asked Questions ==

=== Can I use a url for the readme? ===
Yes, just use the following:
`[parse_readme url="http://my-site.com/readme.txt/"]`

=== I don't want jQuery UI tabs? ===
From your WordPress admin click on "Settings --> Plugin Readme to Post", uncheck "Use jQuery UI Tabs". *Note all sections of the readme.txt will be displayed!*

== Screenshots ==
1. Description section
1. Admin settings
1. FAQ section
1. Screenshot section

== Changelog ==
= 0.1-alpha =
* Initial release