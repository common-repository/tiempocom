<?php

/**
 * Adds TiempoCom_Widget widget.
 */
class TiempoCom_Widget extends WP_Widget {

	var $cache_key;
	var $cache_prefix = 'widget_tiempocom_';
	var $default_cache_time = 3600;

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {

		parent::__construct(
			'TiempoCom_Widget', // Base ID
			'Tiempo.com Widget', // Name
			array( 'description' => __( 'Tiempo.com Widget', 'tiempocom' )) // Args
		);

		$this->generate_cache_key();
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {

		$this->generate_cache_key();

		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];

		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];

		// If there's location set
		if(isset($instance['location']) and $instance['location'] > 0) {
			
			// Show content passing thru cache
			$this->get_widget_cached_content($instance, $args);

		}

		echo $args['after_widget'];
	}

	/**
	 * Get widget content, passing thru cache
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function get_widget_cached_content($instance, $args) {

		global $lw_cache;

		// Get cache time
		$cache_time = (isset($instance['cache_time'])) ? $instance['cache_time']: 0;

		// Check if cache is valid
		if(!$output = $lw_cache->get($this->cache_key, $cache_time)) {

			// Start buffering
			$lw_cache->start();

			// Get the content
			$this->get_widget_content( $instance, $args );

			// Save and show the result
			$output = $lw_cache->collect();

			// Set cached content
			$lw_cache->set( $this->cache_key, $output, $cache_time );

		} else {

			// Just echo the cached content
			echo $output;
		}
	}

	/**
	 * Get widget content
	 *
	 * @param array $instance Saved values from database.
	 * @param array $args     Widget arguments.
	 */
	public function get_widget_content($instance, $args) {

		// Get data from API
		$api = new TiempoCom_API();

		$data = $api -> get_data($instance);

		// Update
		$this->update_widget_instance_cache_time( $args, $data );

		// Load template
		tc_parse_template($instance, $data);
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {

		// Clean up values
		$instance = tc_db_get_processed_values($instance);

		// Extract for use
		extract($instance);

		$api = new TiempoCom_API();

		?>
		<?php if($location == 0) { ?>
			<div class="widget_error"><?php _e('A location is required.', 'tiempocom'); ?></div>
		<?php } ?>

		<input type="hidden" value="" class="tc_widget_interface" />

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'tiempocom' ); ?>:</label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<?php $languages = tc_static_get_languages(); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'language' ); ?>"><?php _e( 'Language', 'tiempocom' ); ?>: </label> 
			<select class="widefat language_selector" id="<?php echo $this->get_field_id( 'language' ); ?>" name="<?php echo $this->get_field_name( 'language' ); ?>" >
				<?php foreach($languages as $code => $label) { ?>
					<option <?php selected( $language, $code ); ?> value="<?php echo $code; ?>"><?php echo $label; ?></option>
				<?php } ?>
			</select>
		</p>

		<h4>1. Location</h4>

		<?php $continents = $api->get_list(1, null, $language); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'continent' ); ?>"><?php _e( 'Continent', 'tiempocom' ); ?>: </label> 
			<select class="widefat continent_selector" id="<?php echo $this->get_field_id( 'continent' ); ?>" name="<?php echo $this->get_field_name( 'continent' ); ?>" >
				<option value="0"><?php _e('Select a continent', 'tiempocom'); ?></option>
				<?php foreach($continents->listado as $item) { ?>
					<option <?php selected( $continent, $item->id ); ?> value="<?php echo $item->id; ?>"><?php echo $item->nombre; ?></option>
				<?php } ?>
			</select>
		</p>


		<p>
			<label for="<?php echo $this->get_field_id( 'country' ); ?>"><?php _e( 'Country', 'tiempocom' ); ?>: </label> 
			<select class="widefat country_selector" id="<?php echo $this->get_field_id( 'country' ); ?>" name="<?php echo $this->get_field_name( 'country' ); ?>" >
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
			<label for="<?php echo $this->get_field_id( 'province' ); ?>"><?php _e( 'Province', 'tiempocom' ); ?>: </label> 
			<select <?php if($disable_province) echo 'disabled="disabled"'; ?> class="widefat province_selector" id="<?php echo $this->get_field_id( 'province' ); ?>" name="<?php echo $this->get_field_name( 'province' ); ?>" >
				<option value="0"><?php _e('Select a province', 'tiempocom'); ?></option>
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
			<label for="<?php echo $this->get_field_id( 'location' ); ?>"><?php _e( 'Location', 'tiempocom' ); ?>: </label> 
			<select class="widefat location_selector" id="<?php echo $this->get_field_id( 'location' ); ?>" name="<?php echo $this->get_field_name( 'location' ); ?>" >
				<option value="0"><?php _e('Select a location', 'tiempocom'); ?></option>
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

			<input type="hidden" value="<?php echo $location_label; ?>" class="location_label" name="<?php echo $this->get_field_name( 'location_label' ); ?>" />
			<input type="hidden" value="<?php echo $location_link; ?>" class="location_link" name="<?php echo $this->get_field_name( 'location_link' ); ?>" />
			<input type="hidden" value="<?php echo $province_type; ?>" class="province_type" name="<?php echo $this->get_field_name( 'province_type' ); ?>" />
		</p>

		<h4>2. Format</h4>

		<?php $day_range = tc_static_get_days(); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'time' ); ?>"><?php _e( 'Time', 'tiempocom' ); ?>: </label> 
			<select class="widefat time_selector" id="<?php echo $this->get_field_id( 'time' ); ?>" name="<?php echo $this->get_field_name( 'time' ); ?>" >
				<?php foreach($day_range as $day) { ?>
					<option <?php selected( $time, $day ); ?> value="<?php echo $day; ?>"><?php echo $day; ?> <?php echo ($day > 1) ? __('days') : __('day'); ?></option>
				<?php } ?>
			</select>
		</p>

		<?php $formats = tc_static_get_formats(); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'format' ); ?>"><?php _e( 'Format', 'tiempocom' ); ?>: </label> 
			<select class="widefat format_selector" id="<?php echo $this->get_field_id( 'format' ); ?>" name="<?php echo $this->get_field_name( 'format' ); ?>" >
				<?php foreach($formats as $key => $label) { ?>
					<option <?php selected( $format, $key ); ?> value="<?php echo $key; ?>"><?php echo $label; ?></option>
				<?php } ?>
			</select>
		</p>

		<h4>3. Content</h4>

		<?php $meta_fields = tc_static_get_meta_fields(); ?>
		
		<p>
			<?php foreach($meta_fields as $key => $label) { ?>
				<input type="checkbox" class="meta_values meta_<?php echo $key; ?>" <?php checked( $$key, 1 ); ?> value="1" id="<?php echo $this->get_field_id( $key ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" />
				<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $label; ?></label><br/>
			<?php } ?>
		</p>

		<?php $temperature_formats = tc_static_get_temperature_formats(); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'temperature_format' ); ?>"><?php _e( 'Temperature format', 'tiempocom' ); ?>: </label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'temperature_format' ); ?>" name="<?php echo $this->get_field_name( 'temperature_format' ); ?>" >
				<?php foreach($temperature_formats as $key => $label) { ?>
					<option <?php selected( $temperature_format, $key ); ?> value="<?php echo $key; ?>"><?php echo $label; ?></option>
				<?php } ?>
			</select>
		</p>

		<?php $wind_formats = tc_static_get_wind_formats(); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'wind_format' ); ?>"><?php _e( 'Wind speed format', 'tiempocom' ); ?>: </label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'wind_format' ); ?>" name="<?php echo $this->get_field_name( 'wind_format' ); ?>" >
				<?php foreach($wind_formats as $key => $label) { ?>
					<option <?php selected( $wind_format, $key ); ?> value="<?php echo $key; ?>"><?php echo $label; ?></option>
				<?php } ?>
			</select>
		</p>

		<?php $rain_formats = tc_static_get_rain_formats(); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'rain_format' ); ?>"><?php _e( 'Rain format', 'tiempocom' ); ?>: </label> 
			<select class="widefat" id="<?php echo $this->get_field_id( 'rain_format' ); ?>" name="<?php echo $this->get_field_name( 'rain_format' ); ?>" >
				<?php foreach($rain_formats as $key => $label) { ?>
					<option <?php selected( $rain_format, $key ); ?> value="<?php echo $key; ?>"><?php echo $label; ?></option>
				<?php } ?>
			</select>
		</p>

		<h4>4. Style</h4>

		<?php $styles = tc_static_get_styles(); ?>

		<p>
			<input type="hidden" value="<?php echo $style; ?>" name="<?php echo $this->get_field_name( 'style' ); ?>" id="<?php echo $this->get_field_id( 'style' ); ?>" />
			<?php foreach($styles as $num) { ?>
				<a href="javascript:;" class="style_option <?php if($num == $style) echo 'active'; ?>" rel="<?php echo $this->get_field_id( 'style' ); ?>" data="<?php echo $num; ?>"><img src="http://css13.tiempo.com/widget/css/galeria<?php echo $num; ?>/pq8.png" /></a>
			<?php } ?>
		</p>

		<p class="style_color_list">

			<label for="<?php echo $this->get_field_id( 'marquee' ); ?>"><?php _e('Frame', 'tiempocom'); ?></label>
			<input type="text" data-default-color="#000000" class="color-field cf_marquee" value="<?php echo $marquee; ?>" value="1" id="<?php echo $this->get_field_id( 'marquee' ); ?>" name="<?php echo $this->get_field_name( 'marquee' ); ?>" /><br/>
			
			<label for="<?php echo $this->get_field_id( 'background' ); ?>"><?php _e('Background', 'tiempocom'); ?></label>
			<input type="text" data-default-color="#FFFFFF" class="color-field cf_background" value="<?php echo $background; ?>" value="1" id="<?php echo $this->get_field_id( 'background' ); ?>" name="<?php echo $this->get_field_name( 'background' ); ?>" /><br/>
				
			<label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e('Text', 'tiempocom'); ?></label>
			<input type="text" data-default-color="#808080" class="color-field cf_text" value="<?php echo $text; ?>" value="1" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" /><br/>
				
			<label for="<?php echo $this->get_field_id( 'max' ); ?>"><?php _e('Max', 'tiempocom'); ?></label>
			<input type="text" data-default-color="#FF0000" class="color-field cf_max" value="<?php echo $max; ?>" value="1" id="<?php echo $this->get_field_id( 'max' ); ?>" name="<?php echo $this->get_field_name( 'max' ); ?>" /><br/>

			<label for="<?php echo $this->get_field_id( 'min' ); ?>"><?php _e('Min', 'tiempocom'); ?></label>
			<input type="text" data-default-color="#0000FF" class="color-field cf_min" value="<?php echo $min; ?>" value="1" id="<?php echo $this->get_field_id( 'min' ); ?>" name="<?php echo $this->get_field_name( 'min' ); ?>" /><br/>
		
		</p>

		<?php $fonts = tc_static_get_fonts(); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'font' ); ?>"><?php _e( 'Font', 'tiempocom' ); ?>: </label> 
			<select class="widefat font_selector" id="<?php echo $this->get_field_id( 'font' ); ?>" name="<?php echo $this->get_field_name( 'font' ); ?>" >
				<?php foreach($fonts as $key => $label) { ?>
					<option <?php selected( $font, $key ); ?> value="<?php echo $key; ?>"><?php echo $label; ?></option>
				<?php } ?>
			</select>
		</p>

		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {

		// Regenerate cache key
		$this->generate_cache_key();
		
		// Clean up values
		$instance = tc_db_get_processed_values($new_instance);

		// Make a first call to get expiry value
		$api = new TiempoCom_API();
		$data = $api -> get_data($instance);
		$instance['cache_time'] = $data->expiracion;

		// Flush cache
		global $lw_cache;
		$lw_cache->remove($this->cache_key);

		return $instance;
	}

	/**
	 * Set cache_key for the current widget
	 */
	function generate_cache_key() {
		$this->cache_key = $this->cache_prefix.$this->id;
	}

	/**
	 * Update the cache_time value from a widget
	 *
	 * @param array $args     Widget arguments.
	 * @param array $data 	  Saved values from API request
	 */
	function update_widget_instance_cache_time($args, $data) {

		// Retrieve the widget identifier
		$options = get_option('widget_tiempocom_widget');
		$id = end(explode('-', $args['widget_id']));

		// Set cache_time value
		$options[$id]['cache_time'] = $data->expiracion;

		// Update option
		update_option('widget_tiempocom_widget', $options);
	}

} // class TiempoCom_Widget

?>