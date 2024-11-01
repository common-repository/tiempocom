<?php
		/**
		 * Languages
		 *
		 * @return array Existing languages
		 */
		function tc_static_get_languages() {
			return array(
				'de' => __('German', 'tiempocom'),
				'ca' => __('Catalan', 'tiempocom'),
				'es' => __('Spanish (Spain)', 'tiempocom'),
				'fr' => __('French', 'tiempocom'),
				'en' => __('English', 'tiempocom'),
				'it' => __('Italian', 'tiempocom'),
				'pt' => __('Portuguese', 'tiempocom'),
				'ga' => __('Galician', 'tiempocom'),
				'eu' => __('Euskera', 'tiempocom')
			);
		}
		
		/**
		 * Get Days
		 *
		 * @return array Days range
		 */
		function tc_static_get_days() {
			return range(1, 7);
		}
		
		/**
		 * Get Meta Fields
		 *
		 * @return array Existing meta fields
		 */
		function tc_static_get_meta_fields() {
			return array(
				'days' => __('Days', 'tiempocom'),
				'symbol' => __('Symbol', 'tiempocom'),
				'temperature' => __('Temperature', 'tiempocom'),
				'wind' => __('Wind', 'tiempocom'),
				'relative_humidity' => __('Relative humidity', 'tiempocom'),
				'snow_height' => __('Snow height', 'tiempocom'),
				'pressure' => __('Pressure', 'tiempocom'),
				'sunrise_sunset' => __('Sunrise/Sunset', 'tiempocom'),
				'moon_output_setting' => __('Moon', 'tiempocom'),
				'rain' => __('Rain', 'tiempocom')
			);
		}

		/**
		 * Get styles
		 *
		 * @return array Range of existing styles
		 */
		function tc_static_get_styles() {
			return range(1, 19);
		}

		/**
		 * Get template formats
		 *
		 * @return array Formats with numeric keys
		 */
		function tc_static_get_formats() {
			return array(
	            1 => __('Normal', 'tiempocom'),
	            2 => __('Small', 'tiempocom'),
	            3 => __('Horizontal', 'tiempocom'),
	            4 => __('Vertical', 'tiempocom'),
	            5 => __('Wide', 'tiempocom'),
	            6 => __('Complete', 'tiempocom')
	        );
		}

		/**
		 * Get the days of the week
		 *
		 * @return array Weekdays with numeric keys
		 */
		function tc_static_get_weeks() {
			return array(
				1 => __('Monday', 'tiempocom'),
				2 => __('Tuesday', 'tiempocom'),
				3 => __('Wednesday', 'tiempocom'),
				4 => __('Thursday', 'tiempocom'),
				5 => __('Friday', 'tiempocom'),
				6 => __('Saturday', 'tiempocom'),
				7 => __('Sunday', 'tiempocom')
			);
		}

		/**
		 * Get the font options
		 *
		 * @return array Fonts with numeric keys
		 */
		function tc_static_get_fonts() {
			return array(
				1 => 'Arial',
				2 => 'Helvetica',
				3 => 'Lucida Grande',
				4 => 'Monospace',
				5 => 'Sans-serif',
				6 => 'Times New Roman',
				7 => 'Verdana'
			);
		}

		/**
		 * Get the temperature formats
		 *
		 * @return array Temperature formats with numeric keys
		 */
		function tc_static_get_temperature_formats() {
			return array(
				0 => '°C',
				1 => '°F'
			);
		}

		/**
		 * Get the wind formats
		 *
		 * @return array Wind format with numeric keys
		 */
		function tc_static_get_wind_formats() {
			return array(
				0 => 'km/h',
				1 => 'm/s',
				2 => 'mph'
			);
		}

		/**
		 * Get the rain formats
		 *
		 * @return array Rain formats with numeric keys
		 */
		function tc_static_get_rain_formats() {
			return array(
				0 => 'mm',
				1 => 'in'
			);
		}

		/**
		 * Template vars
		 *
		 * @return array Template vars translations
		 */
		function tc_get_template_vars() {

			return array(
				'Weather in %s' => array(
					'de' => "Wetter in %s",
					'ca' => "El tems a %s",
					'es' => "El tiempo en %s",
					'fr' => "météo",
					'en' => false,
					'it' => "Meteo",
					'pt' => "Tempo em %s",
					'ga' => "Tempo %s",
					'eu' => "Eguraldia tan %s"
				),

				'Today' => array(
					'de' => "Heute",
					'ca' => "Avui",
					'es' => "Hoy",
					'fr' => "aujourd'hui",
					'en' => false,
					'it' => "Meteo",
					'pt' => "Tempo em %s",
					'ga' => "Tempo %s",
					'eu' => "Gaur"
				),

				'Humidity' => array(
					'de' => "Luftfeuchtigkeit",
					'ca' => "Humitat",
					'es' => "Humedad",
					'fr' => "Humidité",
					'en' => false,
					'it' => "Umidità",
					'pt' => "Umidade",
					'ga' => "Humidade",
					'eu' => "Hezetasuna"
				),

				'Rain' => array(
					'de' => "Regen",
					'ca' => "Pluja",
					'es' => "Lluvia",
					'fr' => "La pluie",
					'en' => false,
					'it' => "Pioggia",
					'pt' => "Chuva",
					'ga' => "Chuvia",
					'eu' => "Euria"
				),

				'Snow' => array(
					'de' => "Schnee",
					'ca' => "Neu",
					'es' => "Nieve",
					'fr' => "Niege",
					'en' => false,
					'it' => "Neve",
					'pt' => "Neve",
					'ga' => "Neve",
					'eu' => "Elurra"
				),

				'Pressure' => array(
					'de' => "Luftdruck",
					'ca' => "Presió",
					'es' => "Presión",
					'fr' => "Pression",
					'en' => false,
					'it' => "Pressione",
					'pt' => "Pressão",
					'ga' => "Presión",
					'eu' => "Presioa"
				),

				'Sunrise/Sunset' => array(
					'de' => "Sunrise/Sunset",
					'ca' => "Alba/Ocàs",
					'es' => "Alba/Ocaso",
					'fr' => "Sunrise/Sunset",
					'en' => false,
					'it' => "Alba/Tramonto",
					'pt' => "Sunrise/Sunset",
					'ga' => "Sunrise/Sunset",
					'eu' => "Eguzki-irteera/-sarrera"
				),

				'Monday' => array(
					'de' => "Montag",
					'ca' => "Dilluns",
					'es' => "Lunes",
					'fr' => "Lundi",
					'en' => false,
					'it' => "Lunedi",
					'pt' => "Segunda",
					'ga' => "Luns",
					'eu' => "Astelehena"
				),

				'Tuesday' => array(
					'de' => "Dienstag",
					'ca' => "Dimarts",
					'es' => "Martes",
					'fr' => "Mardi",
					'en' => false,
					'it' => "Martedì",
					'pt' => "Terça",
					'ga' => "Martes",
					'eu' => "Asteartea"
				),

				'Wednesday' => array(
					'de' => "Mittwoch",
					'ca' => "Dimecres",
					'es' => "Miércoles",
					'fr' => "Mercredi",
					'en' => false,
					'it' => "Miercoledì",
					'pt' => "Quarta",
					'ga' => "Mércores",
					'eu' => "Asteazkena"
				),

				'Thursday' => array(
					'de' => "Donnerstag",
					'ca' => "Dijous",
					'es' => "Jueves",
					'fr' => "Jeudi",
					'en' => false,
					'it' => "Giovedi",
					'pt' => "Quinta",
					'ga' => "Xoves",
					'eu' => "Osteguna"
				),

				'Friday' => array(
					'de' => "Freitag",
					'ca' => "Divendres",
					'es' => "Viernes",
					'fr' => "Vendredi",
					'en' => false,
					'it' => "Venerdì",
					'pt' => "Sexta",
					'ga' => "Venres",
					'eu' => "Ostirala"
				),

				'Saturday' => array(
					'de' => "Samstag",
					'ca' => "Dissabte",
					'es' => "Sábado",
					'fr' => "Samedi",
					'en' => false,
					'it' => "Sabato",
					'pt' => "Sábado",
					'ga' => "Sábado",
					'eu' => "Larunbata"
				),

				'Sunday' => array(
					'de' => "Sonntag",
					'ca' => "Diumenge",
					'es' => "Domingo",
					'fr' => "Dimanche",
					'en' => false,
					'it' => "Domenica",
					'pt' => "Domingo",
					'ga' => "Domingo",
					'eu' => "Igandea"
				),
			);
		}

?>