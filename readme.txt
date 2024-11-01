=== Tiempo.com ===
Contributors: tiempocom
Tags: Weather, widget, tiempo, el tiempo, meteo, prevision del tiempo, prevision meteorologica, prevision meteorologique, previsioni del tempo, Weather, weather forecast, weather report, weerbericht, wetter, Wetterbericht, Wettervorhersage
Requires at least: 3.5
Tested up to: 3.7
Stable tag: 0.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Tiempo.com for WordPress. Allows to add weather widgets and shortcodes from the tiempo.com API.

== Description ==

This plugin provides you the daily weather forecast for the locations you choose.
With no configuration, you will be able to manage widgets and shortcodes with no hassle.

Features:

* Ability to specify a different language for each widget/shortcode.
* Locations all over the world.
* Tons of options and configurations including: colors, layouts, formats, and many more.

Speed load impact:

* Only a CSS file will be loaded, no JS on the front-end.
* Images are loaded from the tiempo.com static file CDN server.
* Internal file caching.

Important: In order to mitigate the site speed impact, please make sure to set write permissions to the `tiempocom/cache` directory. The plugin performs an intelligent expiry time management to ensure you have always the data up to date.

Supported languages:

* English
* Spanish (Spain)
* German
* Catalan
* French
* Italian
* Portuguese
* Euskera
* Galician

== Installation ==

1. Upload the `tiempocom` folder to the `/wp-content/plugins/` directory.
2. Set write permissions to the `tiempocom/cache` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Start creating shortcodes or adding widgets to your sidebars.

== Screenshots ==

1. Manage your shortcodes.
2. Create or edit shortcodes.
3. Add and modify widgets.

== Changelog ==

= 0.1.1 =
* Added rel="nofollow" to widget links.

= 0.1.2 =
* Bug Fix: Wrong results when selecting provinces from certain countries
