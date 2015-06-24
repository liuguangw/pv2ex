<?php

namespace liuguang\pv2ex\model;

use liuguang\pv2ex\model\BaseController;

class User {
	private $conn;
	private $redis;
	private $dbType;
	private $errMsg;
	public function __construct(BaseController $controller) {
		$this->dbType = $controller->getDbType ();
		if ($this->dbType == BaseController::DB_MYSQL) {
			$this->conn = $controller->getConn ();
		} elseif ($this->dbType == BaseController::DB_REDIS) {
			$this->redis = $controller->getRedis ();
		}
	}
	/**
	 * 判断用户名格式是否符合要求
	 *
	 * @param string $username        	
	 * @return boolean
	 */
	public function isUsername($username) {
		if(empty($username)){
			$this->errMsg='用户名不能为空';
			return false;
		}
		if (! preg_match ( '/^[a-z_\\-][a-z0-9_\\-]{0,15}$/', $username )) {
			$this->errMsg='用户名只能含有小写字母、下划线、中横线、数字,最大长度为16位,且不能以数字开头';
			return false;
		} else
			return true;
	}
}