<?php

/**
 * 抄送人列表
 * @author auto create
 */
class NotifierVO
{
	
	/** 
	 * 抄送时机，可选值有 start, start_finish, finish
	 **/
	public $notify_position;
	
	/** 
	 * 抄送人的用户ID
	 **/
	public $user_id;	
}
?>