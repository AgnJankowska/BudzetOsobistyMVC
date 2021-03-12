<?php

namespace App\Models;
use PDO;
use App\Token;

//klasa dzięki której po 1 logowaniu i zaznaczeniu opcji Remember me nie treba się ponownie logować nawet po zamknięciu przeglądarki
class RememberedLogin extends \Core\Model {
	
	//funkcja łącząca się z bazą danych i pobierająca dane do logowania na podstawie tokenu
	public static function findByToken($token) {
		$token = new Token($token);
		$token_hash = $token->getHash();
		
		$sql = 'SELECT * FROM remembered_logins WHERE token_hash = :token_hash';
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':token_hash', $token_hash, PDO::PARAM_STR);
		
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
		
		$stmt->execute();
		
		return $stmt->fetch();
	}
	
	public function hasExpired() {
        return strtotime($this->expires_at) < time();
    }
	
	//funckja która zwraca obiekt User dla zapamiętanego loginu
	public function getUser() {
		return User::findByID($this->user_id);
	}
	
	//funkcja któa umożliwi wylogowanie - usunie token z bazy danych
    public function delete() {
        $sql = 'DELETE FROM remembered_logins
                WHERE token_hash = :token_hash';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':token_hash', $this->token_hash, PDO::PARAM_STR);

        $stmt->execute();
    }
}