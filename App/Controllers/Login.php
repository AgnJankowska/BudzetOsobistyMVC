<?php

namespace App\Controllers;
use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;


class Login extends \Core\Controller {
	
	//funckja do wyświetlania widoku
	public function newAction() {
		View::renderTemplate('Login/new.html');
	}
	
	//funckja do sprawdzenia czy podany email i hasło sa poprawne - wywołujemy metodę z modelu User
	public function createAction() {
		$user = User::authenticate($_POST['email'], $_POST['password']);
		
		$remember_me = isset($_POST['remember_me']);
		
		//jeżeli podane hasło pasuje do adresu email - przekierowanie na stronę
		if ($user) {
			
			//wywołujemy metodę z klasy do dostepności żeby sprawdzić czy jestesmy zalogowani
			Auth::login($user, $remember_me);
			
			//dodanie wiadomości flash, typu domyslnego SUCCESS
			Flash::addMessage('Logowanie powiodło się');
			
			//korzystamy z metody z klasy głownej Controller do przekierowywania na strony - domyslnie jest
			//to storna główna ale może to być także strona
			//na którą chciał wejść nieuprawniony użytkownik
			//przed zalogowaniem
			$this->redirect(Auth::getReturnToPage());
		}
		else {
			//dodanie wiadomości flash, typu WARNING
			Flash::addMessage('Logowanie nieudane, spróbuj jeszcze raz', Flash::WARNING);			
			
			//jeśli podane hasło jest błędne wracamy do strony logowania
			//z zapamiętanym adresem email który przesyłamy w tablicy
			View::renderTemplate('Login/new.html', [
				'email' => $_POST['email'],
			]);
		}
	}
	
	//funckja która uzyjemy przy wylogowywaniu która usunie całą sesję
	public function destroyAction() {
		
		//wywołujemy metodę z klasy dostepności służąca do wywlogowywania
		Auth::logout();
		
		$this->redirect('/login/show-logout-message');
	}
	
	//metoda potrzebna do wyświetlenia wiadomości flash przy wylogowywaniu
	//potrzebujemy jej poniewaz wylogowanie usuwa sesję więc 
	//zwykłe wyświetlenie wiadomości się nie uda
	public function showLogoutMessageAction() {
		//dodanie wiadomości flash typu domyślnego SUCCESS
		Flash::addMessage('Logowanie powiodło się');	
		
		//na koniec przekierowujemy do strony logowania
		$this->redirect('/');
	}
	
}

