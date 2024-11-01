	jQuery(document).ready(function($){

		jQuery('.widgets-sortables .tc_widget_interface, #shortcodes-filter .tc_widget_interface').each(function(index, value) {
			setup_tiempocom_widget(jQuery(value).parent());
		});

		jQuery(document).ajaxSuccess(function(e, xhr, settings) {
			if(settings.data && settings.data.search('action=save-widget') != -1 ) { 
				jQuery('.widgets-sortables .tc_widget_interface').each(function(index, value) {
					setup_tiempocom_widget(jQuery(value).parent());
				});
			}
		});

		jQuery('.copytoclipboard').click(function(){
			window.prompt("Copy to clipboard: Ctrl+C, Enter", jQuery(this).attr('rel'));
		});
	});

	function setup_tiempocom_widget(context) {

		context.submit( function(){
			if(context.find('.location_selector').val() == 0) {
				jQuery('#required_location').fadeIn();
				return false;
			}
		} );

		// Vars
		var picker, 
			font 				= 1, 
			format_selector 	= context.find('.format_selector'), 
			font_selector 		= context.find('.font_selector'), 
			continent_selector 	= context.find('.continent_selector'), 
	    	country_selector 	= context.find('.country_selector'), 
	    	province_selector 	= context.find('.province_selector'), 
	    	location_selector 	= context.find('.location_selector'), 
	    	location_label 		= context.find('.location_label'), 
	    	location_link 		= context.find('.location_link'),
	    	style_option		= context.find('.style_option'),
	    	language_selector	= context.find('.language_selector'),
	    	province_type    	= context.find('.province_type');

	    // Vars
	    var country_selector_text = country_selector.find('option[value="0"]').text(),
	    	province_selector_text = province_selector.find('option[value="0"]').text(),
	    	location_selector_text = location_selector.find('option[value="0"]').text();

	    // API Flags
	    var province_type_val       = province_type.val(),
	        province_selector_type 	= (province_type_val != "") ? province_type_val : 4;

		picker = context.find('.color-field').wpColorPicker({ width: 200 });

	    style_option.click(function(){
	    	var thiss = jQuery(this);
	    	thiss.parent().children().removeClass('active');
	    	thiss.addClass('active');
	    	context.find('#' + thiss.attr('rel') ).val( thiss.attr('data') );
	    	font = set_style_vars(thiss.attr('data'), picker);
	    	font_selector.val(font);
	    });

	    format_selector.change(function() {
	    	meta_tiempocom_filter(jQuery(this).val(), true, context);
	    }).each(function(index, value) {
	    	meta_tiempocom_filter(jQuery(value).val(), false, context);
	    });

	    continent_selector.change(function() {

	    	jQuery.post(ajaxurl, { action: 'api_tiempocom', type: 2, id: jQuery(this).val(), language: language_selector.val() }, function(response) {

				country_selector.html( jQuery("<option />").val(0).text(country_selector_text) );
				province_selector.html( jQuery("<option />").val(0).text(province_selector_text) ).prop('disabled', false);
				location_selector.html( jQuery("<option />").val(0).text(location_selector_text) );

				location_label.val('');
	    		location_link.val('');

				if(response.tipo > 0) {
					jQuery.each(response.listado, function(index, value) {

						country_selector.append(jQuery("<option />").val(value.id).text(value.nombre));

					});
				}
			});
	    });

	    country_selector.change(function() {

	    	jQuery.post(ajaxurl, {action: 'api_tiempocom', type: 3, id: jQuery(this).val(), language: language_selector.val() }, function(response) {
				
				province_selector.html( jQuery("<option />").val(0).text(province_selector_text) ).prop('disabled', false);
				location_selector.html( jQuery("<option />").val(0).text(location_selector_text) );
				location_label.val('');
	    		location_link.val('');

				if(response.tipo > 0) {

					jQuery.each(response.listado, function(index, value) {

						province_selector_type = 4;

						if (value.nivel == 4) {
							province_selector.prop('disabled', 'disabled');
							location_selector.append( jQuery("<option />").val(value.id).text(value.nombre).attr('rel', value.url) );

						} else {

							if(value.nivel == 3 ) {
								province_selector_type = 5;
							}
							province_selector.append(jQuery("<option />").val(value.id).text(value.nombre));
						}

					});
				}

			});
	    });

	    province_selector.change(function() {

	    	jQuery.post(ajaxurl, {action: 'api_tiempocom', type: province_selector_type, id: jQuery(this).val(), language: language_selector.val() }, function(response) {
				location_selector.html( jQuery("<option />").val(0).text(location_selector_text) );
				location_label.val('');
	    		location_link.val('');

				if(response.tipo > 0) {

					jQuery.each(response.listado, function(index, value) {
						location_selector.append( jQuery("<option />").val(value.id).text(value.nombre).attr('rel', value.url) );

					});

				}
			});
	    });

	    location_selector.change(function(){
	    	location_label.val(jQuery(this).find('option:selected').text());
	    	location_link.val(jQuery(this).find('option:selected').attr('rel'));
	    });
	}

	function meta_tiempocom_filter(format_val, after_save, context) {

		if(format_val == 2) {
	    	context.find('.time_selector').val(1).attr("disabled", true);
	    } else {
	    	context.find('.time_selector').removeAttr("disabled");
	    }

		var meta_values = context.find('.meta_values');

		if(after_save === true) {
			meta_values.attr('checked', false);
		}

	    meta_values.attr("disabled", true);

	    if(format_val == 1 || format_val == 2) {
	    	
	    	context.find('.meta_temperature').removeAttr("disabled");

	    	if(after_save === true) {
	    		context.find('.meta_days, .meta_symbol, .meta_temperature').attr('checked', true);
	    	}
	   	}
	    	
	    if(format_val == 3 || format_val == 4) {
	    	context.find('.meta_days, .meta_symbol, .meta_temperature').removeAttr("disabled");

	    	if(after_save === true) {
	    		context.find('.meta_days, .meta_symbol, .meta_temperature').attr('checked', true);
	    	}
	    }

    	if(format_val == 5) {
    		context.find('.meta_days, .meta_symbol, .meta_temperature, .meta_wind, .meta_relative_humidity').removeAttr("disabled");
    		
    		if(after_save === true) {
    			context.find('.meta_days, .meta_symbol, .meta_temperature, .meta_wind, .meta_relative_humidity').attr('checked', true);
    		}
    	}

    	if(format_val == 6) {
    		meta_values.removeAttr("disabled");

    		if(after_save === true) {
    			context.find('.meta_days, .meta_symbol, .meta_temperature, .meta_wind, .meta_relative_humidity, .meta_snow_height, .meta_pressure').attr('checked', true);
    		}
    	}

    	meta_values.each(function(index, obj){
    		var current = jQuery(obj);
    		if(current.is(':disabled')) {
    			current.next('label').addClass('disabled');
    		} else {
    			current.next('label').removeClass('disabled');
    		}
    	});
	}

	function set_style_vars(style, picker) {

		var marquee,
			background,
			text,
			max,
			min,
			font;

		if(style == 1 || style == 3 || style == 4) {

			marquee = '#3366FF';
			background = '#FFFFFF';
			text = '#808080';
			max = '#FE0000';
			min = '#3166FF';
			font = 2;

		} else if(style == 2) {

			marquee = '#3366FF';
			background = '#FFFFFF';
			text = '#808080';
			max = '#3366FF';
			min = '#99CD06';
			font = 2;

		} else  if(style == 5) {

			marquee = '#BDBDBD';
			background = '#FFFFFF';
			text = '#808080';
			max = '#FF6600';
			min = '#999999';
			font = 2;

		} else if(style == 6) {

			marquee = '#BDBDBD';
			background = '#FFFFFF';
			text = '#808080';
			max = '#656565';
			min = '#9B9B9B';
			font = 2;

		} else if(style == 7) {

			marquee = '#BDBDBD';
			background = '#FFFFFF';
			text = '#808080';
			max = '#F56B00';
			min = '#3166FF';
			font = 2;

		} else if(style == 8) {

			marquee = '#BDBDBD';
			background = '#FFFFFF';
			text = '#808080';
			max = '#BDBDBD';
			min = '#FD6864';
			font = 2;

		} else if(style == 9) {

			marquee = '#BDBDBD';
			background = '#FFFFFF';
			text = '#808080';
			max = '#3366FF';
			min = '#99CCFF';
			font = 2;

		} else if(style == 10) {

			marquee = '#BDBDBD';
			background = '#FFFFFF';
			text = '#656565';
			max = '#808000';
			min = '#99CC00';
			font = 2;

		} else if(style == 11) {

			marquee = '#009901';
			background = '#FFFFFF';
			text = '#656565';
			max = '#808000';
			min = '#99CC00';
			font = 2;

		} else if(style == 12) {

			marquee = '#BDBDBD';
			background = '#FFFFFF';
			text = '#656565';
			max = '#FE0000';
			min = '#68CBD0';
			font = 2;

		} else if(style == 13) {

			marquee = '#BDBDBD';
			background = '#FFFFFF';
			text = '#656565';
			max = '#FE0000';
			min = '#353200';
			font = 2;

		} else if(style == 14) {

			marquee = '#BDBDBD';
			background = '#FFFFFF';
			text = '#656565';
			max = '#F56B00';
			min = '#3166FF';
			font = 2;

		} else if(style == 15) {

			marquee = '#BDBDBD';
			background = '#FFFFFF';
			text = '#656565';
			max = '#FE0000';
			min = '#3166FF';
			font = 2;

		} else if(style == 16) {

			marquee = '#BDBDBD';
			background = '#FFFFFF';
			text = '#656565';
			max = '#F8A102';
			min = '#9B9B9B';
			font = 2;

		} else if(style == 17) {

			marquee = '#BDBDBD';
			background = '#FFFFFF';
			text = '#656565';
			max = '#FFC702';
			min = '#9B9B9B';
			font = 2;

		} else if(style == 18) {

			marquee = '#BDBDBD';
			background = '#FFFFFF';
			text = '#656565';
			max = '#FE0000';
			min = '#3166FF';
			font = 2;

		} else if(style == 19) {

			marquee = '#BDBDBD';
			background = '#FFFFFF';
			text = '#3C3C3D';
			max = '#fe2e2e';
			min = '#3276ab';
			font = 2;
		}

		picker.each(function(index, value){

			current = jQuery(value);

			if(current.hasClass('cf_marquee')) {

				color = marquee;

			} else if(current.hasClass('cf_background')) {

				color = background;

			} else if(current.hasClass('cf_text')) {

				color = text;

			} else if(current.hasClass('cf_max')) {

				color = max;

			} else if(current.hasClass('cf_min')) {

				color = min;

			} else {

				color = false;
			}

			if(color) {
				current.wpColorPicker('color', color);
			}
		});

		return font;

	}


