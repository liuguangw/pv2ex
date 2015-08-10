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
	 *
	 * @todo
	 *
	 */
	private function getSiteInfoM($fields) {
	}
	private function getSiteInfoR($fields) {
		$redis = $this->redis;
		$tablePre = $this->tablePre;
		if (empty ( $fields ))
			return $redis->hGetAll ( $tablePre . 'site_conf' );
		else
			return $redis->hMget ( $tablePre . 'site_conf', $fields );
	}
	/**
	 * 修改站点配置
	 *
	 * @return void
	 */
	public function updateSiteInfo($newSets) {
		if ($this->dbType == BaseController::DB_MYSQL)
			$this->updateSiteInfoM ( $newSets );
		elseif ($this->dbType == BaseController::DB_REDIS)
			$this->updateSiteInfoR ( $newSets );
	}
	/**
	 *
	 * @todo
	 *
	 */
	public function updateSiteInfoM($newSets) {
	}
	public function updateSiteInfoR($newSets) {
		$redis = $this->redis;
		$tablePre = $this->tablePre;
		$redis->hMset($tablePre . 'site_conf' ,$newSets);
	}
	/**
	 * 获取数据库服务器状态
	 *
	 * @return array
	 */
	public function getSiteStat() {
		if ($this->dbType == BaseController::DB_MYSQL)
			return $this->getSiteStatM ();
		elseif ($this->dbType == BaseController::DB_REDIS)
			return $this->getSiteStatR ();
	}
	/**
	 *
	 * @todo
	 *
	 */
	private function getSiteStatM() {
		;
	}
	private function getSiteStatR() {
		$result = array ();
		$redis = $this->redis;
		$lastSave = $redis->lastSave ();
		$result ['last_save'] = date ( 'Y-m-d H:i:s P', $lastSave );
		$result ['dbsize'] = $redis->dbSize ();
		$clientInfo = $redis->info ();
		foreach ( $clientInfo as $key => $value ) {
			$result [$key] = $value;
		}
		return $result;
	}
	/**
	 * 判断当前数据库是否有后台异步保存功能
	 *
	 * @return boolean
	 */
	public function hasBgSave() {
		if ($this->dbType == BaseController::DB_MYSQL)
			return false;
		elseif ($this->dbType == BaseController::DB_REDIS)
			return true;
		else
			return false;
	}
	/**
	 * redis后台保存数据(当数据库为redis时有效)
	 *
	 * @return void
	 */
	public function saveDb() {
		if ($this->dbType == BaseController::DB_REDIS)
			$this->saveDbR ();
	}
	private function saveDbR() {
		$this->redis->bgSave ();
	}
}