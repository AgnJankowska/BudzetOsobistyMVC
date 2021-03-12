<?php

namespace App\Controllers;
use \Core\View;
use \App\Auth;
use \App\Flash;

//klasa do której dostęp mają tylko zalogowani użytkownicy
class Profile extends Authenticated {
	
	protected function before() {
		parent::before();
		
		$this->user = Auth::getUser();
	}
	
	//metoda odpowiedzialna za wyświetlenie pliku widoku
	//oraz pobranie danych zalogowanego użytkownika z klasy Auth
	//pobrany obiekt przekazujemy do pliku widoku
	public function showAction() {
		
		View::renderTemplate('Profile/show.html', [
			'user' => $this->user
		]);
	}
	
	//metoda do edycji danych użytkownika
	public function editAction() {
		
		View::renderTemplate('Profile/edit.html', [
			'user' => $this->user
		]);
	}
	
	//metoda do wysłania danych z formularza
	public function updateAction() {

		if($this->user->updateProfile($_POST)) {		
			Flash::addMessage('Changes saved');
			$this->redirect('/profile/show');
		}
		else {
			View::renderTemplate('Profile/edit.html', [
				'user' => $this->user
			]);
		}
	}
	
}