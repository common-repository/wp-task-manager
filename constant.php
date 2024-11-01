<?php
define('DIRECTORY_NAME'				,'wp-task-manager');
define('IMG_DIRECTORY'				,'../wp-content/plugins/'.DIRECTORY_NAME.'/img/');
define('JS_DIRECTORY'				,'../wp-content/plugins/'.DIRECTORY_NAME.'/js/');

define('TABLE_TASK',	'task');
define('TABLE_TASK_COM','task_com');
//---------------------------------------------------------------------------
// Name of my option field in the database
define('OPTION_LANG'				,'task_manager_lang');
define('OPTION_PLUGIN_IS_ACTIVATE'	,'task_manager_activate');
define('OPTION_DATE_FORMAT'			,'task_manager_date_format');
define('OPTION_DELETE_DB'			,'task_manager_delete_db');

define('OPTION_LEVEL_VIEW'			,'task_manager_level_access');
define('OPTION_LEVEL_CREATE'		,'task_manager_level_create');
define('OPTION_LEVEL_EDIT'			,'task_manager_level_edit');
define('OPTION_LEVEL_DONE'			,'task_manager_level_done');
define('OPTION_LEVEL_DELETE'		,'task_manager_level_delete');

define('OPTION_EMAIL_CREATE'		,'task_manger_email_create');
define('OPTION_EMAIL_EDIT'			,'task_manger_email_edit');
define('OPTION_EMAIL_COMMENT'		,'task_manger_email_comment');
define('OPTION_EMAIL_DELETE'		,'task_manger_email_delete');
define('OPTION_EMAIL_DONE'			,'task_manger_email_done');

define('CREATOR_ONLY'				,'10');
define('ACTIVE_USER_ONLY'			,'1');
define('CREATOR_USER'				,'11');
define('NOBODY'						,'0');
//---------------------------------------------------------------------------
// Value of level access. See the table of the option page to understand what the numer means
// -> number of column with the acces
define('ACCESS_TO_VISITOR'	  , '111111');
define('ACCESS_TO_SUBSCRIBER' , '11111');
define('ACCESS_TO_CONTRIBUTOR', '1111');
define('ACCESS_TO_AUTHOR'	  , '111');
define('ACCESS_TO_EDITOR'	  , '11');
define('ACCESS_TO_ADMIN'	  , '1');

//to make link easier


//init global variable
$rank = 0;
$access_view = 0;
$access_create = 0;
$access_delete = 0;
$access_done = 0;
$access_edit = 0;

