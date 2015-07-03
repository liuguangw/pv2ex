<?php

namespace liuguang\pv2ex\model;

class User {
	private $conn;
	private $redis;
	private $dbType;
	private $errMsg;
	private $tablePre;
	private $userImg;
	public function __construct(BaseController $controller) {
		$this->dbType = $controller->getDbType ();
		$this->tablePre = $controller->getTablePre ();
		$this->userImg = $controller->getAppConfig ()->get ( 'app_pub_context' ) . '/img/user_default.png';
		if ($this->dbType == BaseController::DB_MYSQL) {
			$this->conn = $controller->getConn ();
		} elseif ($this->dbType == BaseController::DB_REDIS) {
			$this->redis = $controller->getRedis ();
		}
	}
	/**
	 * 获取错误信息
	 *
	 * @return string
	 */
	public function getErrMsg() {
		return $this->errMsg;
	}
	/**
	 * 判断用户名格式是否符合要求
	 *
	 * @param string $username        	
	 * @return boolean
	 */
	public function isUsername($username) {
		if (empty ( $username )) {
			$this->errMsg = '用户名不能为空';
			return false;
		}
		if (! preg_match ( '/^[a-z_\\-][a-z0-9_\\-]{0,15}$/', $username )) {
			$this->errMsg = '用户名只能含有小写字母、下划线、中横线、数字,最大长度为16位,且不能以数字开头';
			return false;
		} else
			return true;
	}
	/**
	 * 判断昵称格式是否符合要求
	 *
	 * @param string $nickname        	
	 * @return boolean
	 */
	public function isNickname($nickname) {
		$nickLength = mb_strlen ( $nickname, 'UTF-8' );
		if ($nickLength == 0) {
			$this->errMsg = '昵称不能为空';
			return false;
		} elseif ($nickLength > 14) {
			$this->errMsg = '昵称最多只能包含14个字符';
			return false;
		} else
			return true;
	}
	/**
	 * 判断邮箱格式是否符合要求
	 *
	 * @param string $email        	
	 * @return boolean
	 */
	public function isEmail($email) {
		$eLength = strlen ( $email );
		if ($eLength == 0) {
			$this->errMsg = 'email不能为空';
			return false;
		} elseif ($eLength > 24) {
			$this->errMsg = '邮箱最多只能包含24个字符';
			return false;
		}
		// 正则判断邮箱格式
		if (! preg_match ( '/^([A-Za-z0-9\\-_.+]+)@([A-Za-z0-9\\-]+[.][A-Za-z0-9\\-.]+)$/', $email )) {
			$this->errMsg = '邮箱格式错误';
			return false;
		} else
			return true;
	}
	/**
	 * 判断密码格式是否符合要求
	 *
	 * @param string $pass        	
	 * @return boolean
	 */
	public function isPass($pass) {
		$pLength = strlen ( $pass );
		if ($pLength == 0) {
			$this->errMsg = '密码不能为空';
			return false;
		}
		if ($pLength < 6) {
			$this->errMsg = '密码不能少于6位';
			return false;
		} elseif ($pLength > 35) {
			$this->errMsg = '密码不能超过35位';
			return false;
		} else
			return true;
	}
	/**
	 * 判断用户名是否已经存在
	 *
	 * @param string $username        	
	 * @return boolean
	 */
	public function isUsernameExists($username) {
		if ($this->dbType == BaseController::DB_MYSQL)
			return $this->isUsernameExistsM ( $username );
		elseif ($this->dbType == BaseController::DB_REDIS)
			return $this->isUsernameExistsR ( $username );
	}
	/**
	 * 判断用户名是否已经存在
	 *
	 * @todo
	 *
	 * @param string $username        	
	 * @return boolean
	 */
	private function isUsernameExistsM($username) {
	}
	/**
	 * 判断用户名是否已经存在
	 *
	 * @param string $username        	
	 * @return boolean
	 */
	private function isUsernameExistsR($username) {
		return $this->redis->hExists ( $this->tablePre . 'username_uids', $username );
	}
	/**
	 * 判断邮箱是否已经存在
	 *
	 * @param string $email        	
	 * @return boolean
	 */
	public function isEmailExists($email) {
		if ($this->dbType == BaseController::DB_MYSQL)
			return $this->isemailExistsM ( $email );
		elseif ($this->dbType == BaseController::DB_REDIS)
			return $this->isemailExistsR ( $email );
	}
	/**
	 * 判断邮箱是否已经存在
	 *
	 * @todo
	 *
	 * @param string $email        	
	 * @return boolean
	 */
	private function isEmailExistsM($email) {
	}
	/**
	 * 判断邮箱是否已经存在
	 *
	 * @param string $email        	
	 * @return boolean
	 */
	private function isEmailExistsR($email) {
		return $this->redis->hExists ( $this->tablePre . 'email_uids', $email );
	}
	
	/**
	 * 密码单向加密
	 *
	 * @param string $username
	 *        	用户名
	 * @param string $pass
	 *        	密码
	 * @return string
	 */
	public function encodePass($username, $pass) {
		$salt = md5 ( 'liuguang' ) . $username;
		return md5 ( md5 ( $salt . $pass ) . $salt );
	}
	
