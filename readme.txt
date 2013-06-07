=== Rewrite Flush Button ===

Contributors: sscovil 
Tags: rewrite, flush, permalink, admin, troubleshooting
Requires at least: 3.1
Tested up to: 3.5.1
Stable tag: 1.0

Adds a "Flush Rewrite Rules" button to WP-Admin > Settings > Permalinks.


== Description ==

A simple plugin that adds a Troubleshooting section to WP-Admin > Settings > Permalinks with a button for flushing
your rewrite rules.

From the Codex:

> Flushing the rewrite rules is an expensive operation, there are tutorials and examples that suggest executing it on the 'init' hook. This is bad practice. Instead you should flush rewrite rules on the activation hook of a plugin, or when you know that the rewrite rules need to be changed ( e.g. the addition of a new taxonomy or post type in your code ).

This plugin enables WordPress site admins to manually perform this action once, as needed, whenever there is a problem.

== Installation ==

1. Install and activate the plugin via `WP-Admin > Plugins`.
2. Go to `WP-Admin > Settings > Permalinks` and click the button to flush rewrite rules.


== Changelog ==

= 0.1 =
* Initial release.