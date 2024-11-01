<?php
	require_once '../constant.php';
	
	$com = $_GET[OPTION_EMAIL_COMMENT];
	$create = $_GET[OPTION_EMAIL_CREATE];
	$edit =  $_GET[OPTION_EMAIL_EDIT];
	$done = $_GET[OPTION_EMAIL_DONE];
	$delete = $_GET[OPTION_EMAIL_DELETE];
	
	function displayTick( $value){
		if ('11' == $value)
			echo '<td><img src="'.IMG_DIRECTORY.'tick.png" alt="email" title="Got an email"/></td><td><img src="'.IMG_DIRECTORY.'tick.png" alt="email" title="Got an email"/></td>';
		else if('10' == $value )
			echo '<td><img src="'.IMG_DIRECTORY.'tick.png" alt="email" title="Got an email"/></td><td><img src="'.IMG_DIRECTORY.'cross.png" alt="email" title="Got an email"/></td>';
		else if('1' == $value )
			echo '<td><img src="'.IMG_DIRECTORY.'cross.png" alt="email" title="Got an email"/></td><td><img src="'.IMG_DIRECTORY.'tick.png" alt="email" title="Got an email"/></td>';
		else
			echo '<td><img src="'.IMG_DIRECTORY.'cross.png" alt="email" title="Got an email"/></td><td><img src="'.IMG_DIRECTORY.'cross.png" alt="email" title="Got an email"/></td>';
	}
?>

<table class="widefat">
	<tr>
		<th>Action</th>
		<th>Author</th>
		<th>Active User</th>
	</tr>
	<tr>
		<td>Create</td>
		<?php echo displayTick( $create );?>
	</tr>
	<tr>
		<td>Comment</td>
		<?php echo displayTick( $com );?>
	</tr>
	<tr>
		<td>Edit</td>
		<?php echo displayTick( $edit );?>
	</tr>
	<tr>
		<td>Done</td>
		<?php echo displayTick( $done);?>
	</tr>
<?php	/*
	<tr>
		<td>Delete</td>
		<?php echo displayTick( $delete );?>
	</tr> */
?>
</table>