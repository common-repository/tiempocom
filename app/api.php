<?php

	/**
	* TiempoCom API
	*/
	class TiempoCom_API
	{
		var $lookup_url = 'http://api.tiempo.com/peticionWordPress.php?';

		function __construct() {}

		// Gets a API response in JSON or Array.
		function get_api_response($params, $json_decode = true) {

			// URL Building.
			$url = $this->lookup_url . http_build_query($params);

			// We could use cURL, but we want it to work on all possible machines.
			$content = file_get_contents($url);

			// Return array or json formats.
			if($json_decode) {
				return json_decode($content);
			}

			return $content;
		}

		function get_data($args) {

			$code = get_option('tc_install_code');

			$params = array(
				'tipo' => 7,
				'id' => $args['location'],
				'd' => $args['time'],
				'h' => 0, // No lo estamos usando / 0 (ocultar) y 1 (mostrar), por defecto 1
				'simb' => $args['symbol'], // 0 (ocultar) y 1 (mostrar), por defecto 1
				'temp' => $args['temperature'], // 0 (ocultar) y 1 (mostrar), por defecto 1
				'utemp' => $args['temperature_format'], //$args['temperature_format'], // Revisar formatos -- 0 (celsius) y 1 (fahrenheit), por defecto 0
				'v' => $args['wind'], // 0 (ocultar) y 1 (mostrar), por defecto 1.
				'uv' => $args['wind_format'], // 0 (Km/h), 1 (mph) y 2 (m/s), por defecto 0
				'll' => $args['rain'], // 0 (ocultar) y 1 (mostrar), por defecto 1
				'ull' => $args['rain_format'], // ? valores 0 (mm) y 1 (in)
				'p' => $args['pressure'], // 0 (ocultar) y 1 (mostrar), por defecto 1
				'hu' => $args['relative_humidity'], // 0 (ocultar) y 1 (mostrar), por defecto 1
				'cn' => $args['snow_height'], // 0 (ocultar) y 1 (mostrar), por defecto 1
				'nu' => 0, // No lo estamos usando / 0 (ocultar) y 1 (mostrar), por defecto 1
				'ni' => 0, // No lo estamos usando / 0 (ocultar) y 1 (mostrar), por defecto 1
				'sl' => ($args['sunrise_sunset'] or $args['moon_output_setting']) ? 1 : 0, // 0 (ocultar) y 1 (mostrar), por defecto 1
				'idioma' => $args['language'],
				'code' => $code
			);

			return $this->get_api_response($params);
		}


		// Función para obtener el listado paises / etc del API.
		function get_list($tipo, $id = null, $language = null) {

			// Build vars and cache key
			$params['tipo'] = $tipo;
			$cache_key = 'tc_api_req_' . $tipo;

			if($id) {
				$params['id'] = $id;
				$cache_key .= '-' . $id;
			}

			if($language) {
				$params['idioma'] = $language;
				$cache_key .= '-' . $language;
			}

			// Cache the api request
			$result = wp_cache_get($cache_key, 'tc_api_requests');

			// No cache, no party
			if( false === $result) {

				// Get a fresh request
				$result = $this->get_api_response($params);

				// Save it to WP Cache
				wp_cache_set( $cache_key, $result, 'tc_api_requests', 3600 );
			}
			
			return $result;
		}

		// Busqueda en el API. No lo usamos.
		function get_search_location($s, $page = 1, $language) {
			$params = array(
				'tipo' => 6,
				'texto' => $s,
				'pagina' => $page,
				'idioma' => $language
			);
			return $this->get_api_response($params);
		}

		function activate_plugin($domain) {

			$params = array(
				'tipo' => 8,
				'dominio' => $domain
			);

			return $this->get_api_response($params);
		}

		function deactivate_plugin($code) {

			$params = array(
				'tipo' => 9,
				'code' => $code
			);

			return $this->get_api_response($params);
		}
	}
?>