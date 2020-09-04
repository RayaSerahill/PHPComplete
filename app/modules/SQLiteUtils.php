<?php

namespace App;

class SQLiteUtils {
	private $pdo;
	
	public function __construct($pdo) {
		$this->pdo = $pdo;
	}
	
	public function createTables() {
		$commands = ['CREATE TABLE IF NOT EXISTS users (id INTEGER PRIMARY KEY AUTOINCREMENT, mail TEXT NOT NULL, name TEXT NOT NULL, passwd TEXT NOT NULL)'];
		foreach ($commands as $command) {
			$this->pdo->exec($command);
		}
		return true;
	}
	
	public function addUser($mail, $name, $passwd) {
		$sql = 'INSERT INTO users(mail, name, passwd) VALUES(:mail, :name, :passwd)';
		$stmt = $this->pdo->prepare($sql);
		$stmt->execute([':mail' => $mail,':name' => $name,':passwd' => $passwd,]);
		return true;
	}
	
	public function isUnique($mail, $username) {
		$mail_sql = $this->pdo->prepare('SELECT count(*) FROM users WHERE mail = :mail');
		$name_sql = $this->pdo->prepare('SELECT count(*) FROM users WHERE name = :name');
		$mail_sql->execute([':mail' => $mail]);
		$name_sql->execute([':name' => $username]);
		$list = array();
		if ($mail_sql->fetchColumn() > 0) {$list[] = "That email address is already in use";}
		if ($name_sql->fetchColumn() > 0) {$list[] = "That username is not available";}
		if ($list) {return $list;} else {return true;}
	}
}