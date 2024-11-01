<?php
	require_once '../../../../wp-config.php';
	require_once '../constant.php';
	require_once '../function.php';
	global $wpdb;
		
	$taskID = 	filter_input(INPUT_GET, 'id',	FILTER_SANITIZE_NUMBER_INT);
	$done = 	filter_input(INPUT_GET, 'done',	FILTER_SANITIZE_NUMBER_INT);
	$userID = 	filter_input(INPUT_GET, 'user', FILTER_SANITIZE_NUMBER_INT);
	
	$table = $wpdb->prefix.'task';
	$nameUser =  $wpdb->get_var('SELECT display_name FROM '.$wpdb->prefix.'users WHERE ID='.$userID.' LIMIT 1;');
	$taskName = $wpdb->get_var('SELECT task_name FROM '.$table.' WHERE task_id='.$taskID.' LIMIT 1;');		
	
	if( 0 == $done ){
		wp_task_manager_task_is_done( $taskID );
		$title = "Task Accomplish : $taskName";
		$message = "<h2>Task Accomplish $taskName</h2><br/><strong>$nameUser </strong> mark the task as done. </i>";
	}
	else if( 1 == $done ){
		wp_task_manager_task_un_done( $taskID );
		$title = "Task Undone : $taskName";
		$message = "<h2>Task Done $taskName</h2><br/><strong>$nameUser </strong>mark this task as undone. Go back to work !";
	}
	
	wptm_email_notification( get_option(OPTION_EMAIL_CREATE),$title,$message,$userID,$taskID);