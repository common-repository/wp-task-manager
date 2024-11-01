<?php
	require_once '../../../../wp-config.php';
	require_once '../constant.php';
	require_once '../function.php';
	
	global $wpdb;
	
	$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
	$user_id = filter_input(INPUT_GET, 'user_id', FILTER_SANITIZE_NUMBER_INT);
	$text = filter_input(INPUT_GET, 'text', FILTER_SANITIZE_SPECIAL_CHARS);
	
	$table_comment = $wpdb->prefix."task_comment";
	
	$query = "INSERT INTO $table_comment (task_com_task_id,task_com_text,task_com_author_id,task_com_date)
				VALUES('$id','$text','$user_id','".date('Y-n-j')."');";
	
	$wpdb->query($query);

	$table = $wpdb->prefix."task";	
	$query = "UPDATE $table SET task_nbCom = task_nbCom+1 WHERE task_id=$id ;";
	$wpdb->query( $query );
	
	$nameUser =  $wpdb->get_var('SELECT display_name FROM '.$wpdb->prefix.'users WHERE ID='.$user_id.' LIMIT 1;');
	$taskName = $wpdb->get_var('SELECT task_name FROM '.$table.' WHERE task_id='.$id.' LIMIT 1;');
	$title = "New Comment : $taskName";
	$message = "<h2>New Comment $taskName</h2><br/><strong>$nameUser </strong>make this comment about the task: <br/><i>$text</i>";
	wptm_email_notification( get_option(OPTION_EMAIL_CREATE),$title,$message,$user_id,$id);
	
	