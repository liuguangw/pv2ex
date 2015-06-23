<?php

namespace liuguang\pv2ex\model;

use liuguang\mvc\ErrHandler as ErrInter;

class ErrHandler implements ErrInter{
	/**
	 * 错误处理
	 * 
	 * @see \liuguang\mvc\ErrHandler::handle()
	 */
	public function handle($code, $msg) {
		header ( 'Content-Type: text/html; charset=utf-8' );
		echo '<html>
<head><title>', $code, ' Error</title></head>
<body bgcolor="white">
<center><h1>', $code, ' ', $msg, '</h1></center>
<hr><center>liuguang/pv2ex</center>
</body>
</html>';
		exit ();
	}
}