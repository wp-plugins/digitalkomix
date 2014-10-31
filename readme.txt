=== digitalkOmiX ===
Contributors: andywar65
Donate link: http://www.andywar.net/wordpress-plugins/donate
Tags: image, text, comics, balloons, shortcode
Requires at least: 4.0
Tested up to: 4.0
Stable tag: 1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin adds balloons to traditional comic frames. Balloons become part of the content of the post, and can be translated. 

== Description ==

This plugin adds balloons to traditional comic frames. Balloons become part of the content of the post, and can be translated. 
Image becomes an HTML &lt;table&gt; background, while balloons are cells of that &lt;table&gt;.
At this stage plugin works through a shortcode, that can be automatically built in a settings page found in the "Media" menu
of the Dashboard. Shortcode Builder displays actual shortcode (that can be cut and pasted either in a post or a page), a preview
of the image with the balloons, and a view of the grid overlying the image. All settings can be edited in the settings fields below
these previews, including image information, dimension of the overlying grid and texts inside the balloons with their position and 
dimension. Shortcode builder enables only 4 balloons, but you can add more manually editing the pasted shortcode.

Generally speaking, shortcode can be enclosing: [digkom]&lt;IMAGE&gt;[/digkom]

or self-closing: [digkom] (shortcode builder generates this kind).

Enclosing shortcode handles the image directly added from the Media Library, while in the self-closing shortcode 
you will have to add image location and size. Below is a list of the shortcode attributes:

General attributes

*	rows='Number of rows' : how many rows of balloons you have on the image (by default set to 4).
*	cols='Number of columns' : how many columns of balloons you have on the image (by default set to 3).
*	caption='Text of the caption of the image' : text that will appear above the image, if you want it to appear on the bottom 
	you have to add "&lt;bottom&gt;" at the end of the text.
*	text_1='Text in the first balloon &lt;grid&gt;' : first balloon starting from the top left corner of the image. "&lt;grid&gt;" sets
	the position and dimension of the balloon (see below).
*	text_nth='Text in the nth balloon &lt;grid&gt;' : No more than 12 balloons may be added.

Attributes for self-closing shortcode only

*	image_url='URL of the image' : where your image is located.
*	image_link='Link to your image' : you go there when you click on the image.
*	width='Width of the image in pixels'
*	height='Height of the image in pixels'

Please note that if you don't write text in a balloon, the balloon will not be displayed.
If no text is written at all and you preview (or publish) the post, the image will appear with an overlying grid of numbered cells. 
This may be useful to determine row / column number, position and size of balloons.
To position and size balloons, add "&lt;grid 1st,2nd&gt;" at the end of the text_nth, where "1st" is the number of the top-left grid
cell contained by the balloon, while "2nd" is the bottom-right one (use only "1st" if one grid cell is used).
Balloon can contain 1 to all text areas.

In alternative to "&lt;grid&gt;", you may add "&lt;span r,c&gt;" at the end of the text_nth, 
where "r" stands for rowspan and "c" stands for columnspan. &lt;span&gt;, however, restricts table size to max 12 cells.

Additional instructions may be found here: http://www.andywar.net/wordpress-plugins/digitalkomix-plugin

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

1. This is how the overlying grid appears on the frame.
2. Choose top-left and bottom-right corner of the balloon. 
3. This is how the balloon appears once the text is added.
4. Shortcode Builder settings page. On top actual display, below preview of the comic frame and the grid.
5. Shortcode Builder settings page. Image info, grid size and text setting fields.

== Changelog ==

= 1.3 =
* Shortcode Builder settings page added to automatically generate shortcode to be cut and pasted in the post or page.

= 1.2 =
* Grid mode added: no limits to table size, balloon is positioned and sized defining the cells of it's top-left and bottom-right corner.

= 1.1 =
* Grid function added: if no text is written, on post/page preview a grid is overlayed on the image, with numbered text areas. This may be 
useful to choose appropriate number of rows x cols.

= 1.0 =
* First release.

== Upgrade Notice ==

= 1.3 =
* Shortcode Builder settings page added.

= 1.2 =
* Grid mode added.

= 1.1 =
* Grid function added.

= 1.0 =
* No upgrades available.