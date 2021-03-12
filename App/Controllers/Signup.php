<?php
namespace App\Controllers;

//zapis skrócony z użyciem use
use \Core\View;
use \App\Models\User;
use \App\Models\Tables;

class Signup extends \Core\Controller
{	
	public function newAction() {
    //załadowanie pliku widoku 
        View::renderTemplate('Signup/new.html');
    }
	
	public function createAction() {
		//przekazujemy do modelu User.php dane z formularza (tablice $_POST) po to aby zapisał je w bazie danych
		$user = new User($_POST);
		
		//instrukcja co zrobić jeżeli walidacja przebiegła poprawnie
		if($user->save()) {		
			//wysłanie maila aktywującego
			$user->sendActivationEmail();
						
			//wyświeltenie pliku widoku success.html - z wykorzystaniem metody z głownej klasy Controller
			$this -> redirect('/signup/success');
		}
		
		//instrukcja co zrobić jeżeli funckja save zwróciła false (są błędy walidacji formularza)
		else {
			//ponowne wyświeltenie pliku widoku new.html
			View::renderTemplate('Signup/new.html', [
				'user' => $user
			]);
		}
    }
	
	public function successAction() {
		View::renderTemplate('Signup/success.html');
	}
	
	//funckja która wywołuje metodę activate z klasy User, która dokonuje zmiany 
	//parametru is_active na 1, oraz zeruje wartość activation_hash
	public function activateAction() {
		$user = User::getUserByTokenActivate($this->route_params['token']);
		User::activate($this->route_params['token']);
		
		Tables::copyDefaultIncomes($user);
		Tables::copyDefaultExpenses($user);
		Tables::copyDefaultPaymentMethod($user);
		
		$this->redirect('/signup/activated');
	}
	
	//funckja wyświetlająca plik widoku po udanej aktywacji
	public function activatedAction() {
		
		View::renderTemplate('Signup/activated.html');
	}
	
}
?>