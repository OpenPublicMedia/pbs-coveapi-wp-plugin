=== COVE API ===
Contributors: wnet, emroman
Tags: api, dashboard, video
Requires at least: 3.1.3
Tested up to: 3.2.1
Stable tag: 1.0

Access COVE programs and videos using the COVE API.

== Description ==

This plugin allows you to retrieve program and video metadata from version 1 of the COVE API. The plugin handles the authentication of requests using values maintained in the WordPress Admin. Results are cached using the WordPress Transients API.

The plugin uses the COVE_API_Request php class provided by Edgar Roman of PBS for making requests to the api. 

Links: [**COVE API V1**](https://projects.pbs.org/confluence/display/coveapi/COVE+API+Version+1)

== Installation ==

1. Upload the cove-api.zip file to the /wp-content/plugins directory and unzip
1. Activate the plugin from the Plugins menu in your admin menu
1. Configure the plugin by going to the COVE API Options menu item that appears in your admin menu


After activating the plugin, enter the API Key and Secret, the Cache TTL time, and any defaults in the COVE API Options screen.

== Frequently Asked Questions ==

None yet.

== Screenshots ==

1. Option Page
2. Dashboard Widget

== Changelog ==

= 1.0 =
* Initial external release

== Other Notes ==

W3TC users, if using the dashboard widget be sure that Object Cache is enabled and that "Don't cache WordPress Admin" is unchecked.