	/**
	 * 添加一个用户账号
	 *
	 * @param string $username        	
	 * @param string $nickname        	
	 * @param string $pass        	
	 * @param string $email        	
	 * @return int 返回用户id,失败则返回-1
	 */
	public function addAccount($username, $nickname, $pass, $email) {
		if ($this->isUsernameExists ( $username )) {
			$this->errMsg = '用户名' . $username . '已存在';
			return - 1;
		}
		if ($this->isEmailExists ( $email )) {
			$this->errMsg = '邮箱' . $email . '已被使用';
			return - 1;
		}
		if ($this->dbType == BaseController::DB_MYSQL)
			return $this->addAccountM ( $username, $nickname, $pass, $email );
		elseif ($this->dbType == BaseController::DB_REDIS)
			return $this->addAccountR ( $username, $nickname, $pass, $email );
	}
	
	/**
	 * 添加一个用户账号
	 *
	 * @param string $username        	
	 * @param string $nickname        	
	 * @param string $pass        	
	 * @param string $email        	
	 * @return int 返回用户id,失败则返回-1
	 */
	private function addAccountM($username, $nickname, $pass, $email) {
	}
	
	/**
	 * 添加一个用户账号
	 *
	 * @param string $username        	
	 * @param string $nickname        	
	 * @param string $pass        	
	 * @param string $email        	
	 * @return int 返回用户id,失败则返回-1
	 */
	private function addAccountR($username, $nickname, $pass, $email) {
		$lua = '-- 检测用户名是否被使用
		local checkExists=redis.call(\'hexists\',\'' . $this->tablePre . 'username_uids\',ARGV[1])
		if checkExists~=0 then
			return -1
		end
		-- 检测email是否被使用
		checkExists=redis.call(\'hexists\',\'' . $this->tablePre . 'email_uids\',ARGV[4])
		if checkExists~=0 then
			return -1
		end
		local uid=redis.call(\'hincrby\',\'' . $this->tablePre . 'counter\',\'uid\',1)
		redis.call(\'hset\',\'' . $this->tablePre . 'username_uids\',ARGV[1],uid)
		redis.call(\'hset\',\'' . $this->tablePre . 'email_uids\',ARGV[4],uid)
		redis.call(\'hmset\',\'' . $this->tablePre . 'uid:\'..uid..\':userinfo\',\'uid\',uid,\'username\',ARGV[1],\'nickname\',ARGV[2],\'pass\',ARGV[3],\'email\',ARGV[4],\'regtime\',ARGV[5],\'usersign\',\'\',\'user_img\',ARGV[6],\'lastlogin\',0)
		return uid';
		return $this->redis->eval ( $lua, array (
				$username,
				$nickname,
				$this->encodePass ( $username, $pass ),
				$email,
				time (),
				$this->userImg 
		) );
	}
	/**
	 * 添加超级管理员权限
	 *
	 * @param int $uid
	 *        	用户id
	 * @return void
	 */
	public function addSuperAdmin($uid) {
		if ($this->dbType == BaseController::DB_MYSQL)
			$this->addSuperAdminM ( $uid );
		elseif ($this->dbType == BaseController::DB_REDIS)
			$this->addSuperAdminR ( $uid );
	}
	/**
	 * 添加超级管理员权限
	 *
	 * @todo
	 *
	 * @param int $uid
	 *        	用户id
	 * @return void
	 */
	private function addSuperAdminM($uid) {
	}
	/**
	 * 添加超级管理员权限
	 *
	 * @param int $uid
	 *        	用户id
	 * @return void
	 */
	private function addSuperAdminR($uid) {
		$tablePre = $this->tablePre;
		$this->redis->zAdd ( $tablePre . 'superadmins', time (), $uid );
	}
	/**
	 * 检测一个用户是否为网站管理员
	 *
	 * @param int $uid
	 *        	用户id
	 * @return boolean
	 */
	public function isSuperAdmin($uid) {
		if ($this->dbType == BaseController::DB_MYSQL)
			return $this->isSuperAdminM ( $uid );
		elseif ($this->dbType == BaseController::DB_REDIS)
			return $this->isSuperAdminR ( $uid );
	}
	/**
	 * 检测一个用户是否为网站管理员
	 *
	 * @todo
	 *
	 * @param int $uid
	 *        	用户id
	 * @return boolean
	 */
	private function isSuperAdminM($uid) {
	}
	
	/**
	 * 检测一个用户是否为网站管理员
	 *
	 * @param int $uid
	 *        	用户id
	 * @return boolean
	 */
	private function isSuperAdminR($uid) {
		$tablePre = $this->tablePre;
		return ($this->redis->zScore ( $tablePre . 'superadmins', $uid ) !== false);
	}
	/**
	 * 验证用户名密码
	 *
	 * @param string $username        	
	 * @param string $pass        	
	 * @return int 失败时返回-1,正确时返回用户id
	 */
	public function authPass($username, $pass) {
		$encodedPass = $this->encodePass ( $username, $pass );
		if ($this->dbType == BaseController::DB_MYSQL)
			return $this->authPassM ( $username, $encodedPass );
		elseif ($this->dbType == BaseController::DB_REDIS)
			return $this->authPassR ( $username, $encodedPass );
	}
	/**
	 *
	 * @todo
	 *
	 */
	private function authPassM($username, $pass) {
	}
	private function authPassR($username, $pass) {
		$redis=$this->redis;
		$tablePre=$this->tablePre;
		$lua='-- 获取用户id
		local uid=redis.call(\'hget\',ARGV[1]..\'username_uids\',ARGV[2])
		if uid == false then
			return -1
		end
		local pass=redis.call(\'hget\',ARGV[1]..\'uid:\'..uid..\':userinfo\',\'pass\')
		if pass == ARGV[3] then
			return uid
		else
			return -1
		end';
		return $redis->eval($lua,array($tablePre,$username,$pass));
	}
}