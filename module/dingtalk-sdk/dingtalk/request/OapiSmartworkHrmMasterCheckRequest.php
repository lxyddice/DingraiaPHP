<?php
/**
 * dingtalk API: dingtalk.oapi.smartwork.hrm.master.check request
 * 
 * @author auto create
 * @since 1.0, 2021.10.27
 */
class OapiSmartworkHrmMasterCheckRequest
{
	/** 
	 * 业务UK唯一确定一条流水
	 **/
	private $bizUk;
	
	/** 
	 * 子代码，可以不传
	 **/
	private $entityCode;
	
	/** 
	 * 业务领域
	 **/
	private $scopeCode;
	
	/** 
	 * 业务方id(由系统统一分配)
	 **/
	private $tenantId;
	
	private $apiParas = array();
	
	public function setBizUk($bizUk)
	{
		$this->bizUk = $bizUk;
		$this->apiParas["biz_uk"] = $bizUk;
	}

	public function getBizUk()
	{
		return $this->bizUk;
	}

	public function setEntityCode($entityCode)
	{
		$this->entityCode = $entityCode;
		$this->apiParas["entity_code"] = $entityCode;
	}

	public function getEntityCode()
	{
		return $this->entityCode;
	}

	public function setScopeCode($scopeCode)
	{
		$this->scopeCode = $scopeCode;
		$this->apiParas["scope_code"] = $scopeCode;
	}

	public function getScopeCode()
	{
		return $this->scopeCode;
	}

	public function setTenantId($tenantId)
	{
		$this->tenantId = $tenantId;
		$this->apiParas["tenant_id"] = $tenantId;
	}

	public function getTenantId()
	{
		return $this->tenantId;
	}

	public function getApiMethodName()
	{
		return "dingtalk.oapi.smartwork.hrm.master.check";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->bizUk,"bizUk");
		RequestCheckUtil::checkMaxListSize($this->bizUk,20,"bizUk");
		RequestCheckUtil::checkNotNull($this->scopeCode,"scopeCode");
		RequestCheckUtil::checkNotNull($this->tenantId,"tenantId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
