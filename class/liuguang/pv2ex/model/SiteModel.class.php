<?php

namespace liuguang\pv2ex\model;

class SiteModel {
	private $conn;
	private $redis;
	private $dbType;
	private $errMsg;
	private $tablePre;
	public function __construct(BaseController $controller) {
		$this->dbType = $controller->getDbType ();
		$this->tablePre = $controller->getTablePre ();
		if ($this->dbType == BaseController::DB_MYSQL) {
			$this->conn = $controller->getConn ();
		} elseif ($this->dbType == BaseController::DB_REDIS) {
			$this->redis = $controller->getRedis ();
		}
	}
	/**
	 * 获取站点信息
	 *
	 * @param array $fields
	 *        	需要的字段数组，如果为空数组，则表示所有的数组
	 * @return array
	 */
	public function getSiteInfo(array $fields = array()) {
		if ($this->dbType == BaseController::DB_MYSQL)
			return $this->getSiteInfoM ( $fields );
		elseif ($this->dbType == BaseController::DB_REDIS)
			return $this->getSiteInfoR ( $fields );
	}
	/**
	 * @todo 
	 */
	private function getSiteInfoM ( $fields ) {
		
	}
	private function getSiteInfoR( $fields ) {
		$redis=$this->redis;
		$tablePre=$this->tablePre;
		if(empty($fields))
			return $redis->hGetAll($tablePre.'site_confs');
		else
			return $redis->hMget($tablePre.'site_conf',$fields);
	}
}