<?php
/**
 * dingtalk API: dingtalk.oapi.kac.datav.dept.chat.summary.list request
 * 
 * @author auto create
 * @since 1.0, 2021.11.30
 */
class OapiKacDatavDeptChatSummaryListRequest
{
	/** 
	 * 请求参数对象
	 **/
	private $request;
	
	private $apiParas = array();
	
	public function setRequest($request)
	{
		$this->request = $request;
		$this->apiParas["request"] = $request;
	}

	public function getRequest()
	{
		return $this->request;
	}

	public function getApiMethodName()
	{
		return "dingtalk.oapi.kac.datav.dept.chat.summary.list";
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
