<?php

namespace App\Models;
use PDO; //korzystamy z biblioteki PDO
use \App\Token;
use \App\Mail;
use \Core\View;

class User extends \Core\Model {
	
	//tablica do przechowywania błędów walidacji formularza
	public $errors = [];
	
	//konstruktor który zamieni tablicę z danymi z formularza na tablicę asocjacyjną
	//do argumentu dodaliśmy zapis $data = [] po to aby program przyjął DOMYŚLNIE
	//pusta tablice, jeżeli nie zostanie przekazany żadny argument
	public function __construct($data = []) {
		foreach ($data as $key => $value) {
			$this->$key = $value;
		};
	}
			
	//funckja dokonująca walidacji podanych danych w formularzu
	public function validate(){
		//walidacja name - pole wymagane
		if($this->name == '') {
			$this->errors[] = 'Name is required';
		}
		
		//walidacja adresu email
		if(filter_var($this->email, FILTER_VALIDATE_EMAIL)==false){
			$this->errors[] = 'Invalid email address';
		}
		
		//walidacja adresu email - czy nie jest juz zajęty
		if($this->emailExists($this->email, $this->id ?? null)) {
			$this->errors[] = 'email already taken';
		}
		//walidacja hasła - wykonujemy ją przy rejestracji oraz przy jego zmianie
		
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
	
	//funkcja zapisująca w bazie danych dane z formularza
	public function save() {
		
		//wywołujemy funckję dokonująca walidacji
		$this->validate();
		
		//po wywołaniu funckji validate jeśli tablica błędów jest pusta dodajemy rekord do bazy
		if (empty($this->errors)) {
			
			//hashowanie hasła przed umieszzeniem go w bazie danych
			$password_hash = password_hash($this->password, PASSWORD_DEFAULT);
			
			//tworzymy nowy TOKEN który posłuży nam do aktywacji konta wiadomością email
			$token = new Token();
			$hashed_token = $token->getHash();
			$this->activation_token = $token->getValue();
			
			//zapytanie sql wkładające rekordy do bazy
			$sql = 'INSERT INTO users (name, email, password_hash, activation_hash)
					VALUES (:name, :email, :password_hash, :activtion_hash)';
			$db = static::getDB();
			
			//umieszczenie rekordów w bazie 3 etapowo: prepare - bind - execute
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
			$stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
			$stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
			$stmt->bindValue(':activtion_hash', $hashed_token, PDO::PARAM_STR);
			
			return $stmt->execute();
		}		
		return false;
	}
	
	//funckja sprawdzająca czy email nie jest już zajęty przez innego użytkownika
	public static function emailExists ($email, $ignore_id=null) {
		$user = static::findByEmail($email);
		
		if($user) {
			if($user->id != $ignore_id) {
				return true;
			}
		}
		return false;
	}
	
	//funckaj sprawdzająca czy podany adres email jest już w bazie
	public static function findByEmail ($email) {
		$sql = 'SELECT * FROM users WHERE email = :email';
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		$stmt->bindParam(':email', $email, PDO::PARAM_STR);
		
		//zmieniamy właściwość z biblioteki PDO tak aby otrzymać w wyniku nie tablicę asocjacyjną a obiekt
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
		
		$stmt->execute();
		
		return $stmt->fetch();
	}
	
	//funckja wykonująca sprawdzenie czy podane hasło pasuje do adresu email
	public static function authenticate($email, $password) {
		$user = static::findByEmail($email);
		
		//jeżeli w bazie danych znaleziono rekord z podanym adresem email wykonamy sprawdzenie hasła
		if ($user && $user->is_active) {
			if (password_verify($password, $user->password_hash)) {
				return $user;
			}
		}
		return false;
		}
		
	//funckja wyszukująca dane użytkownika na podstawie numeru ID	
	public static function findByID($id) {
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }
	
	//funkcja do zapamiętywania loginu po tym jak użytkownik
	//zaznaczy opcję Remember me
	public function rememberLogin() {
		
		//tworzymy nowy obiekt generujący token
		$token = new Token();		
		$hashed_token = $token->getHash();
		$this->remember_token = $token->getValue();
		
		//zakładamy że login będzie zapamiętany przez 30 dni
		$this->expiry_timestamp = time() + 60 * 60 * 24 * 30;
		
		//dodajemy do bazy nowy rekord zawierający zahashowany token
		//numer id usera i datę wygaśnięcia zapamiętanego loginu
		$sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at) VALUES (:token_hash, :user_id, :expires_at)';
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
		$stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);
		
