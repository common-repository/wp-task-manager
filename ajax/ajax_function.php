<?php
	require_once '../../../../wp-config.php';
	require_once '../function.php';
	
	function get_all_active_task( $id=null ){
		global $wpdb;
//		SELECT task_id,task_name,task_dateTo 
		$table = $wpdb->prefix."task";
		if( isset( $id) ){
			$query = "SELECT * FROM $table 
						WHERE task_to = $id AND task_isDone = 0 
						ORDER BY task_dateTo;";
		}else{
			$query = "SELECT * FROM $table 
						WHERE task_isDone = 0 
						ORDER BY task_dateTo;";
		}
		return $wpdb->get_results( $query, ARRAY_A );
	}
	
	
	