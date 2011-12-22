=========================
COVE API WordPress Plugin
=========================

Description:
============
This plugin allows you to retrieve program and video metadata from version 1 of the COVE API. The plugin handles the authentication of requests using values maintained in the WordPress Admin. Results are cached using the WordPress Transients API.

The plugin uses the COVE_API_Request php class provided by Edgar Roman of PBS for making requests to the api. 

Installation:
=============
1. Upload the cove-api.zip file to the /wp-content/plugins directory and unzip
2. Activate the plugin from the Plugins menu in your admin menu
3. Configure the plugin by going to the COVE API Options menu item that appears in your admin menu
4. After activating the plugin, enter the API Key and Secret, the Cache TTL time, and any defaults in the COVE API Options screen.

Links: 
======
`COVE API V1`_

`PBS Q&A Site`_

Requirements:
=============
* Requires at least: WordPress 3.1.3
* Tested up to: WordPress 3.2.1
* Stable tag: 1.0

Frequently Asked Questions:
===========================
Visit the `PBS Q&A Site`_ for frequently asked questions

Screenshots:
============
1. Option Page
2. Dashboard Widget

Changelog:
==========
:Version 1.0: Initial external release

Other Notes:
=============
* W3TC users, if using the dashboard widget be sure that Object Cache is enabled and that "Don't cache WordPress Admin" is unchecked.
* Tags: api, dashboard, video

Initial Contributors:
=====================
* WNET

  - Eric Knappe
* `PBS (GitHub)`_

  - Edgar Roman (`edgarroman`_)

  - Angel Ramboi (`limpangel`_)
  
  
.. _COVE API V1: 
    https://projects.pbs.org/confluence/display/coveapi/COVE+API+Version+1
    
.. _PBS Q&A Site:
    http://open.pbs.org/answers/
    
.. _PBS (GitHub):
    https://github.com/organizations/pbs
    
.. _edgarroman:
    https://github.com/edgarroman

.. _limpangel:
    https://github.com/limpangel