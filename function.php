<?php
	require_once 'constant.php';
	/**
	 * Get the rank of a user with his id. use $current_user->ID to get the id
	 * @param int $id
	 * @return INT rank code
	 * @author Thomas Genin <xt6@free.fr>
	 */
	function get_user_rank( $id ){
		global $wpdb;
		
		$table = $wpdb->prefix."usermeta";
		$query = "SELECT U.meta_value as rank 
					FROM $table U
					WHERE U.meta_key = '".$wpdb->prefix."user_level' AND U.user_id = $id;";
		$ret = $wpdb->get_var($query);
		switch ($ret){
			case 10:
				$rank = ACCESS_TO_ADMIN;
				break;
				
			case 7:
				$rank = ACCESS_TO_EDITOR;
				break;
				
			case 2:
				$rank = ACCESS_TO_AUTHOR;
				break;
				
			case 1:
				$rank = ACCESS_TO_CONTRIBUTOR;
				break;
				
			default:
				$rank = ACCESS_TO_SUBSCRIBER;
				break;
		}
		return $rank;
	}
	
	function wp_task_manager_display_status( $isdone ){
		if( 0 == $isdone)
			return '<img src="'.IMG_DIRECTORY.'active.png" alt="The task is active : work in progress !" title="The task is active : work in progress !"/>';
		else if( 1 == $isdone)
			return '<img src="'.IMG_DIRECTORY.'lock.png" alt="The task is finish" title="The task is finish"/>';
		else
			return '<img src="'.IMG_DIRECTORY.'alert.png" alt="Error, can\'t get the status" title="Error, can\'t get the status"/>';
	}
	
	
	function wp_task_manager_display_action( $id, $isdone, $view){
		global $task_plugIn_base_url;
		
		if( 0 == $isdone)
			return '<img onclick="clickAction( 0,'.$id.' )" src="'.IMG_DIRECTORY.'tick.png" alt="Make task done"/>';
		else if( 1 == $isdone)
			return '<img onclick="clickAction( 1,'.$id.' )" src="'.IMG_DIRECTORY.'undo.png" alt="Make task active again"/>';
		else
			return '<img src="'.IMG_DIRECTORY.'alert.png" alt="Error, can\'t get the status" title="Error, can\'t get the status"/>';
	}

	
	function wp_task_manager_display_edit_task($id, $view, $url)
	{
		return '<a href="'.$url.'&amp;task=new&action=edit&id='.$id.'&view='.$view.'" title="Edit the task"><img src="'.IMG_DIRECTORY.'edit.png" alt="Edit task"/></a>';	
	}
	
	
	function wp_task_manager_display_delete_task($id, $view)
	{
		global $task_plugIn_base_url;
		return '<img onclick="deleteTask( '.$id.' )" src="'.IMG_DIRECTORY.'cross.png" alt="Delete Task"/>';	
	}	
		
	function wp_task_manager_task_is_done($id){
		global $wpdb;
		$table = $wpdb->prefix."task";
		$wpdb->update($table, array( 'task_isDone' => 1), array('task_id' => $id ) );
	}
	
	
	function wp_task_manager_task_un_done($id){
		global $wpdb;
		$table = $wpdb->prefix."task";
		$wpdb->update($table, array( 'task_isDone' => 0), array('task_id' => $id ) );
	}	
	
	function wp_task_manager_task_delete($id){
		global $wpdb;
		$table = $wpdb->prefix."task";
		$query = "DELETE FROM $table WHERE task_id= $id LIMIT 1;";
		$wpdb->query($query);
	}

	function wptm_delete_com( $id){
		global $wpdb;
		$table = $wpdb->prefix."task_comment";
		$query = "DELETE FROM $table WHERE task_com_task_id= $id;";
		$wpdb->query($query);
	}
	
	
	function wp_task_manager_bouton_new(){
		global $task_plugIn_base_url,$rank,$access_create;
		if( $access_create >= $rank )
			$ret = '<a href="'.$task_plugIn_base_url.'&amp;task=new"><input type="button" value="New" /></a>';
		else
			$ret = ''; 
		return $ret;
	}
	
	/**
	 * Create a link to the panel (= page with all the tasks)
	 * @return string full <a></a>
	 * @author Thomas GENIN <xt6@free.fr>
	 */
	function wp_task_manager_bouton_panel(){
		global $task_plugIn_base_url;
		return '<a href="'.$task_plugIn_base_url.'&amp;task=all"><input type="button" value="Manager" title="View all tasks"/></a>';
	}
	
	
		/**
	 * Make a selectbox with the actual level access _> use for option page
	 * @param $option_name name of the option to make the select
	 * @return string : full select box
	 * @author Thomas GENIN <xt6@free.fr>
	 */
	function wptm_make_select_option_access( $option_name ){
		
		$level_edit = get_option($option_name);
		$select = '<select class="wptm_select_access" id="'.$option_name.'" name="'.$option_name.'">';
		
		//ADMIN
		$select .= '<option value="'.ACCESS_TO_ADMIN.'"';
		if( ACCESS_TO_ADMIN == $level_edit )
			$select .= 'selected="selected" ';
		$select .= '>Administrator</option>';
		
		//EDITOR
		$select .= '<option value="'.ACCESS_TO_EDITOR.'"';
		if( ACCESS_TO_EDITOR == $level_edit )
			$select .= 'selected="selected" ';
		$select .= '>Editor</option>';

		//AUTHOR
		$select .= '<option value="'.ACCESS_TO_AUTHOR.'"';
		if( ACCESS_TO_AUTHOR == $level_edit )
			$select .= 'selected="selected" ';
		$select .= '>Author</option>';

		//CONTRIBUTOR
		$select .= '<option value="'.ACCESS_TO_CONTRIBUTOR.'"';
		if( ACCESS_TO_CONTRIBUTOR == $level_edit )
			$select .= 'selected="selected" ';
		$select .= '>Contributor</option>';

		//SUBSCRIBER
		$select .= '<option value="'.ACCESS_TO_SUBSCRIBER.'"';
		if( ACCESS_TO_SUBSCRIBER == $level_edit )
			$select .= 'selected="selected" ';
		$select .= '>Subscriber</option>';
						
		//VISITOR
		$select .= '<option value="'.ACCESS_TO_VISITOR.'"';
		if( ACCESS_TO_VISITOR == $level_edit )
			$select .= 'selected="selected" ';
		$select .= '>Visitor (Not functionnal)</option>';				
		
		$select .= '</select>';		
	
		return $select;
	}
	
	
		/**
	 * Get a select box which contain all the contributor of the blog
	 * @return string : the full select box name = 'user'
	 * @author Thomas GENIN
	 * @version 1
	 */
	function wp_task_manager_get_select_all_contributor( $select=null )
	{
		global $wpdb;  
	
		$table_user_meta = $wpdb->prefix."usermeta";
		$table_user = $wpdb->prefix."users";
		
		$query = "SELECT U.ID as id, U.display_name as name 
					FROM $table_user U
					JOIN $table_user_meta UM ON U.ID = UM.user_id
					WHERE UM.meta_key = '".$wpdb->prefix."user_level' AND UM.meta_value >= 1 
					ORDER BY display_name;";
		$res = $wpdb->get_results( $query, ARRAY_A );

		if( $res ){
			if( 1 == count($res) ){
				$list = '<select name="user">';
				foreach($res as $one_user){
					if($select == $one_user['id'])
						$list .= '<option value="'.$one_user['id'].'" selected="selected">'.$one_user['name'].'</option>';
					else
						$list .= '<option value="'.$one_user['id'].'">'.$one_user['name'].'</option>';
				}
				$list .= '</select>';				
			}else{
				$list = '<select name="user"><option value="###">Choose ...</option>';
				foreach($res as $one_user){
					if($select == $one_user['id'])
						$list .= '<option value="'.$one_user['id'].'" selected="selected">'.$one_user['name'].'</option>';
					else
						$list .= '<option value="'.$one_user['id'].'">'.$one_user['name'].'</option>';
				}
				$list .= '</select>';
			}
			return $list;
		}else{
			return '<span style="color:red;">ERROR LIST OF USER EMPTY</span>';
		}
	}
	
	
	function wptm_get_select_email( $nameOption ){
		
		$select = get_option( $nameOption);
		
		$ret = '<select class="wptm_select_notification" id="'.$nameOption.'" name="'.$nameOption.'">';
		$ret .= '<option value="'.CREATOR_USER.'"';
		if( CREATOR_USER == $select )
			$ret .= 'selected="selected"';
		$ret .= ' >Creator & user</option>';
		$ret .= '<option value="'.CREATOR_ONLY.'"';
		if( CREATOR_ONLY == $select )
			$ret .= 'selected="selected"';
		$ret .= ' >Creator Only</option>';
		$ret .= '<option value="'.ACTIVE_USER_ONLY.'"';
		if( ACTIVE_USER_ONLY == $select )
			$ret .= 'selected="selected"';
		$ret .= ' >User only</option>';
		$ret .= '<option value="'.NOBODY.'"';
		if( NOBODY == $select )
			$ret .= 'selected="selected"';
		$ret .= ' >Nobody</option>';		
		$ret .= '</select>';
		return $ret;
	}
	
	/**
	 * DEPRECEAT Make several input text for day, month, year. 
	 * @param $date
	 * @param $day
	 * @param $month
	 * @param $year
	 * @return unknown_type
	 */
	function wp_task_manager_get_sel_date($date=null,$day=null,$month=null,$year=null)
	{
		if( empty( $date ) )
			$date = date( 'Y-m-d' );
		
		if( $date != 1 && is_string( $date ) ){
			$timestamp 	= strtotime( $date );
			$day 		= date( 'j', $timestamp );
			$month 		= date( 'n', $timestamp );
			$year 		= date( 'Y', $timestamp );
		}

		$dayList 	= '<select name="day"><option value="###">Choose ...</option>';
		$monthList 	= '<select name="month"><option value="###">Choose ...</option>';
		$yearList 	= '<input type="text" name="year" value="'.$year.'"/>';
		
		for( $i=1; $i<32; $i++ ){
			if( $i == $day ){
				$dayList .= "<option value='$i' selected>$i</option>";
			}else{
				$dayList .= "<option value='$i'>$i</option>";
			}
		}
		$dayList .='</select>';
		
		for( $i=1; $i<13; $i++ ){
			if( $i == $month ){
				$monthList .= "<option value='$i' selected>$i</option>";
			}else{
				$monthList .= "<option value='$i'>$i</option>";
			}
		}
		$monthList .= '</select>';
		
		return "Day:&nbsp;$dayList&nbsp;Month:&nbsp;$monthList&nbsp;Year:&nbsp;$yearList";
	}
	
	/**
	 * Check permission and send email to the right people
	 * 
	 * @param $permission => get from option database
	 * @param $title => title of the email
	 * @param $message => content of the emai
	 * @param $userID => current userID
	 * @param $taskId => ...
	 * @return unknown_type
	 */
	function wptm_email_notification( $permission,$title, $message, $userID, $taskId ){
		global $wpdb;
		
		$blog_title = get_bloginfo('name');
		$message .= '<br/><br/><br/>--------------------------------------------<br/><span style="font-size:x-small;">Email send by the Wordpress Task Manager plugin created by Thomas Genin<br/>Visit <a href="http://thomas.lepetitmonde.net/en/projects/wordpress-task-manager">http://thomas.lepetitmonde.net/en/projects/wordpress-task-manager/</a> for more information.</span>';
		
		$header =  $headers ='From: "'.$blog_title.'"<'.$blog_title.'@wp-task-manger.com>'."\n";
    	$header .='Reply-To: noreply@wp-task-manager.com'."\n";
     	$header .='Content-Type: text/html; charset="iso-8859-1"'."\n";
     	$header .='Content-Transfer-Encoding: 8bit';
		
		switch($permission){
			default:
			case NOBODY:
				break;
			
			case ACTIVE_USER_ONLY:
				$table_task = $wpdb->prefix.TABLE_TASK;
				$query = "SELECT task_to FROM $table_task WHERE task_id = $taskId LIMIT 1;";
				$id = $wpdb->get_var($query);
				if( $id != $userID ){
					$table_user  = $wpdb->prefix.'users';
					$query = "SELECT user_email FROM $table_user WHERE ID = $id LIMIT 1";
					$email = $wpdb->get_var( $query );
	
					wp_mail($email,$blog_title.' | '.$title,$message,$header);
				}		
				break;
				
			case CREATOR_ONLY:
				$table_task = $wpdb->prefix.TABLE_TASK;
				$query = "SELECT task_creator FROM $table_task WHERE task_id = $taskId LIMIT 1;";
				$id = $wpdb->get_var($query);
				if( $id != $userID ){
					$table_user  = $wpdb->prefix.'users';
					$query = "SELECT user_email FROM $table_user WHERE ID = $id LIMIT 1";
					$email = $wpdb->get_var( $query );
	
					wp_mail($email,$blog_title.' | '.$title,$message,$header);
				}	
				break;
				
			case CREATOR_USER:
				$table_task = $wpdb->prefix.TABLE_TASK;
				//USER
				$query = "SELECT task_to FROM $table_task WHERE task_id = $taskId LIMIT 1;";
				$id = $wpdb->get_var($query);
				if( $id != $userID ){
					$table_user  = $wpdb->prefix.'users';
					$query = "SELECT user_email FROM $table_user WHERE ID = $id LIMIT 1";
					$email = $wpdb->get_var( $query );
	
					wp_mail($email,$blog_title.' | '.$title,$message,$header);
				}	
				//CREATOR
				$query = "SELECT task_creator FROM $table_task WHERE task_id = $taskId LIMIT 1;";
				$id = $wpdb->get_var($query);
				if( $id != $userID ){
					$table_user  = $wpdb->prefix.'users';
					$query = "SELECT user_email FROM $table_user WHERE ID = $id LIMIT 1;";
					$email = $wpdb->get_var( $query );
	
					wp_mail($email,$blog_title.' | '.$title,$message,$header);
				}									
				break;
		}
		
	}
	
	
	