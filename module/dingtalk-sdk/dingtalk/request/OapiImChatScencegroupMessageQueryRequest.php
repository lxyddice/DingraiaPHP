<?php
/**
 * dingtalk API: dingtalk.oapi.im.chat.scencegroup.message.query request
 * 
 * @author auto create
 * @since 1.0, 2022.06.27
 */
class OapiImChatScencegroupMessageQueryRequest
{
	/** 
	 * 群标识
	 **/
	private $openConversationId;
	
	/** 
	 * 消息标识
	 **/
	private $openMsgId;
	
	/** 
	 * 消息发送人的unionId（跟userId二选一）
	 **/
	private $senderUnionId;
	
	/** 
	 * 消息发送人的userId（跟unionId二选一）
	 **/
	private $senderUserid;
	
	private $apiParas = array();
	
	public function setOpenConversationId($openConversationId)
	{
		$this->openConversationId = $openConversationId;
		$this->apiParas["open_conversation_id"] = $openConversationId;
	}

	public function getOpenConversationId()
	{
		return $this->openConversationId;
	}

	public function setOpenMsgId($openMsgId)
	{
		$this->openMsgId = $openMsgId;
		$this->apiParas["open_msg_id"] = $openMsgId;
	}

	public function getOpenMsgId()
	{
		return $this->openMsgId;
	}

	public function setSenderUnionId($senderUnionId)
	{
		$this->senderUnionId = $senderUnionId;
		$this->apiParas["sender_union_id"] = $senderUnionId;
	}

	public function getSenderUnionId()
	{
		return $this->senderUnionId;
	}

	public function setSenderUserid($senderUserid)
	{
		$this->senderUserid = $senderUserid;
		$this->apiParas["sender_userid"] = $senderUserid;
	}

	public function getSenderUserid()
	{
		return $this->senderUserid;
	}

	public function getApiMethodName()
	{
		return "dingtalk.oapi.im.chat.scencegroup.message.query";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->openConversationId,"openConversationId");
		RequestCheckUtil::checkNotNull($this->openMsgId,"openMsgId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
