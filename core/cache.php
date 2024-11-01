<?php

/**
* Class LW_Cache
*/
class LW_Cache {

	var $cache_comment;
	var $cache_dir;
	var $file_extension = '.html';
	var $cache_time = 3600;
	var $error_message = 'Please, set write permissions to <strong>%s</strong> directory to enable cache.';
	var $is_writable = false;
	
	function __construct($args = array())
	{
		if(isset($args['cache_dir'])) {
			$this->cache_dir = $args['cache_dir'];
		} else {
			$this->cache_dir = plugin_dir_path( __FILE__ ) . '../cache/';
		}

		if(isset($args['cache_time'])) {
			$this->cache_time = $args['cache_time'];
		}

		if(isset($args['cache_comment'])) {
			$this->cache_comment = sprintf(
				$args['cache_comment'], 
				date("d-m-Y | H:i"), 
				date("d-m-Y | H:i", time() + $this->cache_time) 
			);
		}

		if(isset($args['error_message'])) {
			$this->error_message = $args['error_message'];
		}

		if(false === is_writable($this->cache_dir)) {
			if(@chmod($this->cache_dir, 0755)) {
				$this->is_writable = true;
			}
		} else {
			$this->is_writable = true;
		}

		if(false === $this->is_writable) {
			add_action('admin_notices', array($this, 'add_writable_notice'));
		}
	}

	function add_writable_notice() {
		?>
			<div class="error"><p>
				<?php echo sprintf($this->error_message, $this->cache_dir); ?>
			</p></div>
		<?php
	}

	function get_key_name($key, $md5 = false) {

		if($md5) {
			return md5($key) . $this->file_extension;
		}
    	return $key . $this->file_extension;
    }

    function get_key_path($key) {

    	$file_name = $this->get_key_name($key);

    	return $this->cache_dir . $file_name;
    }

    function add_comment($content) {

		if($this->cache_comment) {

			$comment .= $this->cache_comment;

			return $comment;
		}
	}

	function start() {
		ob_start();
	}

	function collect($flush = true) {
		$output = ob_get_contents();
		if($flush) ob_end_flush();
		return $output;
	}

	// File bunch

	function get($key, $cache_time = null) {

		if($cache_time == null) {
			$cache_time = $this->cache_time;
		}

		return $this->read_cache($key, $cache_time);
	}

	function set($key, $content, $cache_time = null) {
		
		if($cache_time == null) {
			$cache_time = $this->cache_time;
		}

		return $this->write_cache($key, $content);
	}

	function check($key, $cache_time = null) {

		if($cache_time == null) {
			$cache_time = $this->cache_time;
		}

		if($cache_time == 0) {
			return false;
		}

		$cache_file = $this->get_key_path($key);

		//$cache_math = time() - $cache_time;
		//if( file_exists($cache_file) and ($cache_time < filemtime($cache_file)) ) {

		if( file_exists($cache_file) and ( time() < $cache_time ) ) {
            return true;
        }
        return false;
	}

	function remove($key) {

		$file = $this->get_key_path($key);
		if( is_file($file) ) {
			@unlink($file);
		}
	}

	function clear() {
		$dir = $this->cache_dir;
		$files = scandir($dir);
		foreach($files as $file) {
			if(is_file($file)) {
				@unlink($file);
			}
		}
	}	

	// Drivers

    function write_cache($key, $content) {

    	if(false === $this->is_writable) return false;

    	$cache_file = $this->get_key_path($key);

    	$file = fopen($cache_file, 'w'); 
        $bytes = fwrite($file, $content);
        fclose($file);

        return $bytes;
    }

    function read_cache( $key, $cache_time ) {

    	if ($this->check( $key, $cache_time )) {

    		$cache_file = $this->get_key_path($key);

            return file_get_contents($cache_file);
        }

        return false;
        
    }
}

?>