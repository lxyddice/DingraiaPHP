<?php
/**
 * dingtalk API: dingtalk.oapi.process.template.manage.get request
 * 
 * @author auto create
 * @since 1.0, 2021.12.14
 */
class OapiProcessTemplateManageGetRequest
{
	/** 
	 * 应用id
	 **/
	private $appUuid;
	
	/** 
	 * 用户id
	 **/
	private $userid;
	
	private $apiParas = array();
	
	public function setAppUuid($appUuid)
	{
		$this->appUuid = $appUuid;
		$this->apiParas["app_uuid"] = $appUuid;
	}

	public function getAppUuid()
	{
		return $this->appUuid;
	}

	public function setUserid($userid)
	{
		$this->userid = $userid;
		$this->apiParas["userid"] = $userid;
	}

	public function getUserid()
	{
		return $this->userid;
	}

	public function getApiMethodName()
	{
		return "dingtalk.oapi.process.template.manage.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->userid,"userid");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
