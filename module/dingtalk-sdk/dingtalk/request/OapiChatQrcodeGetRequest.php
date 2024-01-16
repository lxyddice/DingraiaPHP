<?php
/**
 * dingtalk API: dingtalk.oapi.chat.qrcode.get request
 * 
 * @author auto create
 * @since 1.0, 2021.07.09
 */
class OapiChatQrcodeGetRequest
{
	/** 
	 * 会话id（逐步淘汰推荐使用openConversationId)
	 **/
	private $chatid;
	
	/** 
	 * 开放群id（与会话id 二选一）
	 **/
	private $openConversationId;
	
	/** 
	 * 分享二维码用户id
	 **/
	private $userid;
	
	private $apiParas = array();
	
	public function setChatid($chatid)
	{
		$this->chatid = $chatid;
		$this->apiParas["chatid"] = $chatid;
	}

	public function getChatid()
	{
		return $this->chatid;
	}

	public function setOpenConversationId($openConversationId)
	{
		$this->openConversationId = $openConversationId;
		$this->apiParas["openConversationId"] = $openConversationId;
	}

	public function getOpenConversationId()
	{
		return $this->openConversationId;
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
		return "dingtalk.oapi.chat.qrcode.get";
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
