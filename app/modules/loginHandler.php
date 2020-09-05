<?php

namespace App;

class loginHandler {
	private $pdo;
	
	public function __construct($pdo) {
		$this->pdo = $pdo;
	}
	
	public function login($mail, $passwd) {
		$list = array();
		$res = (new SQLiteUtils($this->pdo))->loginAuth($mail, $passwd);
		if (!$res) {$list["success"] = "false";$list[] = "Password incorrect";}
		else {$_SESSION["id"] = $res;$list["success"] = "true";}
		return $list;
	}
}