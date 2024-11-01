<?php

		/**
		 * Retrieve a shortcode from Database
		 *
		 * @param int $id Shortcode ID in Database
		 * @return array Formatted Shortcode content
		 */
		function tc_db_get_shortcode($id) {

			global $wpdb;

			// Select query
			$result = $wpdb->get_row('
				SELECT * FROM ' . $wpdb->prefix . 'tc_shortcodes WHERE id = ' . esc_sql($id)
			);

			// No results, no party
			if(!$result) return false;

			// Unserialize values
			$meta = maybe_unserialize($result->meta);
			$meta_formats = maybe_unserialize($result->meta_formats);
			$colors = maybe_unserialize($result->colors);

			// Build array
			$extracted_values = array(

				'id' => $result->id,

				'title' => $result->title,
				'language' => $result->lang,
				'continent' => $result->continent,
				'country' => $result->country,
				'province' => $result->province,
				'location' => $result->location,
				'location_link' => $result->location_link,
				'location_label' => $result->location_label,
				'time' => $result->time,
				'format' => $result->format,
				'style' => $result->style,

				'cache_time' => $result->cache_time,

				'days' => $meta['days'],
				'symbol' => $meta['symbol'],
				'temperature' => $meta['temperature'],
				'wind' => $meta['wind'],
				'relative_humidity' => $meta['relative_humidity'],
				'snow_height' => $meta['snow_height'],
				'pressure' => $meta['pressure'],
				'sunrise_sunset' => $meta['sunrise_sunset'],
				'moon_output_setting' => $meta['moon_output_setting'],
				'rain' => $meta['rain'],

				'temperature_format' => $meta_formats['temperature_format'],
				'wind_format' => $meta_formats['wind_format'],
				'rain_format' => $meta_formats['rain_format'],

				'marquee' => $colors['marquee'],
				'background' => $colors['background'],
				'text' => $colors['text'],
				'max' => $colors['max'],
				'min' => $colors['min'],

				'font' => $result->font,
			);
	
			// Return clean array
			return tc_db_get_processed_values($extracted_values);
		}

		/**
		 * Insert a Shortcode into Database
		 *
		 * @param array $data Shortcode data to insert in Database
		 * @return int|false The number of rows inserted, or false on error.
		 */

		function tc_db_create_shortcode($data) {

			global $wpdb;

			// Clean data
			$data = tc_db_get_processed_values($data);

			// API
			$api = new TiempoCom_API();

			// Get data from API (mainly for cache_time value)
			$instance = $api -> get_data($data);

			// Prepare values
			$insert = tc_db_prepare_to_db($data, $instance);

			// Run insert
			return $wpdb->insert( 
				$wpdb->prefix . 'tc_shortcodes', 
				$insert['values'], 
				$insert['formats']
			);
		}

		/**
		 * Update an existing Shortcode in Database
		 *
		 * @param int $id Shortcode ID to update in Database
		 * @param array $data Shortcode data to update in Database
		 * @return int|false The number of rows updated, or false on error.
		 */

		function tc_db_update_shortcode($id, $data) {

			global $wpdb, $lw_cache;

			// Clean data
			$data = tc_db_get_processed_values($data);

			// API
			$api = new TiempoCom_API();

			// Get data from API (mainly for cache_time value)
			$instance = $api -> get_data($data);

			// Prepare values
			$insert = tc_db_prepare_to_db($data, $instance);

			// Remove cached shortcode
			$lw_cache->remove('shortcode_' . $id);

			// Run update
			return $wpdb->update( 
				$wpdb->prefix . 'tc_shortcodes', 
				$insert['values'], 
				array( 'id' => esc_sql($id) ),
				$insert['formats'],
				array('%d')
			);
		}

		/**
		 * Update the cache_time value from a shortcode
		 *
		 * @param int $id Shortcode ID to update in Database
		 * @param array $data Shortcode data to update in Database
		 * @return int|false The number of rows updated, or false on error.
		 */
		function tc_db_update_shortcode_cache_time($id, $data) {

			global $wpdb;

			// Run the update, everything is ok
			return $wpdb->update( 
				$wpdb->prefix . 'tc_shortcodes', 
				array( 
					'cache_time' => esc_sql($data->expiracion),
				), 
				array( 'id' => esc_sql($id) ),
				array( 
					'%d'
				),
				array('%d')
			);

		}

		/**
		 * Update the cache_time value from a shortcode
		 *
		 * @param array|int $ids Shortcode ID or IDs to delete in Database
		 * @return array An array with the result of each ID row deletion
		 */
		function tc_db_delete_shortcode($ids) {
			
			global $wpdb;
			$output = array();

			// Check if there various ids
			if(is_array($ids)) {

				// Delete them all
				foreach($ids as $id) {
					$output[$id] = $wpdb->query( $wpdb->prepare('
						DELETE FROM ' . $wpdb->prefix . 'tc_shortcodes WHERE id = %d', esc_sql($id) ) 
					);
				}

			} else {

				// Delete just one shortcode
				$output[$ids] = $wpdb->query( $wpdb->prepare('
					DELETE FROM ' . $wpdb->prefix . 'tc_shortcodes WHERE id = %d', esc_sql($ids) ) 
				);
			}

			return $output;
		}		

		/**
		 * Prepare array values to insert into DB
		 *
		 * @param array $data Shortcode data to insert into database
		 * @param array $instance Retrieved data from API
		 * @return array An array with escaped values and formats
		 */
		function tc_db_prepare_to_db($data, $instance) {

			// Values to return
			$values = array( 
				'title' => esc_sql($data['title']), 
				'lang' => esc_sql($data['language']),
				'continent' => esc_sql($data['continent']),
				'country' => esc_sql($data['country']),
				'province' => esc_sql($data['province']),
				'location' => esc_sql($data['location']),
				'location_link' => esc_sql($data['location_link']),
				'location_label' => esc_sql($data['location_label']),
				'time' => esc_sql($data['time']),
				'format' => esc_sql($data['format']),
				'style' => esc_sql($data['style']),

				'cache_time' => esc_sql($instance->expiracion),

				'meta' => serialize(array(
					'days' => esc_sql($data['days']),
					'symbol' => esc_sql($data['symbol']),
					'temperature' => esc_sql($data['temperature']),
					'wind' => esc_sql($data['wind']),
					'relative_humidity' => esc_sql($data['relative_humidity']),
					'snow_height' => esc_sql($data['snow_height']),
					'pressure' => esc_sql($data['pressure']),
					'sunrise_sunset' => esc_sql($data['sunrise_sunset']),
					'moon_output_setting' => esc_sql($data['moon_output_setting']),
					'rain' => esc_sql($data['rain'])
				)),
				'meta_formats' => serialize(array(
					'temperature_format' => esc_sql($data['temperature_format']),
					'wind_format' => esc_sql($data['wind_format']),
					'rain_format' => esc_sql($data['rain_format'])
				)),
				'colors' => serialize(array(
					'marquee' => esc_sql($data['marquee']),
					'background' => esc_sql($data['background']),
					'text' => esc_sql($data['text']),
					'max' => esc_sql($data['max']),
					'min' => esc_sql($data['min'])
				)),
				'font' => esc_sql($data['font'])
			);
	
			// Setup formats, no trick here
			$formats = array( 
					'%s', 
					'%s',
					'%d',
					'%d',
					'%d',
					'%d',
					'%s',
					'%s',
					'%d',
					'%d',
					'%d',
					'%d',
					'%s',
					'%s',
					'%s',
					'%d'
			);

			// Return everything in place
			return array(
				'values' => $values,
				'formats' => $formats
			); 
		}

		/**
		 * Prepares array to be used in user interface, or sets vars to default values
		 *
		 * @param array|null $instance Previous shortcode data if exists
		 * @return array Processed array
		 */
		function tc_db_get_processed_values($instance = null) {

			// Title
			$title = (isset($instance['title'])) ? $instance['title'] : __( 'Tiempo.com', 'tiempocom' );

			$lang_code = explode('_', get_locale());
			$default_lang = $lang_code[0];

			// Language
			$language = (isset($instance[ 'language' ])) ? $instance['language'] : $default_lang;

			// Location
			$continent = (isset($instance[ 'continent' ])) ? $instance['continent'] : 0;
			$country = (isset($instance[ 'country' ])) ? $instance['country'] : 0;
			$province = (isset($instance[ 'province' ])) ? $instance['province'] : 0;
			$location = (isset($instance[ 'location' ])) ? $instance['location'] : 0;

			$location_link = (isset($instance[ 'location_link' ])) ? $instance['location_link'] : 0;
			$location_label = (isset($instance[ 'location_label' ])) ? $instance['location_label'] : 0;

			// Time
			$time = (isset($instance[ 'time' ])) ? $instance['time'] : 5;

			// Format
			$format = (isset($instance[ 'format' ])) ? $instance['format'] : 1;

			// Style
			$style = (isset($instance[ 'style' ])) ? $instance['style'] : 1;

			// Meta
			$days = (isset($instance[ 'days' ])) ? $instance['days'] : 1;
			$symbol = (isset($instance[ 'symbol' ])) ? $instance['symbol'] : 1;
			$temperature = (isset($instance[ 'temperature' ])) ? $instance['temperature'] : 0;
			$wind = (isset($instance[ 'wind' ])) ? $instance['wind'] : 0;
			$relative_humidity = (isset($instance[ 'relative_humidity' ])) ? $instance['relative_humidity'] : 0;
			$snow_height = (isset($instance[ 'snow_height' ])) ? $instance['snow_height'] : 0;
			$pressure = (isset($instance[ 'pressure' ])) ? $instance['pressure'] : 0;
			$sunrise_sunset = (isset($instance[ 'sunrise_sunset' ])) ? $instance['sunrise_sunset'] : 0;
			$moon_output_setting = (isset($instance[ 'moon_output_setting' ])) ? $instance['moon_output_setting'] : 0;
			$rain = (isset($instance[ 'rain' ])) ? $instance['rain'] : 0;
			
			// Meta formats
			$temperature_format = (isset($instance[ 'temperature_format' ])) ? $instance['temperature_format'] : 0;
			$wind_format = (isset($instance[ 'wind_format' ])) ? $instance['wind_format'] : 0;
			$rain_format = (isset($instance[ 'rain_format' ])) ? $instance['rain_format'] : 0;

			// Color options
			$marquee = (isset($instance[ 'marquee' ])) ? $instance['marquee'] : '#000000';
			$background = (isset($instance[ 'background' ])) ? $instance['background'] : '#FFFFFF';
			$text = (isset($instance[ 'text' ])) ? $instance['text'] : '#808080';
			$max = (isset($instance[ 'max' ])) ? $instance['max'] : '#FF0000';
			$min = (isset($instance[ 'min' ])) ? $instance['min'] : '#0000FF';

			// Font
			$font = (isset($instance[ 'font' ])) ? $instance['font'] : 1;

			// Caching
			$cache_time = (isset($instance[ 'cache_time' ])) ? $instance['cache_time'] : 0;

			// Return the built array
			return array(
				'title' => $title,
				'language' => $language,
				'continent' => $continent,
				'country' => $country,
				'province' => $province,
				'location' => $location,
				'location_link' => $location_link,
				'location_label' => $location_label,
				'time' => $time,
				'format' => $format,
				'style' => $style,

				'cache_time' => $cache_time,

				'days' => $days,
				'symbol' => $symbol,
				'temperature' => $temperature,
				'wind' => $wind,
				'relative_humidity' => $relative_humidity,
				'snow_height' => $snow_height,
				'pressure' => $pressure,
				'sunrise_sunset' => $sunrise_sunset,
				'moon_output_setting' => $moon_output_setting,
				'rain' => $rain,

				'temperature_format' => $temperature_format,
				'wind_format' => $wind_format,
				'rain_format' => $rain_format,

				'marquee' => $marquee,
				'background' => $background,
				'text' => $text,
				'max' => $max,
				'min' => $min,

				'font' => $font,

			);
		}

?>