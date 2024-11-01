<?php
	require_once '../../../../wp-config.php';
	require_once 'ajax_function.php';
	
	echo wp_task_manager_bouton_new();
	echo '<span style="float:right;">'.wp_task_manager_bouton_panel().'</span>';
	print_r($_GET);
	echo $_GET['id'];
	$ListTask = get_all_active_task( $_GET['id'] );	

	if( $ListTask ) 
	{
		echo '<p><table class="widefat"><tr><th>When</th><th>Name</th><th>Status</th></tr>';
		foreach( $ListTask as $task )
		{
			$dateTo = filter_var( $task['task_dateTo'], FILTER_SANITIZE_STRING );
			$name 	= filter_var( $task['task_name'], FILTER_SANITIZE_STRING );
			$id 	= filter_var( $task['task_id'], FILTER_SANITIZE_NUMBER_INT );
			echo '<tr style="';
			if(strtotime($dateTo) < time() )
				echo 'color:red;';
			echo '">';
?>
				<td><?php echo mysql2date( get_option(OPTION_DATE_FORMAT), $dateTo ); ?></td>
				<td><?php echo $name; ?></td>
				<td><?php echo wp_task_manager_display_action($id, 0, 'active'); ?></td>
<?php 
			echo '</tr>';
		}
		echo "</table></p>";
	}else{
		echo "<p>No task</>";
	}