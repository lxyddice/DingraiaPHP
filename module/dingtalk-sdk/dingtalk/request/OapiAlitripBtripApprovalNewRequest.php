<?php
/**
 * dingtalk API: dingtalk.oapi.alitrip.btrip.approval.new request
 * 
 * @author auto create
 * @since 1.0, 2023.05.29
 */
class OapiAlitripBtripApprovalNewRequest
{
	/** 
	 * 请求对象
	 **/
	private $rq;
	
	private $apiParas = array();
	
	public function setRq($rq)
	{
		$this->rq = $rq;
		$this->apiParas["rq"] = $rq;
	}

	public function getRq()
	{
		return $this->rq;
	}

	public function getApiMethodName()
	{
		return "dingtalk.oapi.alitrip.btrip.approval.new";
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
