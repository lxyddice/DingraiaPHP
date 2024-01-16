<?php
/**
 * dingtalk API: dingtalk.oapi.medal.corpmedal.query request
 * 
 * @author auto create
 * @since 1.0, 2021.05.20
 */
class OapiMedalCorpmedalQueryRequest
{
	/** 
	 * 勋章模板ID列表
	 **/
	private $templateIds;
	
	/** 
	 * 员工ID
	 **/
	private $userid;
	
	private $apiParas = array();
	
	public function setTemplateIds($templateIds)
	{
		$this->templateIds = $templateIds;
		$this->apiParas["template_ids"] = $templateIds;
	}

	public function getTemplateIds()
	{
		return $this->templateIds;
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
		return "dingtalk.oapi.medal.corpmedal.query";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkMaxListSize($this->templateIds,10,"templateIds");
		RequestCheckUtil::checkNotNull($this->userid,"userid");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
