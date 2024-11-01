<?php

		/**
		 * Gettext overriding in order to perform multi-lang shortcodes and widgets
		 *
		 * Called in /tiempocom.php
		 *
		 * @link http://codex.wordpress.org/Plugin_API/Filter_Reference/gettext
		 */
		function tc_template_gettext_overwrite($translated_text, $text, $domain) {

			if($domain == 'tiempocom') {

				$return = tc_gettext($text);

				if ( $return !== false ) {
					return $return;
				}
			}
			return $translated_text;
		}

		/**
		 * Sets global template language
		 *
		 * @global tc_template_lang
		 *
		 * @param string $language 	Language code to set
		 */
		function tc_set_template_language($language) {
			global $tc_template_lang;
			$tc_template_lang = $language;
		}

		/**
		 * Sets global template language to null
		 *
		 * @global tc_template_lang
		 */
		function tc_unset_template_language() {
			global $tc_template_lang;
			$tc_template_lang = null;
		}

		/**
		 * Returns the translated text if needed
		 *
		 * @param string $text 	String to translate
		 */
		function tc_gettext($text) {

			global $tc_template_lang;

			if($tc_template_lang and $tc_template_lang != 'en') {

				$template_vars = tc_get_template_vars();

				if(isset($template_vars[$text][$tc_template_lang])) {
					return $template_vars[$text][$tc_template_lang];
				}
			}

			return false;
		}

		/**
		 * Prints the Shortcode depending on format
		 *
		 * @param array $instance 	Shortcode data from Database
		 * @param array $data 		Retrieved data from API
		 */
		function tc_parse_template($instance, $data) {

			tc_set_template_language($instance['language']);

			switch($instance['format']) {
				case 1: tc_parse_normal_template($instance, $data); break;
				case 2: tc_parse_small_template($instance, $data); break;
				case 3: tc_parse_horizontal_template($instance, $data); break;
				case 4: tc_parse_vertical_template($instance, $data); break;
				case 5: tc_parse_wide_template($instance, $data); break;
				case 6: tc_parse_complete_template($instance, $data); break;
			}

			tc_unset_template_language();
		}

		/**
		 * Prints Shortcode with Normal format
		 *
		 * @param array $instance 	Shortcode data from Database
		 * @param array $data 		Retrieved data from API
		 */
		function tc_parse_normal_template($args, $data) {

			$shown_featured_day = false;

			$weekday = tc_static_get_weeks();
			$fonts = tc_static_get_fonts();

			?>
			<div class="tiempocom_widget">
				<table class="widget" id="webwid" style="max-width:170px;margin:0 auto;padding:0;color:<?php echo $args['text']; ?>;">
				    <tbody>
				        <tr>
				            <td align="center" style="width:170px;border: 1px solid <?php echo $args['marquee']; ?>;background-color:<?php echo $args['background']; ?>;font-family:<?php echo $fonts[$args['font']]; ?>;color:<?php echo $args['text']; ?>">
				                <a href="<?php echo $args['location_link']; ?>" id="wlink" rel="nofollow" class="wlink" target="_blank" title="El Tiempo en <?php echo $args['location_label']; ?>"><?php echo sprintf(__('Weather in %s', 'tiempocom'), $args['location_label']); ?></a>
				                <table class="fondo" width="100%">
				                    <tbody>
				                    	<?php foreach($data->prediccion as $day) { ?>

					                    	<?php if(!$shown_featured_day) { $shown_featured_day = true; ?>
						                        <tr>
						                        	<?php if($args['temperature']) { ?>
						                            	<td align="center" style="font-family:<?php echo $fonts[$args['font']]; ?>;font-size:24px;"><b style="color:<?php echo $args['max']; ?>;"><?php echo $day->maxima; ?>°</b><br/><b style="color:<?php echo $args['min']; ?>;"><?php echo $day->minima; ?>°</b></td>
						                            <?php } ?>
						                            <td colspan="2" align="center"><img src="http://css13.tiempo.com/widget/css/galeria<?php echo $args['style']; ?>/g<?php echo $day->simbolo; ?>.png" /></td>
						                        </tr>
					                        <?php } else { ?>
						                        <tr>
						                            <td align="center" style="color:<?php echo $args['text']; ?>;font-family:<?php echo $fonts[$args['font']]; ?>;font-size:13px;font-weight:bold;"><?php echo $weekday[$day->dia]; ?></td>
						                            <?php if($args['temperature']) { ?>
						                            	<td class="fondo" align="center" style="font-family:<?php echo $fonts[$args['font']]; ?>;font-size:13px;padding:0px;width:50px;"> <b style="color:<?php echo $args['max']; ?>"><?php echo $day->maxima; ?>°</b>&nbsp; <b style="color:<?php echo $args['min']; ?>"><?php echo $day->minima; ?>°</b> </td>
						                            <?php } ?>
						                            <td align="center"><img src="http://css13.tiempo.com/widget/css/galeria<?php echo $args['style']; ?>/pq<?php echo $day->simbolo; ?>.png" /></td>
						                        </tr>
					                        <?php } ?>

				                        <?php } ?>
				                        <tr>
				                            <td colspan="3" style="font-size:9px;text-align:right;padding-right:5px;"> tiempo.com&nbsp;&nbsp;<u><a href="<?php echo $args['location_link']; ?>" target="_blank" title="<?php echo sprintf(__('Weather in %s', 'tiempocom'), $args['location_label']); ?>">+info</a> </u></td>
				                        </tr>
				                    </tbody>
				                </table>
				            </td>
				        </tr>
				    </tbody>
				</table>
			</div>
			<?php
		}

		/**
		 * Prints Shortcode with Small format
		 *
		 * @param array $instance 	Shortcode data from Database
		 * @param array $data 		Retrieved data from API
		 */
		function tc_parse_small_template($args, $data) {

			$fonts = tc_static_get_fonts();

			?>
			<div class="tiempocom_widget">
				<table class="widget" id="webwid" style="max-width:70px;margin:0 auto;text-align:center;color:<?php echo $args['text']; ?>;font-family:<?php echo $fonts[$args['font']]; ?>;">
				    <tbody>
				        <tr>
				            <td style="border:1px solid <?php echo $args['marquee']; ?>;background-color:<?php echo $args['background']; ?>;width:70px;">
				                
				                <?php foreach($data->prediccion as $day) { ?>
				                
					                <table class="fondo">
					                    <tbody>
					                        <tr>
					                            <td colspan="2" align="center" width="100%" style="color:<?php echo $args['text']; ?>;font-weight:bold;"><?php _e('Today', 'tiempocom'); ?></td>
					                        </tr>
					                        <tr align="center">
					                            <td colspan="2" align="center"><img src="http://css13.tiempo.com/widget/css/galeria<?php echo $args['style']; ?>/pq<?php echo $day->simbolo; ?>.png"></td>
					                        </tr>

					                        <?php if($args['temperature']) { ?>

						                        <tr align="center">
						                            <td style="font-size:14px;font-weight:bold;color:<?php echo $args['text']; ?>"><?php _e('Max.', 'tiempocom'); ?></td>
						                            <td style="color:<?php echo $args['max']; ?>"><?php echo $day->maxima; ?>°</td>
						                        </tr>
						                        <tr align="center">
						                            <td style="font-size:14px;font-weight:bold;color:<?php echo $args['text']; ?>"><?php _e('Min.', 'tiempocom'); ?></td>
						                            <td style="color:<?php echo $args['min']; ?>"><?php echo $day->minima; ?>°</td>
						                        </tr>

					                        <?php } ?>

					                        <tr align="center">
					                            <td colspan="2" align="center" style="font-size:9px;"><u><a href="<?php echo $args['location_link']; ?>" title="<?php echo sprintf(__('Weather in %s', 'tiempocom'), $args['location_label']); ?>">+info</a></u></td>
					                        </tr>
					                    </tbody>
					                </table>

				                <?php } ?>

				            </td>
				        </tr>
				    </tbody>
				</table>
			</div>
			<?php
		}


		/**
		 * Prints Shortcode with Horizontal format
		 *
		 * @param array $instance 	Shortcode data from Database
		 * @param array $data 		Retrieved data from API
		 */
		function tc_parse_horizontal_template($args, $data) {

			$weekday = tc_static_get_weeks();
			$fonts = tc_static_get_fonts();

			$width = 188 + (100 * $args['time']);

			?>
			<div class="tiempocom_widget">
				<table align="center" class="widget" width="<?php echo $width; ?>px">
				    <tbody>
				        <tr>
				            <td style="border:1px solid <?php echo $args['marquee']; ?>; background-color:<?php echo $args['background']; ?>;">
				                <table class="fondo">
				                    <tbody>
				                        <tr>
				                            <td id="webwid" colspan="21" style="max-width:<?php echo $width; ?>px;margin:0 auto;color:<?php echo $args['text']; ?>;text-align:center;width:<?php echo $width; ?>px;">
				                                <a id="wlink" class="wlink" rel="nofollow" href="<?php echo $args['location_link']; ?>" alt="El Tiempo en <?php echo $args['location_label']; ?>" style="color:<?php echo $args['text']; ?>;font-family:<?php echo $fonts[$args['font']]; ?>;font-size:14px;" target="_blank"><?php echo sprintf(__('Weather in %s', 'tiempocom'), $args['location_label']); ?></a>
				                                <table class="fondo" width="100%" align="center">
				                                    <tbody>
				                                        <tr>
				                                        	<?php foreach($data->prediccion as $day) { ?>

				                                        		<?php if($args['days']) { ?>
				                                            		<td align="center" style="padding:0;margin:0;border:0;"> <b style="color:<?php echo $args['text']; ?>;font-size:13px;"><?php echo $weekday[$day->dia]; ?></b> </td>
				                                            	<?php } ?>

				                                            	<?php if($args['symbol']) { ?>
				                                            	<td style="padding:0;margin:0;border:0;width:35px;"> <img src="http://css13.tiempo.com/widget/css/galeria<?php echo $args['style']; ?>/pq<?php echo $day->simbolo; ?>.png"> </td>
				                                            	<?php } ?>

				                                            	<?php if($args['temperature']) { ?>
				                                            		<td style="padding:0;margin:0;border:0; width:35px;height35px;font-family:<?php echo $fonts[$args['font']]; ?>;font-size:15px;font-weight:bold;"> <span style="color:<?php echo $args['max']; ?>;"><?php echo $day->maxima; ?>°</span><br> <span style="color:<?php echo $args['min']; ?>;"><?php echo $day->minima; ?>°</span> </td>
				                                            	<?php } ?>
				                                            <?php } ?>
				                                        </tr>
				                                        <tr>
				                                            <td colspan="21" align="right" style="font-size:9px;padding-right:6px;text-align: right;">tiempo.com&nbsp;&nbsp; <u><a href="<?php echo $args['location_link']; ?>" title="<?php echo sprintf(__('Weather in %s', 'tiempocom'), $args['location_label']); ?>" target="_blank">+info</a></u> </td>
				                                        </tr>
				                                    </tbody>
				                                </table>
				                            </td>
				                        </tr>
				                    </tbody>
				                </table>
				            </td>
				        </tr>
				    </tbody>
				</table>
			</div>
			<?php
		}

		/**
		 * Prints Shortcode with Vertical format
		 *
		 * @param array $instance 	Shortcode data from Database
		 * @param array $data 		Retrieved data from API
		 */
		function tc_parse_vertical_template($args, $data) {

			$weekday = tc_static_get_weeks();
			$fonts = tc_static_get_fonts();

			?>
			<div class="tiempocom_widget">
				<table class="widget" id="webwid" style="max-width:65px;margin:0 auto;color:<?php echo $args['text']; ?>;width:65px;font-family:<?php echo $fonts[$args['font']]; ?>;">
				    <tbody>
				        <tr>
				            <td style="border:1px solid <?php echo $args['marquee']; ?>;background-color:<?php echo $args['background']; ?>;width:65px;">
				                <table class="fondo">
				                    <tbody>
				                    	<?php foreach($data->prediccion as $day) { ?>

				                    		<?php if($args['days']) { ?>
						                        <tr align="center" width="65px">
						                            <td style="font-size:12px;font-weight:bold;"><?php echo $weekday[$day->dia]; ?></td>
						                        </tr>
					                        <?php } ?>

					                        <?php if($args['symbol']) { ?>
						                        <tr align="center" width="65px">
						                            <td align="center"><img src="http://css13.tiempo.com/widget/css/galeria<?php echo $args['style']; ?>/pq<?php echo $day->simbolo; ?>.png"></td>
						                        </tr>
						                    <?php } ?>

					                        <?php if($args['temperature']) { ?>
						                        <tr align="center" width="65px">
						                            <td style="font-size:15px;font-weight:bold;">
						                                <table align="center" width="100%">
						                                    <tbody>
						                                        <tr align="center">
						                                            <td style="font-weight:bold;color:<?php echo $args['max']; ?>"><?php echo $day->maxima; ?>°</td>
						                                            <td style="font-weight:bold;color:<?php echo $args['min']; ?>"><?php echo $day->minima; ?>°</td>
						                                        </tr>
						                                    </tbody>
						                                </table>
						                            </td>
						                        </tr>
					                        <?php } ?>

				                        <?php } ?>
				                        <tr>
				                            <td align="center" style="font-size:9px;padding-bottom:5px;"><u><a href="<?php echo $args['location_link']; ?>" title="<?php echo sprintf(__('Weather in %s', 'tiempocom'), $args['location_label']); ?>" target="_blank">+info</a></u></td>
				                        </tr>
				                    </tbody>
				                </table>
				            </td>
				        </tr>
				    </tbody>
				</table>
			</div>
			<?php
		}
		
		/**
		 * Prints Shortcode with Wide format
		 *
		 * @param array $instance 	Shortcode data from Database
		 * @param array $data 		Retrieved data from API
		 */
		function tc_parse_wide_template($args, $data) {

			$weekday = tc_static_get_weeks();
			$fonts = tc_static_get_fonts();

			$wind_formats = tc_static_get_wind_formats();

			$width = 5 + ($args['time'] * 100);

			?>
			<div class="tiempocom_widget">
				<table id="webwid" class="webwid" style="max-width:<?php echo $width; ?>px;width:<?php echo $width; ?>px;padding:0;margin:0 auto;background-color:<?php echo $args['background']; ?>;color:<?php echo $args['text']; ?>;font-family:<?php echo $fonts[$args['font']]; ?>;">
				    <tbody>
				        <tr>
				            <td align="center" style="border:1px solid <?php echo $args['marquee']; ?>; background-color:<?php echo $args['background']; ?>;width:<?php echo $width; ?>px;">
				                <a id="wlink" href="<?php echo $args['location_link']; ?>" rel="nofollow" title="El Tiempo en <?php echo $args['location_label']; ?>"><?php echo sprintf(__('Weather in %s', 'tiempocom'), $args['location_label']); ?></a>
				                <table class="fondo" width="100%">
				                    <tbody>
				                        <tr>
				                        	<?php foreach($data->prediccion as $day) { ?>
					                            <td align="center" style="padding:0;margin:0;border:0;">
					                                <table>
					                                    <tbody>
					                                    	<?php if($args['days']) { ?>
						                                        <tr>
						                                            <td align="center" style="font-family:2;font-size:12px;color:<?php echo $args['text']; ?>;font-weight:bold;"><?php echo $weekday[$day->dia]; ?></td>
						                                        </tr>
					                                        <?php } ?>

					                                        <?php if($args['symbol']) { ?>
						                                        <tr>
						                                            <td align="center"><img src="http://css13.tiempo.com/widget/css/galeria<?php echo $args['style']; ?>/g<?php echo $day->simbolo; ?>.png"></td>
						                                        </tr>
					                                        <?php } ?>

					                                        <?php if($args['temperature']) { ?>
						                                        <tr>
						                                            <td align="center"><b style="color:<?php echo $args['max']; ?>;text-align:right;"><?php echo $day->maxima; ?>°&nbsp;</b><b style="color:<?php echo $args['min']; ?>;text-align:left;">&nbsp;<?php echo $day->minima; ?>°&nbsp;</b></td>
						                                        </tr>
					                                        <?php } ?>

					                                        <?php if($args['wind']) { ?>
						                                        <tr>
						                                            <td align="center">
						                                                <table width="100%">
						                                                    <tbody>
						                                                        <tr>
						                                                            <td style="text-align:center;"><img src="http://css13.tiempo.com/widget/css/galeria1/simbolo_viento/<?php echo $day->viento_simbolo; ?>.png"></td>
						                                                        </tr>
						                                                        <tr>
						                                                            <td colspan="2" align="center" style="color:<?php echo $args['text']; ?>;font-size:12px;"><?php echo $day->viento_media; ?> <?php echo $wind_formats[$args['wind_format']]; ?></td>
						                                                        </tr>
						                                                    </tbody>
						                                                </table>
						                                            </td>
						                                        </tr>
					                                        <?php } ?>

					                                        <?php if($args['relative_humidity']) { ?>
						                                        <tr>
						                                            <td style="font-size:12px;color:<?php echo $args['text']; ?>;"><b><?php _e('Humidity', 'tiempocom'); ?>:</b><?php echo $day->humedad; ?>%</td>
						                                        </tr>
					                                        <?php } ?>

					                                    </tbody>
					                                </table>
					                            </td>
				                            <?php } ?>
				                        </tr>
				                        <tr>
				                            <td colspan="7" align="right" style="font-size:9px;padding-right:5px; text-align: right;">tiempo.com &nbsp;&nbsp;<u><a href="<?php echo $args['location_link']; ?>" title="<?php echo sprintf(__('Weather in %s', 'tiempocom'), $args['location_label']); ?>" target="_blank">+info</a></u></td>
				                        </tr>
				                    </tbody>
				                </table>
				            </td>
				        </tr>
				    </tbody>
				</table>
			</div>
			<?php
		}

		/**
		 * Prints Shortcode with Complete format
		 *
		 * @param array $instance 	Shortcode data from Database
		 * @param array $data 		Retrieved data from API
		 */
		function tc_parse_complete_template($args, $data) {

			$weekday = tc_static_get_weeks();
			$fonts = tc_static_get_fonts();
			$wind_formats = tc_static_get_wind_formats();

			$rain_formats = tc_static_get_rain_formats();

			$width = 5 + ($args['time'] * 100);

			?>
			<div class="tiempocom_widget">
				<table class="widget" id="webwid" style="max-width:<?php echo $width; ?>px;margin:0 auto;padding:0;background-color:<?php echo $args['background']; ?>; color:<?php echo $args['text']; ?>;font-family:<?php echo $fonts[$args['font']]; ?>;">
				    <tbody>
				        <tr>
				            <td align="center" style="border:1px solid <?php echo $args['marquee']; ?>; background-color:<?php echo $args['background']; ?>;width:<?php echo $width; ?>px;">
				                <a id="wlink" class="wlink" rel="nofollow" rel="nofollow" href="<?php echo $args['location_link']; ?>" title="El Tiempo en <?php echo $args['location_label']; ?>"><?php echo sprintf(__('Weather in %s', 'tiempocom'), $args['location_label']); ?></a>
				                <table class="fondo" align="center" width="100%">
				                    <tbody>
				                        <tr>
				                        	<?php foreach($data->prediccion as $day) { ?>
					                            <td align="center" style="padding:0;margin:0;border:0;">
					                                <table style="margin-left:4px;">
					                                    <tbody>
					                                    	<?php if($args['days']) { ?>
					                                        <tr>
					                                            <td colspan="2" align="center" style="color:<?php echo $args['text']; ?>;font-size:13px;font-weight:bold;"><?php echo $weekday[$day->dia]; ?></td>
					                                        </tr>
					                                        <?php } ?>

					                                        <?php if($args['symbol']) { ?>
					                                        <tr>
					                                            <td colspan="2" align="center"><img src="http://css13.tiempo.com/widget/css/galeria<?php echo $args['style']; ?>/g<?php echo $day->simbolo; ?>.png"></td>
					                                        </tr>
					                                        <?php } ?>

					                                        <?php if($args['temperature']) { ?>
					                                        <tr>
					                                            <td align="center" style="font-size:18px;color:<?php echo $args['text']; ?>;"><b style="color:<?php echo $args['max']; ?>;"><?php echo $day->maxima; ?>°</b>&nbsp;<b style="color:<?php echo $args['min']; ?>;"><?php echo $day->minima; ?>°</b></td>
					                                        </tr>
					                                        <?php } ?>

					                                        <?php if($args['wind']) { ?>
					                                        <tr>
					                                            <td colspan="2" align="center"><img src="http://css13.tiempo.com/widget/css/galeria1/simbolo_viento/<?php echo $day->viento_simbolo; ?>.png"></td>
					                                        </tr>
					                                        <tr>
					                                            <td colspan="2" align="center" style="color:<?php echo $args['text']; ?>;font-size:12px;font-weight:normal;"><?php echo $day->viento_media; ?> <?php echo $wind_formats[$args['wind_format']]; ?></td>
					                                        </tr>
					                                        <?php } ?>

					                                        <?php if($args['rain']) { ?>
					                                        <tr>
					                                        	<td align="center" style="font-size:12px;color:<?php echo $args['text']; ?>;font-family:<?php echo $fonts[$args['font']]; ?>;"><b style="font-size:10px;"><?php _e('Rain', 'tiempocom'); ?>:</b><span style="font-weight:normal;font-size:11px;"><?php echo $day->lluvia; ?><?php echo $rain_formats[$args['rain_format']]; ?></span></td>
					                                        </tr>
					                                        <?php } ?>

					                                        <?php if($args['relative_humidity']) { ?>
					                                        <tr>
					                                            <td colspan="2" align="center" style="font-size:12px;color:<?php echo $args['text']; ?>;font-family:<?php echo $fonts[$args['font']]; ?>;"><b><?php _e('Humidity', 'tiempocom'); ?>:</b><?php echo $day->humedad; ?>%</td>
					                                        </tr>
					                                        <?php } ?>

					                                        <?php if($args['snow_height']) { ?>
					                                        <tr>
					                                            <td colspan="2" align="center" style="font-size:12px;color:<?php echo $args['text']; ?>;font-family:<?php echo $fonts[$args['font']]; ?>;"><b style="font-size:10px;"><?php _e('Snow', 'tiempocom'); ?>:</b><span style="font-weight:normal;font-size:12px;"><?php echo $day->cota_nieve; ?>m.</span></td>
					                                        </tr>
					                                        <?php } ?>

					                                        <?php if($args['pressure']) { ?>
					                                        <tr>
					                                            <td colspan="2" align="center" style="font-size:12px;color:<?php echo $args['text']; ?>;font-family:<?php echo $fonts[$args['font']]; ?>;"><b style="font-size:11px;"><?php _e('Pressure', 'tiempocom'); ?>:</b><?php echo $day->presion; ?>mb</td>
					                                        </tr>
					                                        <?php } ?>

					                                        <?php if($args['sunrise_sunset']) { ?>
					                                        <tr>
					                                        	<td colspan="2" align="center" style="font-size:12px;color:<?php echo $args['text']; ?>;font-family:<?php echo $fonts[$args['font']]; ?>;"><b><?php _e('Sunrise/Sunset', 'tiempocom'); ?></b><br><?php echo $day->sol_salida; ?>h/<?php echo $day->sol_puesta; ?>h</td>
					                                        </tr>
					                                        <?php } ?>

					                                        <?php if($args['moon_output_setting']) { ?>
					                                    	<tr>
					                                    		<td colspan="2" align="center"><span class="sprite_lunas lunas luna_pq<?php echo $day->luna_simbolo; ?>"></span><br><span style="font-size:12px;color:<?php echo $args['text']; ?>;font-family:<?php echo $fonts[$args['font']]; ?>;"><?php echo $day->luna_salida; ?>h/<?php echo $day->luna_puesta; ?>h</span></td>
					                                    	</tr>
					                                    	<?php } ?>
					                                    </tbody>
					                                </table>
					                            </td>
				                            <?php } ?>
				                        </tr>
				                        <tr>
				                            <td colspan="5" align="right" style="font-size:9px;padding-right:5px; text-align: right;">tiempo.com &nbsp;&nbsp;<u><a href="<?php echo $args['location_link']; ?>" title="<?php echo sprintf(__('Weather in %s', 'tiempocom'), $args['location_label']); ?>" target="_blank">+info</a></u></td>
				                        </tr>
				                    </tbody>
				                </table>
				            </td>
				        </tr>
				    </tbody>
				</table>
			</div>

			<?php
		}
?>