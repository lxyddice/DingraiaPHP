<?php
/**
 * dingtalk API: dingtalk.oapi.microapp.list request
 * 
 * @author auto create
 * @since 1.0, 2021.09.17
 */
class OapiMicroappListRequest
{
	
	private $apiParas = array();
	
	public function getApiMethodName()
	{
		return "dingtalk.oapi.microapp.list";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
