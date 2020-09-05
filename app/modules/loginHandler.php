<?php

namespace App;

class loginHandler {
	private $pdo;
	
	public function __construct($pdo) {
		$this->pdo = $pdo;
	}
	
	public function rememberMe() {
		if (!isset($_COOKIE['TyNUqeZfAaE'])) {return false;}
		if (isset($_SESSION["id"])) {return false;}
		if (strlen($_COOKIE['TyNUqeZfAaE']) < 125) {return false;}
		$token = substr($_COOKIE['TyNUqeZfAaE'], 0, 124);
		$valid = str_replace($token,"",$_COOKIE['TyNUqeZfAaE']);
		$stmt = $this->pdo->prepare('SELECT userid, expires, validator FROM auth_tokens WHERE selector = :token');
		$stmt->execute([':token' => $token]);
		if ($data = $stmt->fetch()) {
			if (!password_verify($valid, $data["validator"])) {setcookie('remember_user', null, -1, '/'); return false;}
			if (time() > $data["expires"]) {setcookie('remember_user', null, -1, '/'); $this->deleteToken($token); return false;}
			$_SESSION["id"] = $data["userid"];
			$time = time()+(60*60*24*30);
			$sql = $this->pdo->prepare('UPDATE auth_tokens SET expires = :time WHERE selector = :token');
			$sql->execute([':time' => $time, ':token' => ''.$token]);
			return true;
		}
		return false;
	}
	
	function deleteToken($token) {
		$stmt = $this->pdo->prepare('DELETE FROM auth_tokens WHERE selector = :token');
		$stmt->execute([':token' => $token]);
	}
	
	public function isLogged() {
		if ($this->rememberMe()) {return false;}
		if (isset($_SESSION["id"])) {
			$list["success"] = "true";
			$list[] = "You are already logged in";
			return $list;
		}
		return false;
	}
	
	public function login($mail, $passwd, $rme = false) {
		if (isset($_SESSION["id"])) {
			$list["success"] = "false";
			$list[] = "You are already logged in";
			return $list;
		}
		if ($this->rememberMe()) {return true;}
		$list = array();
		$res = (new SQLiteUtils($this->pdo))->loginAuth($mail, $passwd);
		if (!$res) {$list["success"] = "false";$list[] = "Password incorrect";}
		else if ($res === "The email you've entered doesn't match any account.") {$list["success"] = "false";$list[] = $res;}
		else {$_SESSION["id"] = $res;$list["success"] = "true";}
		if ($list["success"] === "true" && $rme === true) {
			$token = $this->generateToken();
			$valid = $this->generateToken();
			$cookie = $token . $valid;
			setcookie("TyNUqeZfAaE", $cookie, time()+(60*60*24*30), '/', 'localhost', false, false);
			$time = time()+(60*60*24*30);
			$valid = password_hash($valid, PASSWORD_BCRYPT, array('cost'=>12));
			$stmt = $this->pdo->prepare('INSERT INTO auth_tokens(selector, validator, userid, expires) VALUES(:selector, :token, :userid, :expires)');
			$stmt->execute([':selector' => $token,':token' => $valid,':userid' => $rme,':expires' => $time,]);
			return true;
		}
		if ($list["success"] === "true") { return true; }
		return $list;
	}

	function generateToken($length = 62) {
		return bin2hex(random_bytes($length));
	}
}