<?php

/**
 * result
 * @author auto create
 */
class IsvAuthCodeResult
{
	
	/** 
	 * 授权码有效期，unix时间戳，单位ms
	 **/
	public $expire_time;
	
	/** 
	 * isv访问授权码
	 **/
	public $isv_code;	
}
?>