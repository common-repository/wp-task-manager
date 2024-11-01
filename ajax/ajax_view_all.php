<?php
	require_once '../../../../wp-config.php';
	require_once '../constant.php';
	require_once '../function.php';

	global $wpdb;
	
	$view = filter_input(INPUT_GET, 'view', FILTER_SANITIZE_STRING);
	$rank = filter_input(INPUT_GET, 'rank', FILTER_SANITIZE_NUMBER_INT);
	$access_done = filter_input(INPUT_GET, 'access_done', FILTER_SANITIZE_NUMBER_INT);
	$access_delete = filter_input(INPUT_GET, 'access_delete', FILTER_SANITIZE_NUMBER_INT);
	$access_edit = filter_input(INPUT_GET, 'access_edit', FILTER_SANITIZE_NUMBER_INT);
	$url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL);
	
	$table = $wpdb->prefix."task";
		
	//prepare query
	$query = "SELECT T.task_id,T.task_name,T.task_desc,T.task_dateCreate,T.task_dateTo,U1.display_name as creator,T.task_isDone, U2.display_name as user, task_nbCom
				FROM $table T 
				JOIN ".$wpdb->prefix."users U1 ON T.task_creator = U1.ID
				JOIN ".$wpdb->prefix."users U2 ON T.task_to = U2.ID ";
			
	switch($view){
		default:
		case 'active':
			$query .= ' WHERE task_isDone = 0 ORDER BY task_dateTo;';
			break;
			
		case 'done':
			$query .= ' WHERE task_isDone = 1 ORDER BY task_dateTo;';
			break;
			
		case 'all':
			$query .= ' ORDER BY task_dateTo;';				
			break;
	}
	$list_task = $wpdb->get_results($query, ARRAY_A);
	$format_lang = get_option(OPTION_DATE_FORMAT);
	if($list_task) {
//		
?>
	<table class="widefat" id="wptm_table">
		<thead>
		<tr>
			<th>When</th>
			<th>Name</th>
			<th>To</th>
			<th>By</th>
			<th>The</th>
			<th>Status</th>
			<th>Coms</th>
<?php
			if( $access_done >= $rank)
				echo '<th>Action</th>';
			if( $access_edit >= $rank)
				echo '<th>Edit</th>';
			if( $access_delete >= $rank)
				echo '<th>Del</th>';
?>				
		</tr>
		</thead>
		<tbody>
<?php
			foreach ($list_task as $task){
				
				$creator = filter_var($task['creator'],FILTER_SANITIZE_STRING);
				$description = filter_var($task['task_desc'],FILTER_SANITIZE_STRING);
				$id = filter_var($task['task_id'],FILTER_SANITIZE_NUMBER_INT);
				$isDone = filter_var($task['task_isDone'],FILTER_SANITIZE_NUMBER_INT);
				$name = filter_var($task['task_name'],FILTER_SANITIZE_STRING);
				$user = filter_var($task['user'],FILTER_SANITIZE_STRING);
				$dateTo = filter_var($task['task_dateTo'],FILTER_SANITIZE_STRING);
				$dateCreate = filter_var($task['task_dateCreate'], FILTER_SANITIZE_STRING);
				$nb_comment = filter_var($task['task_nbCom'], FILTER_SANITIZE_NUMBER_INT);
?>
				<tr style="
<?php 				
				if( (strtotime($task['task_dateTo']) < time() ) && (0 == $isDone) )
					echo 'color:red;';
?>
				">
					<td class="tooltip" title="<?php echo $description;?>"><?php echo mysql2date( $format_lang, $dateTo ); ?></td>
					<td class="tooltip" title="<?php echo $description;?>"><?php echo $name; ?></td>
					<td class="tooltip" title="<?php echo $description;?>"><?php echo $user; ?></td>
					<td class="tooltip" title="<?php echo $description;?>"><?php echo $creator; ?></td>
					<td class="tooltip" title="<?php echo $description;?>"><?php echo mysql2date( $format_lang, $dateCreate ); ?></td>
					<td><?php echo wp_task_manager_display_status( $isDone ); ?></td>
					<td onclick="displayCom(<?php echo $id?>)"><a href="#"><b><?php echo $nb_comment;?></b><img src="<?php echo IMG_DIRECTORY;?>bubble.png"/></a></td>
<?php 
					if( $access_done >= $rank)
						echo '<td><a href="#">'.wp_task_manager_display_action( $id, $isDone, $view ).'</a></td>';
					if( $access_edit >= $rank)
						echo '<td><a href="#">'.wp_task_manager_display_edit_task( $id, $view, $url ).'</a></td>';
					if( $access_delete >= $rank)
						echo '<td><a href="#">'.wp_task_manager_display_delete_task( $id, $view ).'</a></td>';
?>						
				</tr>
<?php
			}
?>
				</tbody></table>
      			<script type="text/javascript">
					jQuery("#wptm_table").tablesorter();
					tooltip();
				</script>
<?php 
		}else{
			echo "<p>No task</p>";
		}
?>





