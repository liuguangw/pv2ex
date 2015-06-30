<?php

namespace liuguang\pv2ex\model;

class SiteUrl {
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
	 * 添加底部链接
	 *
	 * @param string $linkName
	 *        	链接的文本
	 * @param string $linkUrl
	 *        	链接的目标url
	 * @return void
	 */
	public function addUrl($linkName, $linkUrl) {
		if ($this->dbType == BaseController::DB_MYSQL)
			$this->addUrlM ( $linkName, $linkUrl );
		elseif ($this->dbType == BaseController::DB_REDIS)
			$this->addUrlR ( $linkName, $linkUrl );
	}
	/**
	 *
	 * @todo
	 *
	 */
	private function addUrlM($linkName, $linkUrl) {
	}
	private function addUrlR($linkName, $linkUrl) {
		$redis = $this->redis;
		$tablePre = $this->tablePre;
		// 获取链接id
		$linkid = $redis->hIncrBy ( $tablePre . 'counter', 'linkid', 1 );
		// 设置链接信息
		$redis->hMset ( $tablePre . 'linkid:' . $linkid . ':lininfo', array (
				'name' => $linkName,
				'url' => $linkUrl 
		) );
		$redis->zAdd ( $tablePre . 'links', $linkid, $linkid );
	}
	/**
	 * 获取底部所有链接
	 *
	 * @return array
	 */
	public function getSiteLinks() {
		if ($this->dbType == BaseController::DB_MYSQL)
			return $this->getSiteLinksM ();
		elseif ($this->dbType == BaseController::DB_REDIS)
			return $this->getSiteLinksR ();
	}
	/**
	 * @todo
	 */
	private function getSiteLinksM() {
	}
	private function getSiteLinksR() {
		$redis = $this->redis;
		$tablePre = $this->tablePre;
		$linkIdArr=$redis->zRange($tablePre.'links',0,-1);
		$links=array();
		foreach ($linkIdArr as $linkid){
			$links[]=$redis->hGetAll($tablePre . 'linkid:' . $linkid . ':lininfo');
		}
		return $links;
	}
}