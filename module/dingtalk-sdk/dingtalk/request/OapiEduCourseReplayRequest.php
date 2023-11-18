<?php
/**
 * dingtalk API: dingtalk.oapi.edu.course.replay request
 * 
 * @author auto create
 * @since 1.0, 2021.07.03
 */
class OapiEduCourseReplayRequest
{
	/** 
	 * 需要回放的课程编码
	 **/
	private $courseCode;
	
	/** 
	 * 操作用户id
	 **/
	private $opUserId;
	
	/** 
	 * 指定一段的历史回放编码
	 **/
	private $targetId;
	
	private $apiParas = array();
	
	public function setCourseCode($courseCode)
	{
		$this->courseCode = $courseCode;
		$this->apiParas["course_code"] = $courseCode;
	}

	public function getCourseCode()
	{
		return $this->courseCode;
	}

	public function setOpUserId($opUserId)
	{
		$this->opUserId = $opUserId;
		$this->apiParas["op_user_id"] = $opUserId;
	}

	public function getOpUserId()
	{
		return $this->opUserId;
	}

	public function setTargetId($targetId)
	{
		$this->targetId = $targetId;
		$this->apiParas["target_id"] = $targetId;
	}

	public function getTargetId()
	{
		return $this->targetId;
	}

	public function getApiMethodName()
	{
		return "dingtalk.oapi.edu.course.replay";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->courseCode,"courseCode");
		RequestCheckUtil::checkNotNull($this->opUserId,"opUserId");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
