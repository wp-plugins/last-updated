=== last updated ===
Contributors: wudi96
Tags: widget
Requires at least: 3.0.1
Tested up to: 3.8
Stable tag: 1.6
License: CC-BY-SA 3.0
License URI: http://creativecommons.org/licenses/by-sa/3.0

Provides a widget that shows last updated posts (also custom post-types are supported).

== Description ==

EN:

This plugin provides a widget that shows last updated posts or pages or other custom post-types.

Settings:
You can set your own title.
You can change the number of displayed posts or pages. Standard is 5.
You can choose your post-type.
Optional it can display the date when the post or pages was updated.

DE:

Dieses Plugin stellt ein Widget zur Verfügung, das zuletzt aktualisierte Beiträge und Seiten anzeigt.

Einstellungen:
Man kann einen Titel, die Anzahl der aufgelisteten Seiten oder Beiträge festlegen und hat die Möglichkeit optional ein Datum anzuzeigen.
Außerdem kann man zwischen den verschiedenen post-types wählen.

== Installation ==

EN:

1. Upload `lastupdated` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Place the Widget in a Sidebar.

DE:

1. Lade den Ordner 'lastupdated'  in '/wp-content/plugins/' hoch.
2. Aktiviere das Plugin im 'Plugin' Menü von WordPRess.
3. Ziehe das Widget im 'Widget' Menü in eine Sidebar. 

== Frequently Asked Questions ==

= Can I change the Date Format? =

This plugin uses the Date Format what you have set in Settings->
General->Date Format.

== Screenshots ==

1. The last updated widget

== Changelog ==

= 1.6 =
Added support for all post-types.

= 1.5.1 =
Bugfix: changed ASC to DESC

= 1.5 =
Bugfix: Problem when saving posttype as German user.
New algorithm: Excludes Posts, when they are higher located in a 
table ordered by creation time than in a table ordered by updating time. -> Just 
displays posts that are really old and updated.

= 1.4.2 =
Some Design fixes

= 1.4 =
Choose now between 'posts', 'pages' or 'both'.

= 1.3 =
Internationalisation

== Upgrade Notice ==

= 1.6 =
you have to reset your post-type selection.

= 1.4 =
more comfortable :D

= 1.3 =
For English and German People.
