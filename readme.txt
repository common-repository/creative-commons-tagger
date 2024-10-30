=== Creative Commons Tagger ===
Contributors: Haldaug
Tags: creative commons, license, cc, images
Requires at least: 3.0.1
Tested up to: 3.8
Stable tag: 0.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds support for tagging images with creative commons licenses. 

== Description ==

This plugin adds support for providing licensing information for images uploaded in the media library. The user can input the Creative Commons licensing information in custom fields in the media post. The licensing information is displayed underneath the image in the wp-caption element.


== Installation ==

1. Upload the folder `creative-commons-tagger` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Does the plugin support other languages? =

You are free to translate the plugin yourself using the provided .PO-files.

= Are localized licenses provided? =

The plugin supports the localized Creative Commons licenses for Norway and USA in addition to the international licese. You can specify which in the settings page. I will add more localized licenses in the future.

== Screenshots ==

1. The licensing information displayed by the plugin underneath an image.
2. The custom fields on the media page where you can fill in the licensing information.
3. This is a screen shot of the settings page.


== Changelog ==

= 0.6 =
* Cleaned up css to make it compatible withmost themes
* Added support for more display options.

= 0.5 =
* Added Norwegian translation


== Upgrade Notice ==

= 0.6 =
* The font sizes of the license are now inherited from the parent div. This will ensure proper font sizes for the licensing information.
* You can now select between 3 display options in the settings page: Float right, float left or below the caption text.

= 0.5 =
First stable release

== Other Notes ==

= Credits =

This plugin is based on the code given in the tutorial 'How to Add Custom Fields to Attachments' by Guillaume Voisin: [wp.tutsplus.com](http://wp.tutsplus.com/tutorials/creative-coding/how-to-add-custom-fields-to-attachments/)
