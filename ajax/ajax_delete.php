<?php
	require_once '../../../../wp-config.php';
	require_once '../constant.php';
	require_once '../function.php';
	
	$id = filter_input(INPUT_GET, 'id',FILTER_SANITIZE_NUMBER_INT);
	
	wp_task_manager_task_delete( $id );
	
	wptm_delete_com( $id );
