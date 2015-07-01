<?php

namespace liuguang\pv2ex\model;

use liuguang\mvc\DataMap;

/**
 * 处理用户会话的模块
 *
 * @author liuguang
 *        
 */
class USession {
	private $conn;
	private $redis;
	private $dbType;
	private $errMsg;
	private $tablePre;
	//
	private $cookieName;
	private $cookieLife;
	private $sid;
	private $sessionData;
	private $isNew;
	private $isDestroy;
	public function __construct(BaseController $controller) {
		$this->dbType = $controller->getDbType ();
		$this->tablePre = $controller->getTablePre ();
		if ($this->dbType == BaseController::DB_MYSQL) {
			$this->conn = $controller->getConn ();
		} elseif ($this->dbType == BaseController::DB_REDIS) {
			$this->redis = $controller->getRedis ();
		}
		$this->cookieName = 'osid';
		$this->cookieLife = 30 * 24 * 3600;
		$this->isDestroy = false;
		if (empty ( $_COOKIE [$this->cookieName] )) {
			$this->initNewSession ();
		} else {
			$sid = $_COOKIE [$this->cookieName];
			if (! preg_match ( '/^[a-z0-9]{32}$/', $sid ))
				$this->initNewSession ();
			elseif (! $this->sidExists ( $sid ))
				$this->initNewSession ();
			else
				$this->initSession ( $sid );
		}
	}
	/**
	 * 判断会话id是否存在
	 *
	 * @param string $sid
	 *        	会话id
	 * @return boolean
	 */
	private function sidExists($sid) {
		if ($this->dbType == BaseController::DB_MYSQL)
			return $this->sidExistsM ( $sid );
		elseif ($this->dbType == BaseController::DB_REDIS)
			return $this->sidExistsR ( $sid );
	}
	/**
	 *
	 * @todo
	 *
	 */
	private function sidExistsM($sid) {
	}
	/**
	 * 获取session在redis数据库中的key名称
	 *
	 * @param string $sid
	 *        	会话id
	 * @return string
	 */
	private function getSessionRkey($sid) {
		return $this->tablePre . 'sid:' . $sid . ':data';
	}
	private function sidExistsR($sid) {
		$redis = $this->redis;
		return $redis->exists ( $this->getSessionRkey ( $sid ) );
	}
	private function createNewSid() {
		$randStr = uniqid ();
		for($i = 0; $i < 8; $i ++) {
			$randStr .= ('-' . rand ( 1000, 9999 ));
		}
		return md5 ( $randStr );
	}
	/**
	 * 初始化一个全新的会话
	 *
	 * @return void
	 */
	private function initNewSession() {
		do {
			$sid = $this->createNewSid ();
		} while ( $this->sidExists ( $sid ) );
		$this->sid = $sid;
		$data = array ();
		$this->sessionData = new DataMap ( $data );
		$this->isNew = true;
	}
	/**
	 * 从sid加载一个已经存在的会话
	 *
	 * @param string $sid        	
	 * @return void
	 */
	private function initSession($sid) {
		$this->sid = $sid;
		$data = $this->redis->hGetAll ( $this->getSessionRkey ( $sid ) );
		$this->sessionData = new DataMap ( $data );
		$this->isNew = false;
	}
	/**
	 * 获取用户id
	 *
	 * @return int
	 */
	public function getUid() {
		return $this->sessionData->get ( 'uid', 0 );
	}
	/**
	 * 设置用户id
	 *
	 * @param int $uid
	 *        	用户id
	 * @return void
	 */
	public function setUid($uid) {
		$this->sessionData->set ( 'uid', $uid );
	}
	/**
	 * 获取当前的会话唯一标识字符串
	 *
	 * @return string
	 */
	public function getSid() {
		return $this->sid;
	}
	/**
	 * 销毁当前会话
	 *
	 * @return void
	 */
	public function destroy() {
		$this->isDestroy = true;
	}
	/**
	 * 获取session数据
	 *
	 * @return \liuguang\mvc\DataMap
	 */
	public function getSessionData() {
		return $this->sessionData;
	}
	/**
	 * 销毁sid会话在数据库内的记录
	 *
	 * @param string $sid
	 *        	会话id
	 * @return void
	 */
	private function destroySession($sid) {
		if ($this->dbType == BaseController::DB_MYSQL)
			$this->destroySessionM ( $sid );
		elseif ($this->dbType == BaseController::DB_REDIS)
			$this->destroySessionR ( $sid );
	}
	/**
	 *
	 * @todo
	 *
	 */
	private function destroySessionM($sid) {
	}
	private function destroySessionR($sid) {
		$redis = $this->redis;
		$redis->del ( $this->getSessionRkey ( $sid ) );
	}
	/**
	 * 保存会话到数据库
	 *
	 * @param string $sid
	 *        	会话id
	 * @return void
	 */
	private function saveSession($sid) {
		setcookie ( $this->cookieName, $sid, time () + $this->cookieLife );
		if ($this->dbType == BaseController::DB_MYSQL)
			$this->saveSessionM ( $sid );
		elseif ($this->dbType == BaseController::DB_REDIS)
			$this->saveSessionR ( $sid );
	}
	/**
	 *
	 * @todo
	 *
	 */
	private function saveSessionM($sid) {
	}
	private function saveSessionR($sid) {
		$redis = $this->redis;
		$key = $this->getSessionRkey ( $sid );
		$redis->hMset ( $key, $this->sessionData->toArray () );
		$redis->expire ( $key, $this->cookieLife );
	}
	/**
	 * 更新数据库里面的会话数据
	 *
	 * @param string $sid
	 *        	会话id
	 * @return void
	 */
	private function updateSession($sid) {
		if ($this->dbType == BaseController::DB_MYSQL)
			$this->updateSessionM ( $sid );
		elseif ($this->dbType == BaseController::DB_REDIS)
			$this->updateSessionR ( $sid );
	}
	/**
	 * 
	 * @todo
	 */
	private function updateSessionM($sid){
		
	}
	private function updateSessionR($sid){
		$redis = $this->redis;
		$key = $this->getSessionRkey ( $sid );
		$redis->hMset ( $key, $this->sessionData->toArray () );
	}
	public function __destruct() {
		if ($this->isDestroy) {
			if (! $this->isNew)
				$this->destroySession ( $this->sid );
		} else {
			if ($this->isNew)
				$this->saveSession ( $this->sid );
			else {
				if ($this->sessionData->hasChanged ())
					$this->updateSession ( $this->sid );
			}
		}
	}
}