=== How Old Am I ===
Contributors: StathisG
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=JT4GXFTBH99LS
Tags: how old am i, age, age calculator, calculate age, automatic age
Requires at least: 3.0.1
Tested up to: 4.2.4
Stable tag: 1.2.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

How Old Am I calculates and displays ages in several formats.

== Description ==
How Old Am I calculates and displays ages in several formats, giving you up-to-date detailed age information which can be added on your posts and pages, without having to constantly update them.

== Screenshots ==

1. The plugin's settings
2. Example using default settings
3. Example using custom format with age in numbers
4. Example using Custom format with age in full
5. Example using shortcode attributes

== Installation ==

= How to install =

1. Upload the extracted archive to wp-content/plugins/ or upload the archive from the admin dashboard (Plugins -> Add New -> Upload).
2. Activate the plugin via the Plugins menu

= How to configure =

1. Open the plugin settings page (Settings -> How Old Am I).
2. Enter your birth date.
3. Select a display format.
4. Select how to deal with negative ages.
5. Select the appropriate PHP version (the default selection is PHP 5.3 or later). If you don't know which PHP version is installed on your server, then try the plugin with the default option and if it doesn't work, switch to the other one.

= How to use =

Select your date of birth and enter the shortcode [how-old-am-i] in any post or page.

The following attributes are available to be used in the shortcode (the attributes can be combined):

* on — takes as an argument either a date (format: YYYY-MM-DD) and overrides the current date, or the word "post" and uses the date & time of the post to override the current date
* bday — takes as an argument either a date (format: YYYY-MM-DD) and overrides the birth date set on the plugin's settings, or the word "post" and uses the date & time of the post to override the birth date

Some examples using the attributes:

* [how-old-am-i on="2013-03-01"] — displays the age as it was on the 1st of March, 2013
* [how-old-am-i on="post"] — displays the age as it was on the date the post was published on
* [how-old-am-i bday="1980-02-22"] — displays the age using as a birth date the 22nd of February, 1980
* [how-old-am-i bday="post"] — displays the age using as a birth date the date that the post was published on
* [how-old-am-i on="2013-03-01" bday="1980-02-22"] — displays the age of a person born on the 22nd of February, 1980, as it was on the 1st of March, 2013, ignoring both the birth date set in the plugin's setting, and the current date
* [how-old-am-i on="post" bday="1980-02-22"] — the same example as before, using the publish date of the post as the current date
* [how-old-am-i on="2013-03-01" bday="post"] — the same example as before, using the publish date of the post as the birth date

== Frequently Asked Questions ==

= How can I get support? =

For questions, issues, or feature requests, you can [contact me](http://burnmind.com/contact), or post them either in the [WordPress Forum](http://wordpress.org/tags/how-old-am-i) (make sure to add the tag "how-old-am-i"), or in [this](http://burnmind.com/freebies/how-old-am-i) blog post.

== Changelog ==

= 1.2.0 =
* Added the ability to show negative ages.

= 1.1.0 =
* Added "on" and "bday" shortcode attributes.

= 1.0.0 =
* Initial release.