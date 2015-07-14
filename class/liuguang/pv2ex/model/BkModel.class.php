<?php

namespace liuguang\pv2ex\model;

/**
 * 板块节点模块
 *
 * @author liuguang
 *        
 */
class BkModel {
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
	 * 创建一个板块节点
	 *
	 * @param int $pid
	 *        	节点的上级id,根节点为0
	 * @param string $bkname
	 *        	节点名称
	 * @param string $isBk
	 *        	是否为一个节点,true表示节点，false表示逻辑区域
	 * @param string $isOpen
	 *        	是否开启次节点
	 * @param string $needLogin
	 *        	是否需要登录
	 * @return int 返回创建的节点id
	 */
	public function createBk($pid, $bkname, $isBk = true, $isOpen = true, $needLogin = false) {
		if ($this->dbType == BaseController::DB_MYSQL)
			return $this->createBkM ( $pid, $bkname, $isBk, $isOpen, $needLogin );
		elseif ($this->dbType == BaseController::DB_REDIS)
			return $this->createBkR ( $pid, $bkname, $isBk, $isOpen, $needLogin );
	}
	/**
	 *
	 * @todo
	 *
	 */
	private function createBkM($pid, $bkname, $isBk, $isOpen, $needLogin) {
		;
	}
	private function createBkR($pid, $bkname, $isBk, $isOpen, $needLogin) {
		$redis = $this->redis;
		$tablePre = $this->tablePre;
		$lua = '-- 获取板块id
		local bkid=redis.call(\'hincrby\',ARGV[1]..\'counter\',\'bkid\',1)
		-- 设置板块节点的父节点
		redis.call(\'zadd\',ARGV[1]..\'bkid:\'..ARGV[3]..\':children\',bkid,bkid)
		redis.call(\'hmset\',ARGV[1]..\'bkid:\'..bkid..\':bkinfo\',\'pid\',ARGV[3],\'bkid\',bkid,\'is_bk\',ARGV[5],\'is_open\',ARGV[6],\'need_login\',ARGV[7],\'bkname\',ARGV[4],\'bk_alt\',ARGV[4]..\'....\',\'create_time\',ARGV[2])
		return bkid';
		return $redis->eval ( $lua, array (
				$tablePre,
				time (),
				$pid,
				$bkname,
				($isBk ? '1' : '0'),
				($isOpen ? '1' : '0'),
				($needLogin ? '1' : '0') 
		) );
	}
	/**
	 * 判断节点id是否存在
	 *
	 * @param int $bkid
	 *        	板块id
	 * @return boolean
	 */
	public function bkIdExists($bkid) {
		if ($this->dbType == BaseController::DB_MYSQL)
			return $this->bkIdExistsM ( $bkid );
		elseif ($this->dbType == BaseController::DB_REDIS)
			return $this->bkIdExistsR ( $bkid );
	}
	/**
	 *
	 * @todo
	 *
	 */
	private function bkIdExistsM($bkid) {
	}
	private function bkIdExistsR($bkid) {
		if ($bkid == 0)
			return true;
		$redis = $this->redis;
		$tablePre = $this->tablePre;
		return $redis->exists ( $tablePre . 'bkid:' . $bkid . ':bkinfo' );
	}
	/**
	 * 获取节点的信息
	 *
	 * @param int $bkid
	 *        	节点id
	 * @param array $field
	 *        	字段的数组,若为空数组,则表示获取所有的字段信息
	 * @return array
	 */
	public function getBkinfo($bkid, array $field = array()) {
		if ($this->dbType == BaseController::DB_MYSQL)
			return $this->getBkinfoM ( $bkid, $field );
		elseif ($this->dbType == BaseController::DB_REDIS)
			return $this->getBkinfoR ( $bkid, $field );
	}
	/**
	 *
	 * @todo
	 *
	 */
	private function getBkinfoM($bkid, $field) {
	}
	private function getBkinfoR($bkid, $field) {
		$redis = $this->redis;
		$tablePre = $this->tablePre;
		$keyStr = $tablePre . 'bkid:' . $bkid . ':bkinfo';
		if (empty ( $field ))
			return $redis->hGetAll ( $keyStr );
		else
			return $redis->hMget ( $keyStr, $field );
	}
	/**
	 * 判断一个节点是否拥有子节点
	 *
	 * @param int $bkid
	 *        	节点id
	 * @return boolean
	 */
	public function hasChildBk($bkid) {
		if ($this->dbType == BaseController::DB_MYSQL)
			return $this->hasChildBkM ( $bkid );
		elseif ($this->dbType == BaseController::DB_REDIS)
			return $this->hasChildBkR ( $bkid );
	}
	/**
	 *
	 * @todo
	 *
	 */
	private function hasChildBkM($bkid) {
	}
	private function hasChildBkR($bkid) {
		$redis = $this->redis;
		$tablePre = $this->tablePre;
		$keyStr = $tablePre . 'bkid:' . $bkid . ':children';
		if (! $redis->exists ( $keyStr ))
			return false;
		else {
			$tmpArr = $redis->zRange ( $keyStr, 0, 0 );
			return (! empty ( $tmpArr ));
		}
	}
	/**
	 * 获取下级节点id列表
	 *
	 * @param int $bkid
	 *        	父节点id
	 * @param int $limit
	 *        	限制返回的节点数目,默认值为0,表示不限制返回数目
	 * @return array
	 */
	public function getChildrenBks($bkid, $limit = 0) {
		if ($this->dbType == BaseController::DB_MYSQL)
			return $this->getChildrenBksM ( $bkid, $limit );
		elseif ($this->dbType == BaseController::DB_REDIS)
			return $this->getChildrenBksR ( $bkid, $limit );
	}
	/**
	 *
	 * @todo
	 *
	 */
	private function getChildrenBksM($bkid, $limit) {
	}
	private function getChildrenBksR($bkid, $limit) {
		$redis = $this->redis;
		$tablePre = $this->tablePre;
		$keyStr = $tablePre . 'bkid:' . $bkid . ':children';
		if (! $redis->exists ( $keyStr ))
			return array ();
		if ($limit == 0)
			return $redis->zRange ( $keyStr, 0, - 1 );
		else
			return $redis->zRange ( $keyStr, 0, $limit - 1 );
	}
	/**
	 * 将板块节点加入到首页导航处
	 *
	 * @param int $bkid
	 *        	节点id
	 * @return void
	 */
	public function addBk2Index($bkid) {
		if ($this->dbType == BaseController::DB_MYSQL)
			$this->addBk2IndexM ( $bkid );
		elseif ($this->dbType == BaseController::DB_REDIS)
			$this->addBk2IndexR ( $bkid );
	}
	/**
	 *
	 * @todo
	 *
	 */
	private function addBk2IndexM($bkid) {
	}
	private function addBk2IndexR($bkid) {
		$redis = $this->redis;
		$tablePre = $this->tablePre;
		$redis->zAdd ( $tablePre . 'index_bkids', $bkid, $bkid );
	}
}