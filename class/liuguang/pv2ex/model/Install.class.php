<?php

namespace liuguang\pv2ex\model;

use liuguang\pv2ex\model\BaseController;

class Install {
	private $conn;
	private $redis;
	private $dbType;
	private $isReady;
	private $errMsg;
	public function __construct(BaseController $controller) {
		// 配置文件中检测安装设置
		if ($controller->getAppConfig ()->get ( 'appinit', false )) {
			$this->isReady = false;
			$this->errMsg = '程序已安装完成,不能再次安装';
		} else {
			$this->dbType = $controller->getDbType ();
			$this->isReady = false;
			if ($this->dbType == BaseController::DB_MYSQL) {
				try {
					$this->conn = $controller->getConn ( true );
					$this->isReady = true;
				} catch ( \PDOException $e ) {
					$this->isReady = false;
					$this->errMsg = $e->getMessage ();
				}
			} elseif ($this->dbType == BaseController::DB_REDIS) {
				try {
					$this->redis = $controller->getRedis ( true );
					$this->isReady = true;
				} catch ( \RedisException $e ) {
					$this->isReady = false;
					$this->errMsg = $e->getMessage ();
				}
			}
		}
	}
	/**
	 * 判断数据库连接是否正常
	 *
	 * @return boolean
	 */
	public function statReady() {
		return $this->isReady;
	}
	/**
	 * 获取错误信息
	 *
	 * @return string
	 */
	public function getErrMsg() {
		return $this->errMsg;
	}
}