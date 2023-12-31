<?php
/**
 * dingtalk API: dingtalk.oapi.xiaoxuan.pre.test1 request
 * 
 * @author auto create
 * @since 1.0, 2023.02.06
 */
class OapiXiaoxuanPreTest1Request
{
	/** 
	 * 1
	 **/
	private $name;
	
	/** 
	 * 1
	 **/
	private $normalData;
	
	/** 
	 * 1
	 **/
	private $systemData;
	
	private $apiParas = array();
	
	public function setName($name)
	{
		$this->name = $name;
		$this->apiParas["name"] = $name;
	}

	public function getName()
	{
		return $this->name;
	}

	public function setNormalData($normalData)
	{
		$this->normalData = $normalData;
		$this->apiParas["normalData"] = $normalData;
	}

	public function getNormalData()
	{
		return $this->normalData;
	}

	public function setSystemData($systemData)
	{
		$this->systemData = $systemData;
		$this->apiParas["systemData"] = $systemData;
	}

	public function getSystemData()
	{
		return $this->systemData;
	}

	public function getApiMethodName()
	{
		return "dingtalk.oapi.xiaoxuan.pre.test1";
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
