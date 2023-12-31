<?php
/**
 * dingtalk API: dingtalk.corp.message.corpconversation.asyncsend request
 * 
 * @author auto create
 * @since 1.0, 2022.12.13
 */
class CorpMessageCorpconversationAsyncsendRequest
{
	/** 
	 * 微应用的id
	 **/
	private $agentId;
	
	/** 
	 * 接收者的部门id列表
	 **/
	private $deptIdList;
	
	/** 
	 * 与msgtype对应的消息体，具体见文档
	 **/
	private $msgcontent;
	
	/** 
	 * 消息类型,如text、file、oa等，具体见文档
	 **/
	private $msgtype;
	
	/** 
	 * 是否发送给企业全部用户
	 **/
	private $toAllUser;
	
	/** 
	 * 接收者的用户userid列表
	 **/
	private $useridList;
	
	private $apiParas = array();
	
	public function setAgentId($agentId)
	{
		$this->agentId = $agentId;
		$this->apiParas["agent_id"] = $agentId;
	}

	public function getAgentId()
	{
		return $this->agentId;
	}

	public function setDeptIdList($deptIdList)
	{
		$this->deptIdList = $deptIdList;
		$this->apiParas["dept_id_list"] = $deptIdList;
	}

	public function getDeptIdList()
	{
		return $this->deptIdList;
	}

	public function setMsgcontent($msgcontent)
	{
		$this->msgcontent = $msgcontent;
		$this->apiParas["msgcontent"] = $msgcontent;
	}

	public function getMsgcontent()
	{
		return $this->msgcontent;
	}

	public function setMsgtype($msgtype)
	{
		$this->msgtype = $msgtype;
		$this->apiParas["msgtype"] = $msgtype;
	}

	public function getMsgtype()
	{
		return $this->msgtype;
	}

	public function setToAllUser($toAllUser)
	{
		$this->toAllUser = $toAllUser;
		$this->apiParas["to_all_user"] = $toAllUser;
	}

	public function getToAllUser()
	{
		return $this->toAllUser;
	}

	public function setUseridList($useridList)
	{
		$this->useridList = $useridList;
		$this->apiParas["userid_list"] = $useridList;
	}

	public function getUseridList()
	{
		return $this->useridList;
	}

	public function getApiMethodName()
	{
		return "dingtalk.corp.message.corpconversation.asyncsend";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->agentId,"agentId");
		RequestCheckUtil::checkMaxListSize($this->deptIdList,20,"deptIdList");
		RequestCheckUtil::checkNotNull($this->msgcontent,"msgcontent");
		RequestCheckUtil::checkNotNull($this->msgtype,"msgtype");
		RequestCheckUtil::checkMaxListSize($this->useridList,100,"useridList");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
