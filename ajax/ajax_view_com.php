<?php
	require_once '../../../../wp-config.php';
	require_once '../constant.php';
	require_once '../function.php';
	
	global $wpdb;
	
	$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
	
	$table_task = $wpdb->prefix."task";
	$table_comment = $wpdb->prefix."task_comment";
	$table_user = $wpdb->prefix."users";
	$format_lang = get_option(OPTION_DATE_FORMAT);
	
	$query = "SELECT T.task_id,T.task_name,T.task_desc,T.task_dateCreate,T.task_dateTo,U1.display_name as creator,T.task_isDone, U2.display_name as user
			FROM $table_task T 
			JOIN ".$wpdb->prefix."users U1 ON T.task_creator = U1.ID
			JOIN ".$wpdb->prefix."users U2 ON T.task_to = U2.ID 
			WHERE task_id=".$id.";";
	$list_task = $wpdb->get_results($query, ARRAY_A);

	if($list_task) {
			foreach ($list_task as $task){
				$creator = filter_var($task['creator'],FILTER_SANITIZE_STRING);
				$description = filter_var($task['task_desc'],FILTER_SANITIZE_STRING);
				$id = filter_var($task['task_id'],FILTER_SANITIZE_NUMBER_INT);
				$isDone = filter_var($task['task_isDone'],FILTER_SANITIZE_NUMBER_INT);
				$name = filter_var($task['task_name'],FILTER_SANITIZE_STRING);
				$user = filter_var($task['user'],FILTER_SANITIZE_STRING);
				$dateTo = filter_var($task['task_dateTo'],FILTER_SANITIZE_STRING);
				$dateCreate = filter_var($task['task_dateCreate'], FILTER_SANITIZE_STRING);
?>
	<hr/>
	<h3><?php echo $name;?></h3>
	<table class="widefat">
		<tr>
			<th>When</th>
			<th>Name</th>
			<th>To</th>
			<th>By</th>
			<th>The</th>
			<th>Status</th>
		</tr>
		<tr>
			<td><?php echo mysql2date( $format_lang, $dateTo ); ?></td>
			<td><?php echo $name; ?></td>
			<td><?php echo $user; ?></td>
			<td><?php echo $creator; ?></td>
			<td><?php echo mysql2date( $format_lang, $dateCreate ); ?></td>
			<td><?php echo wp_task_manager_display_status( $isDone ); ?></td>
		</tr>
<?php
			}
?>
	</table>
	<p><i><?php echo $description;?></i></p>
<?php 
	}
	//prepare query
	$query = "SELECT C.task_com_text as text, C.task_com_date as date, U.display_name as author
			FROM $table_comment C 
			JOIN $table_user U on C.task_com_author_id = U.ID
			WHERE task_com_task_id = $id 
			ORDER BY task_com_id;";
	$res = $wpdb->get_results($query, ARRAY_A);

	if($res) {
?>
	<h3>Comments</h3>
			<table class="widefat">
				<tr>
					<th></th>
					<th>Date</th>
					<th>Author</th>
					<th>Text</th>
				</tr>
<?php
		$nb_comment = 0;
		foreach( $res as $com){
			$nb_comment++;
			$date = filter_var($com['date'],FILTER_SANITIZE_STRING);
			$author = filter_var($com['author'],FILTER_SANITIZE_STRING);
			$text = filter_var($com['text'],FILTER_SANITIZE_STRING);
?>
			<tr>
				<td><?php echo $nb_comment;?></td>
				<td><?php echo mysql2date( $format_lang, $date );?></td>
				<td><?php echo $author;?></td>
				<td><?php echo $text;?></td>
			</tr>
<?php		
		}
?>
		</table>
<?php 
	}
?>
		<center>
			<form name="wptm_add_com" id="wptm_add_com">
				<b>Your Answer:</b><br/>
				<textarea  id="ctext" name="ctext" style="background-color:#B4CDCE;" rows="15" cols="50"></textarea><br/>
				<input type="button" value="Add" onclick="addCom('<?php echo $id; ?>')"/>&nbsp;&nbsp;
				<input type="reset" value="Clear"/>
			</form>
		</center>

		
		