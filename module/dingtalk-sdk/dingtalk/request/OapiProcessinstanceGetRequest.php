<?php
/**
 * dingtalk API: dingtalk.oapi.processinstance.get request
 * 
 * @author auto create
 * @since 1.0, 2022.09.27
 */
class OapiProcessinstanceGetRequest
{
	/** 
	 * 审批实例id
	 **/
	private $processInstanceId;
	
	private $apiParas = array();
	
	public function setProcessInstanceId($processInstanceId)
	{
		$this->processInstanceId = $processInstanceId;
		$this->apiParas["process_instance_id"] = $processInstanceId;
	}

	public function getProcessInstanceId()
	{
		return $this->processInstanceId;
	}

	public function getApiMethodName()
	{
		return "dingtalk.oapi.processinstance.get";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->processInstanceId,"processInstanceId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
