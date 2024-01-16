<?php
/**
 * dingtalk API: dingtalk.oapi.extcontact.get request
 * 
 * @author auto create
 * @since 1.0, 2021.04.09
 */
class OapiExtcontactGetRequest
{
	/** 
	 * 外部联系人的userId
	 **/
	private $userId;
	
	private $apiParas = array();
	
	public function setUserId($userId)
	{
		$this->userId = $userId;
		$this->apiParas["user_id"] = $userId;
	}

	public function getUserId()
	{
		return $this->userId;
	}

	public function getApiMethodName()
	{
		return "dingtalk.oapi.extcontact.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->userId,"userId");
		RequestCheckUtil::checkMaxLength($this->userId,64,"userId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
