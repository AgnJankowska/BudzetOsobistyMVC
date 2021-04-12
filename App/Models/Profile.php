<?php

namespace App\Models;
use PDO;
use App\Token;
use App\Auth;

class Profile extends \Core\Model {
	
	public function ucfirstUtf8($str) {
		$in =  mb_strtolower($str,"utf8");
		$out = mb_strtoupper(mb_substr($in, 0, 1)).mb_substr($in, 1);
		return $out;
	}

	public static function updateName($data, $user) {	
		$profile = new Profile();
		$newName = $profile->ucfirstUtf8($data['newName']);
	
		$sql = 'UPDATE users SET name=:newName WHERE id=:id';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':newName', $newName, PDO::PARAM_STR);
		$stmt->bindValue(':id', $user->id, PDO::PARAM_STR);
				
		return $stmt->execute();
	}
	
	public static function updatePassword($data, $user) {

		$password = $data['password'];
		$password_hash = password_hash($password, PASSWORD_DEFAULT);
		
		$profile = new Profile();
		$profile->validate();
		
		if (empty($user->errors)) {
			$sql = 'UPDATE users SET password_hash=:password_hash WHERE id=:id';
						
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':id', $user->id, PDO::PARAM_STR);
			$stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
						
			return $stmt->execute();
		}
		return false;
	}
	
	public function validate(){

		if(isset($this->password)) {
			//walidacja długości hasła min 6 znaków
			if(strlen($this->password) < 6) {
				$this->errors[] = 'Please enter at least 6 characters';
			}
			
			//walidacja hasła - minimum jedna litera
			if (preg_match('/.*[a-z]+.*/i', $this->password) == 0) {
				$this->errors[] = 'Password needs at least one letter';
			}
			
			//walidacja hasła - minimum jedna cyfra
			if (preg_match('/.*\d+.*/i', $this->password) == 0) {
				$this->errors[] = 'Password needs at least one number';
			}
		}			
	}
}
