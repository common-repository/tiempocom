<?php
	
		/**
		 * Prints the Shortcode management form view
		 *
		 * @param string $action Form action
		 */
		function tc_print_shortcode_form($action = 'new') {

			if(isset($_GET['shortcode']) and $id = $_GET['shortcode']) {

				// Get shortcode data
				$instance = tc_db_get_shortcode($id);
			} else {

				// Get clean vars to create a new shortcode
				$instance = tc_db_get_processed_values();
			}

			// Extract values
			extract($instance);

			// API Instance
			$api = new TiempoCom_API();

			?>

			<input type="hidden" value="" class="tc_widget_interface" />

			<div class="wgt-block">

				<p>
					<label for="title"><?php _e( 'Title', 'tiempocom' ); ?>:</label> 
					<input class="widefat" id="title" name="title" type="text" value="<?php echo esc_attr( $title ); ?>" />
				</p>

				<?php $languages = tc_static_get_languages(); ?>

				<p>
					<label for="language"><?php _e( 'Language', 'tiempocom' ); ?>: </label> 
					<select class="widefat language_selector" id="language" name="language" >
						<?php foreach($languages as $code => $label) { ?>
							<option <?php selected( $language, $code ); ?> value="<?php echo $code; ?>"><?php echo $label; ?></option>
						<?php } ?>
					</select>
				</p>

				<h4><?php _e('1. Location', 'tiempocom'); ?></h4>

				<?php $continents = $api->get_list(1, null, $language); ?>

				<p>
					<label for="continent"><?php _e( 'Continent', 'tiempocom' ); ?>: </label> 
					<select class="widefat continent_selector" id="continent" name="continent" >
						<option value="0"><?php _e('Select a continent', 'tiempocom'); ?></option>
						<?php foreach($continents->listado as $item) { ?>
							<option <?php selected( $continent, $item->id ); ?> value="<?php echo $item->id; ?>"><?php echo $item->nombre; ?></option>
						<?php } ?>
					</select>
				</p>

				<p>
					<label for="country"><?php _e( 'Country', 'tiempocom' ); ?>: </label> 
					<select class="widefat country_selector" id="country" name="country" >
						<option value="0"><?php _e('Select a country', 'tiempocom'); ?></option>
						<?php
							if($continent) {
								$countries = $api->get_list(2, $continent, $language);
								foreach($countries->listado as $item) {
									?><option <?php selected( $country, $item->id ); ?> value="<?php echo $item->id; ?>"><?php echo $item->nombre; ?></option><?php
								}
							}
						?>
					</select>
				</p>

				<?php

					// Some extra logic to retriever correctly provinces / locations
					// depending on country.
					$province_type = 4;
					$disable_province = false;

					if($country) {

						$provinces = $api->get_list(3, $country, $language);

						foreach($provinces->listado as $item) {

							if($item->nivel == 4) $disable_province = true;
							if($item->nivel == 3) $province_type = 5;
						}
					} else {
						$provinces = array();
					}

				?>

				<p>
					<label for="province"><?php _e( 'Province', 'tiempocom' ); ?>: </label> 
					<select <?php if($disable_province) echo 'disabled="disabled"'; ?> class="widefat province_selector" id="province" name="province" >
						<option value="0"><?php _e('Select a province', 'tiempocom' ); ?></option>
						<?php
							if(!$disable_province and $provinces) {
								foreach($provinces->listado as $item) {
									?><option <?php selected( $province, $item->id ); ?> value="<?php echo $item->id; ?>"><?php echo $item->nombre; ?></option><?php
								}
							}
						?>
					</select>
				</p>

				<p>
					<label for="location"><?php _e( 'Location', 'tiempocom' ); ?>: </label> 
					<select class="widefat location_selector" id="location" name="location" >
						<option value="0"><?php _e('Select a location', 'tiempocom' ); ?></option>
						<?php
							if($province and $disable_province == false) {

								$locations = $api->get_list($province_type, $province, $language);
								foreach($locations->listado as $item) {
									?><option <?php selected( $location, $item->id ); ?> rel="<?php echo $item->url; ?>" value="<?php echo $item->id; ?>"><?php echo $item->nombre; ?></option><?php
								}

							} else if($country and $disable_province) {

								foreach($provinces->listado as $item) {
									?><option <?php selected( $location, $item->id ); ?> rel="<?php echo $item->url; ?>" value="<?php echo $item->id; ?>"><?php echo $item->nombre; ?></option><?php
								}

							}
						?>
					</select>

					<input type="hidden" value="<?php echo $location_label; ?>" class="location_label" name="location_label" />
					<input type="hidden" value="<?php echo $location_link; ?>" class="location_link" name="location_link" />
					<input type="hidden" value="<?php echo $province_type; ?>" class="province_type" name="province_type" />
				</p>

			</div>

			<div class="wgt-block">

				<h4><?php _e('2. Format', 'tiempocom'); ?></h4>

				<?php $day_range = tc_static_get_days(); ?>

				<p>
					<label for="time"><?php _e( 'Time', 'tiempocom' ); ?>: </label> 
					<select class="widefat time_selector" id="time" name="time" >
						<?php foreach($day_range as $day) { ?>
							<option <?php selected( $time, $day ); ?> value="<?php echo $day; ?>"><?php echo $day; ?> <?php echo ($day > 1) ? __('days', 'tiempocom') : __('day', 'tiempocom'); ?></option>
						<?php } ?>
					</select>
				</p>

				<?php $formats = tc_static_get_formats(); ?>

				<p>
					<label for="format"><?php _e( 'Format', 'tiempocom' ); ?>: </label> 
					<select class="widefat format_selector" id="format" name="format" >
						<?php foreach($formats as $key => $label) { ?>
							<option <?php selected( $format, $key ); ?> value="<?php echo $key; ?>"><?php echo $label; ?></option>
						<?php } ?>
					</select>
				</p>

				<h4><?php _e('3. Content', 'tiempocom'); ?></h4>

				<?php $meta_fields = tc_static_get_meta_fields(); ?>
				
				<p>
					<?php foreach($meta_fields as $key => $label) { ?>
						<input type="checkbox" class="meta_values meta_<?php echo $key; ?>" <?php checked( $$key, 1 ); ?> value="1" id="<?php echo $key; ?>" name="<?php echo $key; ?>" />
						<label for="<?php echo $key; ?>"><?php echo $label; ?></label><br/>
					<?php } ?>
				</p>

				<?php $temperature_formats = tc_static_get_temperature_formats(); ?>

				<p>
					<label for="temperature_format"><?php _e( 'Temperature format', 'tiempocom' ); ?>: </label> 
					<select class="widefat" id="temperature_format" name="temperature_format" >
						<?php foreach($temperature_formats as $key => $label) { ?>
							<option <?php selected( $temperature_format, $key ); ?> value="<?php echo $key; ?>"><?php echo $label; ?></option>
						<?php } ?>
					</select>
				</p>

				<?php $wind_formats = tc_static_get_wind_formats(); ?>

				<p>
					<label for="wind_format"><?php _e( 'Wind speed format', 'tiempocom' ); ?>: </label> 
					<select class="widefat" id="wind_format" name="wind_format" >
						<?php foreach($wind_formats as $key => $label) { ?>
							<option <?php selected( $wind_format, $key ); ?> value="<?php echo $key; ?>"><?php echo $label; ?></option>
						<?php } ?>
					</select>
				</p>

				<?php $rain_formats = tc_static_get_rain_formats(); ?>

				<p>
					<label for="rain_format"><?php _e( 'Rain format', 'tiempocom' ); ?>: </label> 
					<select class="widefat" id="rain_format" name="rain_format" >
						<?php foreach($rain_formats as $key => $label) { ?>
							<option <?php selected( $rain_format, $key ); ?> value="<?php echo $key; ?>"><?php echo $label; ?></option>
						<?php } ?>
					</select>
				</p>

			</div>

			<div class="wgt-block">

				<h4><?php _e('4. Style', 'tiempocom'); ?></h4>

				<?php $styles = tc_static_get_styles(); ?>

				<p>
					<input type="hidden" value="<?php echo $style; ?>" name="style" id="style" />
					<?php foreach($styles as $num) { ?>
						<a href="javascript:;" class="style_option <?php if($num == $style) echo 'active'; ?>" rel="style" data="<?php echo $num; ?>"><img src="http://css13.tiempo.com/widget/css/galeria<?php echo $num; ?>/pq8.png" /></a>
					<?php } ?>
				</p>

				<p class="style_color_list">

					<label for="marquee"><?php _e('Frame', 'tiempocom'); ?></label>
					<input type="text" data-default-color="#000000" class="color-field cf_marquee" value="<?php echo $marquee; ?>" value="1" id="marquee" name="marquee" /><br/>
					
					<label for="background"><?php _e('Background', 'tiempocom'); ?></label>
					<input type="text" data-default-color="#FFFFFF" class="color-field cf_background" value="<?php echo $background; ?>" value="1" id="background" name="background" /><br/>
						
					<label for="text"><?php _e('Text', 'tiempocom'); ?></label>
					<input type="text" data-default-color="#808080" class="color-field cf_text" value="<?php echo $text; ?>" value="1" id="text" name="text" /><br/>
						
					<label for="max"><?php _e('Max', 'tiempocom'); ?></label>
					<input type="text" data-default-color="#FF0000" class="color-field cf_max" value="<?php echo $max; ?>" value="1" id="max" name="max" /><br/>

					<label for="min"><?php _e('Min', 'tiempocom'); ?></label>
					<input type="text" data-default-color="#0000FF" class="color-field cf_min" value="<?php echo $min; ?>" value="1" id="min" name="min" /><br/>
				
				</p>

				<?php $fonts = tc_static_get_fonts(); ?>

				<p>
					<label for="font"><?php _e( 'Font', 'tiempocom' ); ?>: </label> 
					<select class="widefat font_selector" id="font" name="font" >
						<?php foreach($fonts as $key => $label) { ?>
							<option <?php selected( $font, $key ); ?> value="<?php echo $key; ?>"><?php echo $label; ?></option>
						<?php } ?>
					</select>
				</p>

				<div id="major-publishing-actions">

					<?php if($action == 'new') { ?>

						<div id="publishing-action">
							<input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="<?php _e('Save'); ?>">
						</div>
						<div class="clear"></div>

					<?php } else { ?>

						<div id="delete-action">
							<a class="submitdelete deletion" href=""><?php _e('Delete', 'tiempocom'); ?></a>
						</div>

						<div id="publishing-action">
							<input name="update" type="submit" class="button button-primary button-large" id="publish" accesskey="p" value="<?php _e('Update', 'tiempocom'); ?>">
						</div>
						<div class="clear"></div>
					
					<?php } ?>
				</div>

			</div>

			<?php 
		}

?>