<?php
/*
Plugin Name: WP-Task-Manager
Plugin URI: http://thomas.lepetitmonde.net/en/index.php/projects/wordpress-task-manager
Description: Integrate in Wordpress, a task manager system. V2 IS COMING SOON !<a href="options-general.php?page=wp_task_manager_page_option">Options</a> | <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8475218">Donate</a> | <a href="http://thomas.lepetitmonde.net/en/projects/wordpress-task-manager" >Support</a> |  <a href="http://www.amazon.fr/gp/registry/registry.html/ref=wem-si-html_viewall?id=3CBHAPX24HDQ4" target="_blank" title="Amazon Wish List">Amazon Wishlist</a>
Author: Thomas Genin
Author URI: http://thomas.lepetitmonde.net/
Version: 1.62
*/
/** Copyright 2009  Thomas GENIN  (email : xt6@free.fr)
 
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
 
    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
require_once 'constant.php';
require_once 'function.php';

$task_plugIn_base_url='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?page='.plugin_basename (__FILE__);

//---------------------------------------------------------------------------
register_activation_hook(__FILE__, 'wp_task_manager_install');
register_deactivation_hook(__FILE__, 'wp_task_manager_uninstall');
//---------------------------------------------------------------------------
add_action('admin_menu', 'wp_task_manager_create_menu');
add_action('wp_dashboard_setup', 'wp_task_manager_init_dashboard_widget');
//---------------------------------------------------------------------------	

	/**
	 * Call at the installation of the plugin : create the database table, add the oprion field with default value
	 * @return 
	 * @author Thomas GENIN <xt6@free.fr>
	 */
	function wp_task_manager_install()
	{
		global $wpdb;
		
	    $table = $wpdb->prefix."task";
	    $structure = "CREATE TABLE $table (
			`task_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`task_name` VARCHAR( 100 ) NOT NULL ,
			`task_desc` TEXT NOT NULL ,
			`task_creator` INT UNSIGNED NOT NULL ,
			`task_to` INT NOT NULL ,
			`task_dateCreate` DATE NOT NULL ,
			`task_dateTo` DATE NOT NULL ,
			`task_isDone` TINYINT NOT NULL ,
			`task_nbCom` int(10) unsigned NOT NULL default '0'
			) ENGINE = MYISAM";
	    $wpdb->query( $structure );
	    
	    $table = $wpdb->prefix."task_comment";
	    $structure = "CREATE TABLE `$table` (
			  `task_com_id` int(10) unsigned NOT NULL auto_increment,
			  `task_com_task_id` int(11) NOT NULL,
			  `task_com_text` text NOT NULL,
			  `task_com_author_id` int(11) NOT NULL default '0',
			  `task_com_date` date NOT NULL,
			  PRIMARY KEY  (`task_com_id`)
			) ENGINE=MyISAM ";
	    $wpdb->query( $structure );
	    	    
		//@todo test if the creation of the database don't bug if there is an okd database.	    
	    add_option( OPTION_PLUGIN_IS_ACTIVATE, 1 );
	    add_option( OPTION_DATE_FORMAT, 'd/m/Y' );
	    add_option( OPTION_DELETE_DB, '1');
	    
	    add_option( OPTION_LEVEL_VIEW, ACCESS_TO_ADMIN );
	    add_option( OPTION_LEVEL_CREATE, ACCESS_TO_ADMIN );
	    add_option( OPTION_LEVEL_EDIT, ACCESS_TO_ADMIN );
	    add_option( OPTION_LEVEL_DONE, ACCESS_TO_ADMIN );
	    add_option( OPTION_LEVEL_DELETE, ACCESS_TO_ADMIN );
	    
	    add_option( OPTION_EMAIL_CREATE,	CREATOR_USER );
		add_option( OPTION_EMAIL_EDIT,		CREATOR_USER );
		add_option( OPTION_EMAIL_COMMENT, 	CREATOR_USER );
		add_option( OPTION_EMAIL_DELETE, 	CREATOR_USER );
		add_option( OPTION_EMAIL_DONE, 		CREATOR_USER );
	    
	}
	
	/**
	 * Destroy everything create by the plugin 
	 * @return 
	 * @author Thomas GENIN <xt6@free.fr>
	 */
	function wp_task_manager_uninstall()
	{
		global $wpdb;
		$table = $wpdb->prefix."task";
		$table2= $wpdb->prefix."task_comment";
		//Delete the table
		/*
		if( get_option( OPTION_DELETE_DB ) ==  1 )
		{
			$query = "DROP TABLE $table;";
			$wpdb->query( $query );
			$query = "DROP TABLE $table2;";
			$wpdb->query( $query );
		}*/
		//option
	    delete_option( OPTION_PLUGIN_IS_ACTIVATE );
	    delete_option( OPTION_DATE_FORMAT);	
	    delete_option(OPTION_DELETE_DB);	
	    
	    delete_option( OPTION_LEVEL_VIEW );
	    delete_option( OPTION_LEVEL_CREATE);
	    delete_option( OPTION_LEVEL_EDIT);
	    delete_option( OPTION_LEVEL_DONE);
	    delete_option( OPTION_LEVEL_DELETE);

	    delete_option( OPTION_EMAIL_CREATE );
		delete_option( OPTION_EMAIL_EDIT );
		delete_option( OPTION_EMAIL_COMMENT );
		delete_option( OPTION_EMAIL_DELETE );
		delete_option( OPTION_EMAIL_DONE );	    
	}

	/**
	 * Create the menu ...
	 * @return unknown_type
	 * @author Thomas GENIN <xt6@free.fr>
	 */
	function wp_task_manager_create_menu(){
		global $current_user,$rank,$access_view,$access_create,$access_delete,$access_done,$access_edit;
		//As the function is call every times you move in the administration of wordpress, i use it to get access
		$rank = get_user_rank( $current_user->ID );
		$access_view = intval( get_option(OPTION_LEVEL_VIEW) );
		$access_create = intval( get_option(OPTION_LEVEL_CREATE) );
		$access_delete = intval( get_option(OPTION_LEVEL_DELETE) );
		$access_done = intval( get_option(OPTION_LEVEL_DONE) );
		$access_edit = intval( get_option(OPTION_LEVEL_EDIT) );
		
		if( ACCESS_TO_ADMIN == $rank)
			add_submenu_page('options-general.php', 'WP Task Manager', 'WP Task Manager', 'manage_options', 'wp_task_manager_page_option', 'wp_task_manager_page_option');

		if( ('1' == get_option(OPTION_PLUGIN_IS_ACTIVATE) ) && ($access_view >= $rank ) ){
			add_menu_page( 'Task Manager', 'Task Manager', 1, __FILE__, 'wp_task_manager_page_dispatcher', IMG_DIRECTORY.'ico16.png');
	    	if( $access_create >= $rank ){
				add_submenu_page( __FILE__, 'New Task', 'New task', 1, __FILE__.'&amp;task=new', 'wp_task_manager_page_dispatcher' );
	    	}
		}
	}

	//add Dashboard Widget via ajax_function wp_add_dashboard_widget()
	function wp_task_manager_init_dashboard_widget() {
		global $access_view, $rank;
		if( ('1' == get_option(OPTION_PLUGIN_IS_ACTIVATE) ) && ($access_view >= $rank )  )
			wp_add_dashboard_widget( 'wp_dashboard_my_task_manager', __( 'My Task' ), 'wp_task_manager_dashboard_widget' );
	}
	
	
	function wp_task_manager_page_dispatcher(){
		if (isset($_POST['task'])) 
			$_GET['task']=$_POST['task'];

		$task = filter_input(INPUT_GET,'task',FILTER_SANITIZE_STRING);	
		switch ($task) {
			default:
	   		case 'all':
	   			wp_task_manager_view_all_task();
	    		break;
	    		
	   		case 'new':
				wp_task_manager_page_new_task();
				break;
			
	   		case 'option':
	   			wp_task_manager_page_option();
	   			break;
	    }
	} 

	function wp_task_manager_page_option(){
		
?>
<div class="wrap">
	<h2><?php _e("Wordpress Task Manager Option")?></h2>

		<h1>Donation EURO</h1>
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
		<input type="hidden" name="cmd" value="_s-xclick">
		<input type="hidden" name="hosted_button_id" value="8475055">
		<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
		<img alt="" border="0" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
		</form>
		<h1>Dontation US Dollars</h1>
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="8475218">
			<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypal.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
			</form>
		<br/>	
	<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
	<h1>Options</h1>
	<h3>General</h3>
	<table class="form-table">
			<tr valign="top">
				<th scope="row">Date Format</th>
				<td><select name="<?php echo OPTION_DATE_FORMAT?>">
<?php
	$date_format = get_option(OPTION_DATE_FORMAT);
?>
					<optgroup label="Day First">
						<option value="d/m/Y" <?php if('d/m/Y' == $date_format) echo 'selected="selected"';?>>25/12/2009</option>
						<option value="d-m-Y" <?php if('d-m-Y' == $date_format) echo 'selected="selected"';?>>25-12-2009</option>
						<option value="j-n-y" <?php if('j-n-y' == $date_format) echo 'selected="selected"';?>>1-1-09 (1 January 2009)</option> 
					</optgroup>
					<optgroup label="Month First">
						<option value="m/d/Y" <?php if('m/d/Y' == $date_format) echo 'selected="selected"';?>>12/25/2009</option>
						<option value="m-d-Y" <?php if('m-d-Y' == $date_format) echo 'selected="selected"';?>>12-25-2009</option>
						<option value="n-j-y" <?php if('n-j-y' == $date_format) echo 'selected="selected"';?>>1-1-09 (1 January 2009)</option>
					</optgroup>
				</select>
				</td>
			</tr>		
	</table>	
	<h3>Access Level</h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">View Level Access</th>
			<td><?php echo wptm_make_select_option_access(OPTION_LEVEL_VIEW); ?></td>
		</tr>		

		<tr valign="top">
			<th scope="row">Create Level Access</th>
			<td><?php echo wptm_make_select_option_access(OPTION_LEVEL_CREATE); ?></td>
		</tr>

		<tr valign="top">
			<th scope="row">Edit Level Access</th>
			<td><?php echo wptm_make_select_option_access(OPTION_LEVEL_EDIT); ?></td>
		</tr>
	
		<tr valign="top">
			<th scope="row">Validation Level Access</th>
			<td><?php echo wptm_make_select_option_access(OPTION_LEVEL_DONE); ?></td>
		</tr>		

		<tr valign="top">
			<th scope="row">Delete Level Access</th>
			<td><?php echo wptm_make_select_option_access(OPTION_LEVEL_DELETE); ?></td>
		</tr>			
	</table>
	<p id="table_permission"></p>
	<h3>Notification</h3>
	<p>Who will receive an email when the action is performed</p>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">Create</th>
			<td><?php echo wptm_get_select_email( OPTION_EMAIL_CREATE); ?></td>
		</tr>
		<tr valign="top">
			<th scope="row">Comment</th>
			<td><?php echo wptm_get_select_email( OPTION_EMAIL_COMMENT); ?></td>
		</tr>				
		<tr valign="top">
			<th scope="row">Edit</th>
			<td><?php echo wptm_get_select_email( OPTION_EMAIL_EDIT); ?></td>
		</tr>		
		<tr valign="top">
			<th scope="row">Done - Undone</th>
			<td><?php echo wptm_get_select_email( OPTION_EMAIL_DONE); ?></td>
		</tr><?php /*
		<tr valign="top">
			<th scope="row">Delete</th>
			<td><?php echo wptm_get_select_email( OPTION_EMAIL_DELETE); ?></td>
		</tr>		*/?>		
	</table>
	<p id="wptm_table_notification"></p>
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="<?php echo OPTION_DATE_FORMAT.','.OPTION_LEVEL_VIEW.','.OPTION_LEVEL_CREATE.','.OPTION_LEVEL_EDIT.','.OPTION_LEVEL_DONE.','.OPTION_LEVEL_DELETE; ?>" />
	
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>			
	</form>
	</div>
	<script type="text/javascript">
		jQuery("#table_permission").ready(function(){get_table_permission(); get_table_notification();});
		jQuery(".wptm_select_access").change(function(){get_table_permission();});
		jQuery(".wptm_select_notification").change(function(){get_table_notification();});
		function get_table_permission(){
		    var view = jQuery("#<?php echo OPTION_LEVEL_VIEW;?>").val();
		    var create = jQuery("#<?php echo OPTION_LEVEL_CREATE?>").val();
		    var edit = jQuery("#<?php echo OPTION_LEVEL_EDIT?>").val();
		    var done = jQuery("#<?php echo OPTION_LEVEL_DONE?>").val();
		    var delet = jQuery("#<?php echo OPTION_LEVEL_DELETE?>").val();
		    jQuery.get("../wp-content/plugins/wp-task-manager/ajax/ajax_option_permission.php",
				    						{<?php echo OPTION_LEVEL_VIEW;?>:view,
				    						<?php echo OPTION_LEVEL_CREATE;?>:create,
				    						<?php echo OPTION_LEVEL_EDIT;?>:edit,
				    						<?php echo OPTION_LEVEL_DONE;?>:done,
				    						<?php echo OPTION_LEVEL_DELETE;?>:delet},function(data)
				    {jQuery('#table_permission').html(data);}
		    );
		}

		function get_table_notification(){
		    var create = jQuery("#<?php echo OPTION_EMAIL_CREATE?>").val();
		    var edit = jQuery("#<?php echo OPTION_EMAIL_EDIT?>").val();
		    var done = jQuery("#<?php echo OPTION_EMAIL_DONE?>").val();
		    var delet = jQuery("#<?php echo OPTION_EMAIL_DELETE?>").val();
		    var com = jQuery("#<?php echo OPTION_EMAIL_COMMENT?>").val();
		    jQuery.get("../wp-content/plugins/wp-task-manager/ajax/ajax_option_notification.php",
				    						{<?php echo OPTION_EMAIL_CREATE;?>:create,
				    						<?php echo OPTION_EMAIL_EDIT;?>:edit,
				    						<?php echo OPTION_EMAIL_DONE;?>:done,
				    						<?php echo OPTION_EMAIL_DELETE;?>:delet,
						    				<?php echo OPTION_EMAIL_COMMENT;?>:com},function(data)
				    {jQuery('#wptm_table_notification').html(data);}
		    );
		}
	</script>	
<?php
	}
	
	//Content of Dashboard-Widget
	function wp_task_manager_dashboard_widget() {

		global $wpdb,$current_user,$task_plugIn_base_url,$access_create,$access_done,$rank;
		
		echo wp_task_manager_bouton_new();	
		echo '<span style="float:right;">'.wp_task_manager_bouton_panel().'</span>';
		
		$table = $wpdb->prefix."task";
		$query = "SELECT task_id,task_name,task_dateTo 
					FROM $table 
					WHERE task_to = $current_user->ID AND task_isDone = 0 
					ORDER BY task_dateTo;";
		$ListTask = $wpdb->get_results( $query, ARRAY_A );
	
		if( $ListTask ) 
		{
			echo '<p><table class="widefat"><tr><th>When</th><th>Name</th>';
			if( $access_done >= $rank )
				echo '<th>Status</th>';
			echo '</tr>';
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
<?php 
					if( $access_done >= $rank )
						echo '<td>'.wp_task_manager_display_action($id, 0, 'active').'</td>'; 
				echo '</tr>';
			}
			echo "</table></p>";
		}else{
			echo "<p>No task</>";
		}
	}

	
	function wp_task_manager_page_new_task(){
		global $task_plugIn_base_url,$current_user,$wpdb,$rank,$access_create,$access_edit; 

		$day 		= date( 'j');
		$month 		= date( 'n');
		$year 		= date( 'Y');
		
		
		if( ($access_create >= $rank) || ($access_edit >= $rank)){
			$msg = '';
			$go 	= filter_input( INPUT_POST, 'go', FILTER_SANITIZE_STRING );
			$action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING);
			
			if( isset( $go ) ){
				$error[0] = false;
				
				$day 		 = filter_input( INPUT_POST, 'day', FILTER_SANITIZE_NUMBER_INT );
				$description = filter_input( INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS );
				$month 		 = filter_input( INPUT_POST, 'month', FILTER_SANITIZE_NUMBER_INT );
				$name 		 = filter_input( INPUT_POST, 'name', FILTER_SANITIZE_STRING );
				$user 		 = filter_input( INPUT_POST, 'user', FILTER_SANITIZE_NUMBER_INT );
				$year 		 = filter_input( INPUT_POST, 'year', FILTER_SANITIZE_NUMBER_INT );
				
				if( empty( $name ) ){
					$error['name'] = true;
					$error[0] = true;
					$msg .= '<span style="color:red">The&nbsp;<b>Name</b>&nbsp;of the task must be set.</span><br/>';
				}
				if( empty( $description ) ){
					$error['description'] = true;
					$error[0]=true;
					$msg .= '<span style="color:red">The&nbsp;<b>Description</b>&nbsp;of the task must be set.</span><br/>';
				}
				if( $user == '###' ){
					$error['user'] = true;
					$error[0] = true;
					$msg .= '<span style="color:red">The&nbsp;<b>User</b>&nbsp;of the task must be set.</span><br/>';
				}
				if( $day == '###' ){
					$error['day'] = true;
					$error[0] = true;
					$msg .= '<span style="color:red">The&nbsp;<b>Day</b>&nbsp;of the task must be set.</span><br/>';
				}			
				if( $user == '###' ){
					$error['month'] = true;
					$error[0] = true;
					$msg .= '<span style="color:red">The&nbsp;<b>Month</b>&nbsp;of the task must be set.</span><br/>';
				}	
				if( !is_numeric($year) || strlen($year) != 4 ){
					$error['year'] = true;
					$error[0] = true;
					$msg .= '<span style="color:red">The&nbsp;<b>Year</b>&nbsp;of the task must be set correctly.</span><br/>';
				}
			
				//let's insert the task in the database
				if( ! $error[0] ){
					$table = $wpdb->prefix."task";
					
					//UPDATE Task
					if( isset($_POST['id'])){
						$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
						$wpdb->update($table, array('task_name' => $name,
														'task_desc' => $description,
														'task_creator' => $current_user->ID,
														'task_to' => $user,
														'task_dateCreate' => date('Y-n-j'),
														'task_dateTo' => "$year-$month-$day") , 
												array('task_id' => $id) );
						if(1){
							$nameUser =  $wpdb->get_var('SELECT display_name FROM '.$wpdb->prefix.'users WHERE ID='.$current_user->ID.' LIMIT 1;');
							$title = "Update Task : $name";
							$message = "<h2>Upate Task $name</h2><br/><strong>$nameUser </strong>update this task.<br/>The task should be done before ".mysql2date( get_option(OPTION_DATE_FORMAT), "$year-$month-$day" ).'<br/><br/>Here is the description:<br/><i>'.$description.'</i>';
							wptm_email_notification( get_option(OPTION_EMAIL_CREATE),$title,$message,$current_user->ID,$id);
							$msg .= '<span style="color:green; background-color:#9ECA98">Successfully updated task</span>';
						}else{
							$msg .= '<span style="color:red; background-color:#D16F6F">An error occur when the plugin update task</span>';	
						}
												
					}else{
						$wpdb->insert($table, array(	'task_name' => $name,
														'task_desc' => $description,
														'task_creator' => $current_user->ID,
														'task_to' => $user,
														'task_dateCreate' => date('Y-n-j'),
														'task_dateTo' => "$year-$month-$day",
														'task_isDone' => 0) );
						$id = $wpdb->get_var('SELECT LAST_INSERT_ID()');
						
						if(1){
							$nameUser =  $wpdb->get_var('SELECT display_name FROM '.$wpdb->prefix.'users WHERE ID='.$current_user->ID.' LIMIT 1;');
							$title = "New Task : $name";
							$message = "<h2>New Task $name</h2><br/><strong>$nameUser </strong>give you this task.<br/>The task should be done before ".mysql2date( get_option(OPTION_DATE_FORMAT), "$year-$month-$day" ).'<br/><br/>Here is the description:<br/><i>'.$description.'</i>';
							wptm_email_notification( get_option(OPTION_EMAIL_CREATE),$title,$message,$current_user->ID,$id);
							$msg .= '<span style="color:green; background-color:#9ECA98">Successfully created task</span>';
						}else{
							$msg .= '<span style="color:red; background-color:#D16F6F">An error occur when the plugin create task</span>';
						}
						
					}
					$msg .= '&nbsp;&nbsp;&nbsp;'.wp_task_manager_bouton_panel().'&nbsp;&nbsp;&nbsp;'.wp_task_manager_bouton_new().'<br/><hr/><br/>';
				}
			}	
			if( isset($action) && $action = 'edit'){
				$id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
				$table = $wpdb->prefix."task";
				$query = "SELECT task_id,task_name,task_desc,task_to,DAY(task_dateTo) as `day`,MONTH(task_dateTo) as `month`, YEAR(task_dateTo) as `year` FROM $table WHERE task_id = $id LIMIT 1;";
				$res = $wpdb->get_row($query);
	
				$day 		 = filter_var( $res->day,FILTER_SANITIZE_NUMBER_INT );
				$description = filter_var( $res->task_desc, FILTER_SANITIZE_STRING );
				$month 		 = filter_var( $res->month, FILTER_SANITIZE_NUMBER_INT );
				$name 		 = filter_var( $res->task_name, FILTER_SANITIZE_STRING );
				$user 		 = filter_var( $res->task_to, FILTER_SANITIZE_NUMBER_INT );
				$year 		 = filter_var( $res->year, FILTER_SANITIZE_NUMBER_INT );	
				$id 		 = filter_var( $res->task_id, FILTER_SANITIZE_NUMBER_INT );			
			}
			
			if( isset($id) )
				echo '<h1>Edit Task</h1>';
			else	
				echo '<h1>Add Task</h1>';
			?>	
			
			<br/>
			<div><?php echo $msg; ?></div>
			<script type="text/javascript" src="<?php echo JS_DIRECTORY;?>calendar.js"></script>
			<div>
				<form method="post" action="<?php echo $task_plugIn_base_url.'&amp;task=new' ?>">
					<table>
						<tr <?php if( isset($error['name']) ) echo 'style="background-color:#D41346;"'?>>
							<td>Name</td>
							<td><input type="text" name="name" size="58" value="<?php if( isset($name) ) echo $name;?>"/></td>
						</tr>
						<tr <?php if( isset($error['description']) ) echo 'style="background-color:#D41346;"'?>>
							<td>Description</td>
							<td><textarea name="description" rows="8" cols="52"><?php if( isset($description) ) echo $description;?></textarea></td>
						</tr>
						<tr <?php if( isset($error['user']) ) echo 'style="background-color:#D41346;"'?>>
							<td>Assign To</td>
							<td><?php if(isset($user)) echo wp_task_manager_get_select_all_contributor( $user ); else echo wp_task_manager_get_select_all_contributor( ); ?></td>
						</tr>
						<tr <?php if( isset($error['day']) || isset($error['month']) || isset($error['year']) ) echo 'style="background-color:#D41346;"'?>>
							<td>For</td>
							<td>Day: <input name="day" id="day" value="<?php echo $day;?>" size="2" type="text"> Month:<input name="month" id="month" value="<?php echo $month;?>" size="2" type="text"> Year: <input name="year" id="year" value="<?php echo $year;?>" size="4" type="text">
								<a href="#" onclick="cal.showCalendar('anchor9'); return false;" title="cal.showCalendar('anchor9'); return false;" name="anchor9" id="anchor9"><img src="<?php echo IMG_DIRECTORY?>calendar.png"/></a>
							</td>
						</tr>
					</table>
					<br/>
	<?php 
					if( isset( $id) )
						echo "<input type='hidden' name='id' value='$id'/>";
	?>
					<input type="submit" value="<?php if( isset($id) ) echo 'Update'; else echo 'Add';?>" name="go"/>&nbsp;&nbsp;&nbsp;
					<input type="reset" value="Clear" />&nbsp;&nbsp;&nbsp;
					<?php echo wp_task_manager_bouton_panel();?>
				</form>
			</div>
			<div id="wptm_calendar" style="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></div>
	<?php 
		}else{
			echo '<h1 style="color:red;font-weight:bold;">You don\'t have the right to be here !</h1>';
		}
		?>
		<style>
			.TESTcpYearNavigation,.TESTcpMonthNavigation{
				background-color:#6677DD;text-align:center;vertical-align:center;text-decoration:none;color:#FFFFFF;font-weight:bold;
			}
			.TESTcpDayColumnHeader,.TESTcpYearNavigation,.TESTcpMonthNavigation,.TESTcpCurrentMonthDate,.TESTcpCurrentMonthDateDisabled,
			.TESTcpOtherMonthDate,.TESTcpOtherMonthDateDisabled,.TESTcpCurrentDate,.TESTcpCurrentDateDisabled,.TESTcpTodayText,
			.TESTcpTodayTextDisabled,.TESTcpText{font-family:arial;font-size:8pt;}
			TD.TESTcpDayColumnHeader{text-align:right;border:solid thin #6677DD;border-width:0 0 1 0;}
			.TESTcpCurrentMonthDate,.TESTcpOtherMonthDate,.TESTcpCurrentDate{text-align:right;text-decoration:none;}
			.TESTcpCurrentMonthDateDisabled,.TESTcpOtherMonthDateDisabled,.TESTcpCurrentDateDisabled{color:#D0D0D0;text-align:right;text-decoration:line-through;}
			.TESTcpCurrentMonthDate{color:#6677DD;font-weight:bold;}
			.TESTcpCurrentDate{color: #FFFFFF;font-weight:bold;}
			.TESTcpOtherMonthDate{color:#808080;}
			TD.TESTcpCurrentDate{color:#FFFFFF;background-color: #6677DD;border-width:1;border:solid thin #000000;}
			TD.TESTcpCurrentDateDisabled{border-width:1;border:solid thin #FFAAAA;}
			TD.TESTcpTodayText,TD.TESTcpTodayTextDisabled{border:solid thin #6677DD;border-width:1 0 0 0;}
			A.TESTcpTodayText,SPAN.TESTcpTodayTextDisabled{height:20px;}
			A.TESTcpTodayText{color:#6677DD;font-weight:bold;}
			SPAN.TESTcpTodayTextDisabled{color:#D0D0D0;}
			.TESTcpBorder{border:solid thin #6677DD;}
		</style>
		
		<script language="JavaScript">
			var cal = new CalendarPopup("wptm_calendar");
			cal.setCssPrefix("TEST");
			
			cal.setReturnFunction("setMultipleValues");
			function setMultipleValues(y,m,d) {jQuery("#day").val(d);jQuery("#month").val(m);jQuery("#year").val(y);}
		</script>
<?php
	}
	
	//<?php include_once 'ajax/ajax_view_all.php';
	function wp_task_manager_view_all_task(){
		global $rank,$task_plugIn_base_url,$access_done,$access_delete,$access_edit,$current_user;
		//wp_enqueue_script('sortable', JS_DIRECTORY.'jquery.tablesorter.js', array('jquery'));
		?>		
	<style>#tooltip{position:absolute;border:1px solid #333;background:#f7f5d1;padding:2px 5px;color:#333;display:none;}</style>
	<script type="text/javascript" src="<?php echo JS_DIRECTORY; ?>jquery.tablesorter.js"></script>
	<div>
		<h1>Tasks</h1>
		<p id="wptm_msg"></p>
		<p><u><b>View:</b></u>&nbsp;&nbsp;<span id='wptm_view_all' style="color:blue;">All</span>&nbsp;&nbsp;<span id='wptm_view_active' style="color:red;">Active</span>&nbsp;&nbsp;<span id='wptm_view_done' style="color:blue;">Done</span></p>
		<div id="wptm_all_task"></div>
		<p><?php echo '<br/>'.wp_task_manager_bouton_new();?></p>
		<div id="wptm_comment" style="display:none"></div>
	</div>
	<script>
		var view = "active";
		var comId = 0;
		
		jQuery(document).ready(function () {
			getTask();
		});

		function getTask(){
			jQuery("#wptm_all_task").hide("slow");
			jQuery.get("../wp-content/plugins/wp-task-manager/ajax/ajax_view_all.php", { view: view, rank: <?php echo $rank;?>,access_done:<?php echo $access_done?>,access_edit: <?php echo $access_edit?>,access_delete:<?php echo $access_delete?>, url:"<?php echo $task_plugIn_base_url?>"},
					function(data){
						jQuery("#wptm_all_task").html(data);
					});
			jQuery("#wptm_all_task").toggle("slow");
		}
					
		jQuery("#wptm_view_active").click(function (){
			if("active" != view){
				jQuery("#wptm_view_active").css("color","red");
				jQuery("#wptm_view_all").css("color","blue");
				jQuery("#wptm_view_done").css("color","blue");
				view = "active";
				jQuery("#wptm_comment").hide("slow");
				getTask();
			}				
		});

		jQuery("#wptm_view_all").click(function (){
			if("all" != view){
				jQuery("#wptm_view_all").css("color","red");
				jQuery("#wptm_view_done").css("color","blue");	
				jQuery("#wptm_view_active").css("color","blue");				
				view = "all";
				jQuery("#wptm_comment").hide("slow");
				getTask();
			}				
		});

		jQuery("#wptm_view_done").click(function (){
			if("done" != view){
				jQuery("#wptm_view_done").css("color","red");
				jQuery("#wptm_view_active").css("color","blue");
				jQuery("#wptm_view_all").css("color","blue");
				view = "done";
				jQuery("#wptm_comment").hide("slow");
				getTask();
			}
		});

		function clickAction( dones,ids ){
			jQuery.get("../wp-content/plugins/wp-task-manager/ajax/ajax_done.php", {done: dones, id: ids, user:<?php echo $current_user->ID;?> },
					function(data){
						jQuery("#wptm_msg").html(data);
					});
			getTask();
		}

		function deleteTask( ids ){
			jQuery.get("../wp-content/plugins/wp-task-manager/ajax/ajax_delete.php", {id: ids },
					function(data){
						jQuery("#wptm_msg").html(data);
					});
			getTask();
		}
				
		function displayCom( ids ){
			jQuery("#wptm_comment").hide("slow");
			if( comId != ids){
				comId = ids;
				jQuery.get("../wp-content/plugins/wp-task-manager/ajax/ajax_view_com.php", {id: ids, user_id: <?php echo $current_user->ID; ?> },
					function(data){
						jQuery("#wptm_comment").html(data);
					});
				jQuery("#wptm_comment").toggle("slow");
			}else{
				comId = 0;
			}
		}

		function addCom( ids ){
			vtext = jQuery("#ctext").val();
			jQuery.get("../wp-content/plugins/wp-task-manager/ajax/ajax_add_com.php", {id: ids, user_id: <?php echo $current_user->ID; ?>, text:vtext },
				function(data){
					comId = 0;
					getTask();
					displayCom( ids );
				});
		}
	</script>
<?php
	}
?>