		return $stmt->execute();		
	}
	
	
	//funkcja do resetowania hasła jeżeli użytkownik go zapomni
	public static function sendPasswordReset($email) {
		$user = static::findByEmail($email);
		
		if ($user) {
			if($user->startPasswordReset()) {
				$user->sendPasswordResetEmail();
			}
		}
	}
	
	public function startPasswordReset() {
		$token = new Token();
		$hashed_token = $token->getHash();
		$this->password_reset_token = $token->getValue();
		
		$expiry_timestamp = time() + 60 * 60 * 2; //aktywne przez 2h
		
		$sql = 'UPDATE users 
				SET password_reset_hash = :token_hash,
					password_reset_expires_at = :expires_at
				WHERE id=:id';
				
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
		$stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $expiry_timestamp), PDO::PARAM_STR);
		$stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
		
		return $stmt->execute();
	}
	
	//funckja wysyłająca użytkownikowi wiaodmość email z instrukcją resetowania hasła
	protected function sendPasswordResetEmail() {
		$url = 'http://' . $_SERVER['HTTP_HOST'] . '/password/reset/' . $this->password_reset_token;
		
		$text = View::getTemplate('Password/reset_email.txt', ['url' => $url]);
		$html = View::getTemplate('Password/reset_email.html', ['url' => $url]);
		
		Mail::send($this->email, 'Password reset', $text, $html);
	}
	
	public static function findByPasswordReset($token) {
		$token = new Token($token);
		$hashed_token = $token->getHash();
		
		$sql = 'SELECT * FROM users 
				WHERE password_reset_hash = :token_hash';
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
		
		$stmt->execute();
		
		$user = $stmt->fetch();

		if($user) {
					
			//sprawdzimy czy wysłany link nie wygasł i nadal możliwa jest zmoana hasła
			if (strtotime($user->password_reset_expires_at) > time()) {
				return $user;
			}
		}
	}
	
	public function resetPassword($password) {
		
		$this->password = $password;
		$this->validate();
	
		if(empty($this->errors)) {
			$password_hash = password_hash($this->password, PASSWORD_DEFAULT);
			
			$sql = 'UPDATE users
					SET password_hash = :password_hash,
						password_reset_hash = NULL,
						password_reset_expires_at = NULL
					WHERE id=:id';
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
			$stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
			
			return $stmt->execute();
		}
		return false;
	}
	
	//funckja wysyłająca link aktywacyjny
	public function sendActivationEmail() {
		$url = 'http://' . $_SERVER['HTTP_HOST'] . '/signup/activate/' . $this->activation_token;
		
		$text = View::getTemplate('Signup/activation_email.txt', ['url' => $url]);
		$html = View::getTemplate('Signup/activation_email.html', ['url' => $url]);
		
		Mail::send($this->email, 'Account activation', $text, $html);
	}
	
	public static function activate($value) {
		$token = new Token($value);
		$hashed_token = $token->getHash();
		
		$sql = 'UPDATE users
				SET is_active = 1,
					activation_hash = null
				WHERE activation_hash = :hashed_token';
				
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);
		
		$stmt->execute();
	}
	
	public static function getUserByTokenActivate($value) {
		$token = new Token($value);
		$hashed_token = $token->getHash();
		
		$sql = 'SELECT * FROM users 
				WHERE activation_hash = :hashed_token';
				
		$db = static::getDB();
		$stmt = $db->prepare($sql);
		
		$stmt->bindValue(':hashed_token', $hashed_token, PDO::PARAM_STR);
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
		$stmt->execute();
		
		return $stmt->fetch();
	}
	



		/*
	//funkcja wykonująca aktualizację danych w bazie danych
	public function updateProfile($data) {
		$this->name = $data['name'];
		$this->email = $data['email'];
		
		//walidacja hasła TYLKO jeżeli je zmieniamy
		if($data['password'] != '') {
			$this->password = $data['password'];
		}
		
		$this->validate();
		
		if (empty($this->errors)) {
			$sql = 'UPDATE users
					SET name=:name, 
					email=:email';

			//dodajemy hasło tylko jeżeli jest aktualizowane
			if(isset($this->password)) {
				$sql .= ',	password_hash=:password_hash';
			}
			
			$sql .="\n WHERE id=:id";
					
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);
			
			$stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
			$stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
			$stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
			
			//dodajemy hasło tylko jeżeli jest ustanowione
			if(isset($this->password)) {
				$password_hash = password_hash($this->password, PASSWORD_DEFAULT);
				$stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
			}
			
			return $stmt->execute();
		}
		return false;
	}*/
}
?>
