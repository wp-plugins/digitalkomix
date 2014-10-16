=== digitalkOmiX ===
Contributors: andywar65
Donate link: http://www.andywar.net/wordpress-plugins/donate
Tags: image, text, comics, balloons, shortcode
Requires at least: 4.0
Tested up to: 4.0
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin adds balloons to traditional comic frames. Balloons become part of the content of the post, and can be translated. 

== Description ==

This plugin adds balloons to traditional comic frames. Balloons become part of the content of the post, and can be translated. 
Image becomes an HTML &lt;table&gt; background, while balloons are cells of that &lt;table&gt;.
At this stage plugin works through a shortcode, that can be self-closing: [digkom]

or enclosing: [digkom]&lt;IMAGE&gt;[/digkom]

In the self-closing shortcode you will have to add image location and size, while the enclosing shortcode handles the image added
directly from the Media Library. Below is a list of the shortcode attributes:

Attributes for self-closing shortcode only

*	image_url='URL of the image' : where your image is located.
*	image_link='Link to your image' : you go there when you click on the image.
*	width='Width of the image in pixels'
*	height='Height of the image in pixels'

General attributes

*	rows='Number of rows' : how many rows of balloons you have on the image.
*	cols='Number of columns' : how many columns of balloons you have on the image, please note that you may not have more than 
	12 balloons on the image.
*	caption='Text of the caption of the image' : text that will appear above the image, if you want it to appear on the bottom 
	you have to add "&lt;bottom&gt;" at the end of the text.
*	text_1='Text in the first balloon' : first balloon is in the top left corner of the image, last balloon is in the bottom right corner.
*	text_nth='Text in the nth balloon' : position of the nth balloon depends on how many rows and columns you set.

Please note that if you don't write text in a balloon, the balloon will not be displayed.
If no text is written at all and you preview (or publish) the post, the image will appear with cell grid on it, with numbered text areas. 
This may be useful to tune up the row and column number.
If you want a balloon to span more than a cell, you have to add "&lt;span r,c&gt;" at the end of the text, 
where "r" stands for rowspan and "c" stands for columnspan.

== Installation ==

1. Download and unzip `digitalkomix` folder, then upload it to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Nothing else

== Frequently Asked Questions ==

= Does the plugin work on multisites? =

Yes, it does.

= Does it work on all themes? =

It has been tested on twentythirteen and twentyfourteen.

== Screenshots ==

1. This is how the balloon appears on the frame.
2. This is how the grid appears when no text is added.

== Changelog ==

= 1.1 =
* Grid function added: if no text is written, on post/page preview a grid is overlayed on the image, with numbered text areas. This may be 
useful to choose appropriate number of rows x cols.

= 1.0 =
* First release.

== Upgrade Notice ==

= 1.1 =
* Grid function added.

= 1.0 =
No upgrades available.