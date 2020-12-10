=== Mandatory Excerpts for WordPress ===
Contributors: theMikeD
Tags: excerpt
Plugin page: https://www.codenamemiked.com/plugins/mandatory-excerpt/
Requires at least: 4.4.0
Tested up to: 5.6
Requires PHP: 7.0
Stable tag: 1.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Causes the excerpt to be required before a post can be published. Supports both editor types (classic and block aka gutenberg). Filters are provided for customization; see the [User Guide](https://www.codenamemiked.com/plugins/mandatory-excerpt/) for complete usage tips.

== Description  ==

An excerpt is a (usually) brief, text-only post summary. It is used in various places of a theme when a summary of a post is needed, such as on your blog page and archive pages for terms.

Excerpts are either manually crafted or (usually poorly) automatically extracted from the main post content. Hand crafted ones are better because

* they avoid things like sentences being cut off in the mid…
* they allow you create an actual summary and not just the first x words from the post content
* they allow you to do better SEO targeting
* they look better

This plugin will enforce the use of hand-crafted excerpts by your users by preventing a post without an excerpt from being published.

Classic editor support is based on the work done by Scott Walkinshaw and other contributors, found [here](https://gist.github.com/swalkinshaw/2695510).

== Installation ==

Either use the WordPress Plugin Installer (Dashboard > Plugins > Add New, then search for "mandatory excerpt"), or manually as follows:

1. Upload the entire `mandatory-excerpt` folder to the `/wp-content/plugins/` directory
2. DO NOT change the name of the `mandatory-excerpt` folder
3. Activate the plugin through the 'Plugins' menu in the WordPress Dashboard

Note for WordPress Multisite users:

* Install the plugin in your `/plugins/` directory (do not install in the `/mu-plugins/` directory).
* In order for this plugin to be visible to Site Admins, the plugin has to be activated for each blog by the Network Admin.

== Upgrading from a previous version ==

Use the upgrade link in the Dashboard (Dashboard > Updates) to upgrade this plugin.

== Frequently Asked Questions ==

= I don’t see a spot for the excerpt in my Classic Editor post edit screen =

There are two possible reasons for this.

1. The post type does not support the excerpt field. Generally, posts and pages support it but it can be turned off in code.
2. The Excerpt field is not being displayed. To see it, click on Screen Options button on the top right corner of the classic editor post edit screen and then enable it.

= Does this work with Gutenberg / the block editor? =

Sure does, but a little differently. See [the manual](https://www.codenamemiked.com/plugins/mandatory-excerpt/) for more.

= Where can I get Support? =

Guidance on using the plugin can be found in the plugin's User Guide (TBD).

If you're still stuck or you think you fod a bug, you can post a message on the plugin's Support Forum.

Support is provided in my free time but every effort will be made to respond to support queries as quickly as possible.

Source code can be found in [github](https://github.com/theMikeD/mandatory-excerpt).

= Where is the Settings page? =

There isn't one.

= Does this plugin support custom post types? =

Yes, if the custom post type supports the excerpt field. But you'll have to specifically include it using the `mandatory_excerpt_post_types` filter. See [here](https://www.codenamemiked.com/plugins/mandatory-excerpt/filters/).

== License and Disclaimer ==

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License 2 as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

The license for this software can be found here: [http://www.gnu.org/licenses/gpl-2.0.html](http://www.gnu.org/licenses/gpl-2.0.html)

== Screenshots ==

1. Classic Editor: If you try to save a post without an excerpt, this message will appear and the post status will be reset to "draft."
2. Classic Editor: If you don't see the Excerpt section, it may be because it's not enabled. Click Screen Options at the top right of the edit screen, and enable it.
3. Gutenberg/Block Editor: the Publish/Update button is disabled until the excerpt has content. You also get a message about the missing excerpt.

== Changelog ==
= 1.1.1 =
* small fix for GB

= 1.1.0 =
* refactoring
* adds gutenberg/block editor support
* adds localization framework + fr_CA translation

= 1.0.0 =
* 11 January 2020
* Initial release