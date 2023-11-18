<?php

/**
 * 人员列表
 * @author auto create
 */
class Entity
{
	
	/** 
	 * 实体id，表示员工id/部门id
	 **/
	public $id;
	
	/** 
	 * 实体名，表示员工名称/部门名称
	 **/
	public $name;
	
	/** 
	 * 1：员工，2：商旅内部部门，3：三方部门
	 **/
	public $type;	
}
?>