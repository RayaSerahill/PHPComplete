<?php

namespace App;

class registerHandler {
	private $pdo;
	
	public function __construct($pdo) {
		$this->pdo = $pdo;
	}
	public function newUser($email, $name, $password, $password_again) {
		$error = false;
		$list = array();
		if (!isset($email) ||!isset($name) || !isset($password) || !isset($password_again)) {$error = true; $list["success"] = "false";$list[] = "You must fill all fields";}
		if (empty($email) ||empty($name) || empty($password) || empty($password_again)) {if (!$error) {$list["success"] = "false"; $list[] = "You must fill all fields";}}
		if (!$this->email_validation($email)) {$list["success"] = "false"; $list[] = "Invalid email address";}
		if (!$this->name_validation($name)) {$list["success"] = "false"; $list[] = "Username must include at least 1 letter and be 3-15 characters long";}
		$pass = $this->passwd_validation($password, $password_again);
		if ($pass !== true) {$list["success"] = "false";foreach ($pass as $error) {$list[] = $error;}}
		$res = (new SQLiteUtils($this->pdo))->isUnique($email, $name);
		if ($res !== true){foreach ($res as $error) {$list["success"] = "false";$list[] = $error;}}
		if (!array_key_exists("success", $list)) {$list["success"] = "true"; $password = hash_hmac("sha256", $password, Config::PEPPER); $password = password_hash($password, PASSWORD_BCRYPT, array('cost'=>12)); (new SQLiteUtils($this->pdo))->addUser($email, $name, $password);}
		return $list;
	}
	
	function email_validation($str) {return (!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $str))? FALSE : TRUE;}
	function name_validation($str) {return (!preg_match("/^[A-Za-z][A-Za-z0-9]{2,14}$/", $str))? FALSE : TRUE;}
	function passwd_validation($pass, $again) {
		$errors = array();
		if (strlen($pass) < 8 || strlen($pass) > 32) {$errors[] = "Password should be min 8 characters and max 32 characters";}
		if (!preg_match("/\d/", $pass)) {$errors[] = "Password should contain at least one digit";}
		if (!preg_match("/[A-Z]/", $pass)) {$errors[] = "Password should contain at least one Capital Letter";}
		if (!preg_match("/[a-z]/", $pass)) {$errors[] = "Password should contain at least one small Letter";}
		if (!preg_match("/\W/", $pass)) {$errors[] = "Password should contain at least one special character";}
		if (preg_match("/\s/", $pass)) {$errors[] = "Password should not contain any white space";}
		if ($pass !== $again) {$errors[] = "Passwords don't match";}
		if ($errors) {return $errors;} else {return true;}
	}
}