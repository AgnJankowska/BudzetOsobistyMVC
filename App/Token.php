<?php
namespace App;

//klasa stworzona w celu wygenerowania bezpiecznego tokena którego użyjemy do tego aby użytkownik mógł być zapamiętany po pierwszym zalogowaniu
class Token {
	
	protected $token;
	
	//konstruktor klasy - generuje losowy numer token
	public function __construct($token_value=null) {
		if($token_value) {
			$this->token = $token_value;
		}
		else {
		$this->token = bin2hex(random_bytes(16));
		}
	}
	
	//metoda do zwracania wartości wygenerowanego tokena
	public function getValue() {
		return $this->token;
	}
	
	//hashowanie wygenerowanego tokena w celach bezpieczeństwa
	//1 argument to rodzaj hashowania
	//2 argument to hashowany string
	//3 argument to opcja dodatkowego wzmocnienia zabezpiecznia
	public function getHash() {
		return hash_hmac('sha256', $this->token, \App\Config::SECRET_KEY);
	}	
}