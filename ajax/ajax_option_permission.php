<?php
	require_once '../constant.php';
	require_once '../function.php';

	function wptm_format_access_code_in_array($access){
		while( 6 > strlen($access) )
		{
			$access = '0'.$access;
		}	
		return str_split($access);		
	}	
	
//---------------------------------------------------------------------------------------
//Table with access and user level

	$access = $_GET[OPTION_LEVEL_VIEW];
	$create = $_GET[OPTION_LEVEL_CREATE];
	$edit =  $_GET[OPTION_LEVEL_EDIT];
	$validate = $_GET[OPTION_LEVEL_DONE];
	$delete = $_GET[OPTION_LEVEL_DELETE];

	//'Subscriber','Contributor','Author','Editor','Admin'
	$authorization = array(	
						array('See',wptm_format_access_code_in_array($access)),
						array('Create',wptm_format_access_code_in_array($create)),
						array('Edit',wptm_format_access_code_in_array($edit)),
						array('Validate',wptm_format_access_code_in_array($validate)),
						array('Delete',wptm_format_access_code_in_array($delete))
						);
?>		
	<table class='widefat'>
		<tr>
			<th></th>
			<th>Visitor</th>
			<th>Subscriber</th>
			<th>Contributor</th>
			<th>Author</th>
			<th>Editor</th>
			<th>Admin</th>
		</tr>
<?php 
			foreach($authorization as $user){
				echo '<tr>';
				foreach($user as $line){
					if( is_string($line) )
						echo "<th>$line</th>";
					else
					{
						foreach($line as $elem){
							if( 1 == $elem)
								echo '<td><img src="'.IMG_DIRECTORY.'tick.png" alt="access" title="Have Access"/></td>';
							else
								echo '<td><img src="'.IMG_DIRECTORY.'cross.png" alt="no access" title="Not Access"/></td>';
						}
					}
				}
				echo '</tr>';
			}
?>
	</table> 