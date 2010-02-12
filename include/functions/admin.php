<?php if(!defined('IN_NK')) die('Invalid inclusion.');
/**
 * admin.php
 * 
 * Functions that are used only inside of the admin panel.
 * @author Nijiko Yonskai <me@nijikokun.com>
 * @version 1.2
 * @copyright (c) 2010 ANIGAIKU
 * @package ninko
 * @subpackage functions
 */
 

/**
 * Updates configuration setting inside of database
 * @global array already set $config
 * @global resource
 * @param $key configuration item key
 * @param $value data to be updated
 * @return boolean
 */
function update_config($key, $value)
{
	global $config, $database;
		
	// Update
	$result = $database->query( "UPDATE `config` SET `value`='{$value}' WHERE `key`='{$key}'" );
		
	if($result)
	{
		// Update config
		$config[ $key ] = $value;
			
		// Return true
		return true;
	}
	else
	{
		return false;
	}
}

/**
 * Fetches forum data based on input
 * @global resource
 * @param boolean $posts check to see if we want posts and not topics / topics
 * @param integer $current data to be scanned for links and replaced
 * @param integer $limit data to be scanned for links and replaced
 * @return array|boolean
 */
function fetch_all($posts = false, $current = 0, $limit = 15)
{
	global $database;
	
	if($posts)
	{
		// Query
		$query = "SELECT * FROM `forum` WHERE `reply` != 0 ORDER BY `time` DESC LIMIT {$current},{$limit}";

		// Return Data
		$return = $database->query( $query );
			
		// Exists?
		if($database->num( $return ) > 0)
		{
			// Finally return Results
			while($row = $database->fetch( $return ))
			{
				$rows[] = $row;
			}
			
			return $rows;
		}
		else
		{
			// Guess not~
			return false;
		}
	}
	else
	{
		// Query
		$query = "SELECT * FROM `forum` WHERE `reply` = 0 ORDER BY `updated` DESC LIMIT {$current},{$limit}";

		// Return Data
		$return = $database->query( $query );
			
		// Exists?
		if($database->num( $return ) > 0)
		{
			// Finally return Results
			while($row = $database->fetch( $return ))
			{
				$rows[] = $row;
			}
			
			return $rows;
		}
		else
		{
			// Guess not~
			return false;
		}
	}
}

/**
 * Delete a whole topic and its posts.
 * @global resource
 * @param $id topic identification number
 * @return string|boolean
 */
function delete_topic($id)
{
	global $database;
	
	if(!alpha($id, 'numeric'))
	{
		return 'ID_INVALID'; exit;
	}
	
	$topic = $database->query( "DELETE FROM `forum` WHERE `id` = '{$id}' LIMIT 1" );
	
	// Check to see if the topic was deleted, If not return error!
	if($topic)
	{
		// Check to see if there are any posts, anything at all.
		if(!last_post($id, false, 'id'))
		{
			return true;
		}
		
		// Guess not lets start deleting them.
		$posts = $database->query( "DELETE FROM `forum` WHERE `reply` = '{$id}'" );
		
		if($posts)
		{
			return true;
		}
		else
		{
			return 'DELETING_POSTS';
		}
	}
	else
	{
		return 'DELETING_TOPIC';
	}
}

/**
 * Delete a post
 * @global resource
 * @param $id post identification number
 * @return string|boolean
 */
function delete_post($id)
{
	global $database;
	
	if(!alpha($id, 'numeric'))
	{
		return 'ID_INVALID';
	}
	
	$post = $database->query( "DELETE FROM `forum` WHERE `id` = '{$id}' LIMIT 1" );
	
	// Check to see if the topic was deleted, If not return error!
	if($post)
	{
		return true;
	}
	else
	{
		return 'DELETING_POST';
	}
}

/**
 * Ban a user
 * @global resource
 * @param $id user identification number
 * @return string|boolean
 */
function ban_user($id)
{
	global $database;
	
	if(!alpha($id, 'numeric'))
	{
		return 'ID_INVALID';
	}
	
	$banned = $database->query( "UPDATE `users` SET `banned` = '1' WHERE `id` = '{$id}' LIMIT 1" );
	
	// Check to see if the user was banned.
	if($banned)
	{
		return true;
	}
	else
	{
		return 'BANNING_USER';
	}
}

/**
 * Unban a user
 * @global resource
 * @param $id user identification number
 * @return string|boolean
 */
function unban_user($id)
{
	global $database;
	
	if(!alpha($id, 'numeric'))
	{
		return 'ID_INVALID';
	}
	
	$banned = $database->query( "UPDATE `users` SET `banned` = '0' WHERE `id` = '{$id}' LIMIT 1" );
	
	// Check to see if the user was banned.
	if($banned)
	{
		return true;
	}
	else
	{
		return 'BANNING_USER';
	}
}

?>