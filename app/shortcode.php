<?php

    /**
     * Tiempocom Shortcode
     *
     * Usage: [tiempocom id="ID"] 
     *
     * Called in /tiempocom.php
     */

	function tc_shortcode_inv($atts) {

        // Extract values
		extract(shortcode_atts(array(
            "id" => null
        ), $atts));

        // No ID, no party
        if(!$id) return '';

        // Get our cache lib
        global $lw_cache;

        // Get shortcode by $id
        if(!$instance = tc_db_get_shortcode($id)) {
            return '';
        }

        // Build some useful vars
        $cache_key = 'shortcode_' . $id;
        $cache_time = $instance['cache_time'];

        // Get cache if exists
        if(!$output = $lw_cache->get($cache_key, $cache_time)) {

            // Start caching
            $lw_cache->start();

            // Init API
            $api = new TiempoCom_API();
            
            // Get Data
            $data = $api -> get_data($instance);

            // Load the current template
            tc_parse_template($instance, $data);

            // Collect output
            $output = $lw_cache->collect();
            
            // Save cache
            $lw_cache->set( $cache_key, $output, $cache_time );

            // Update cache times
            tc_db_update_shortcode_cache_time( $id, $data );

        } else {

            // Output directly
            echo $output;
        }

	}


?>