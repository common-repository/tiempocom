<div class="wrap">

	<div id="icon-edit-tiempocom" class="icon32"><br/></div>

	<h2>Tiempo.com <?php echo sprintf('<a href="?page=%s&action=%s" class="add-new-h2">%s</a>', $_REQUEST['page'], 'new', __('Add shortcode', 'tiempocom')); ?></h2>

	<p><?php echo sprintf(__('The plugin %s is an application with which you can get the weather forecast for the locations you want daily.', 'tiempocom'), '<a href="http://www.tiempo.com" target="_blank">Tiempo.com</a>'); ?></p>

	<form id="shortcodes-filter" method="post">
        
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />

		<?php

			//Prepare Table of elements
			$tc_shortcodes_table = new TC_Shortcodes_Table();

			// Process requests, prepare items.
			$tc_shortcodes_table->prepare_items();

			// Get it visual.
			$tc_shortcodes_table->display();
		?>

	</form>

</div>