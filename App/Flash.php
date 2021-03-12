<?php

namespace App;

class Flash {
	
	//wiadomości flash będą reprezentować 3 grupy: success
	//warning i info, będą się różnić swoim stylem w CSS
	const SUCCESS = 'success';
	const INFO = 'info';
	const WARNING = 'warning';
	
	//metoda dodająca treśc wiaodmości do zmiennej sesyjnej 
	//w postaci tablicy
	//ponieważ chcemy wiadomośc flash były różne pod względem //treści i stylu dodajemy dodatkową zmienną type, która domyslnie
	//ustawiona jest na success, ale douszczamy inne type jak 
	//warning i info
	public static function addMessage($message, $type='success') {
		if(! isset($_SESSION['flash_notification'])) {
			$_SESSION['flash_notification'] = [];
		}
		
		//dodanie wiaodmości do tablicy 
		$_SESSION['flash_notification'][] = [
			'body' => $message,
			'type' => $type
			];
	}
	
	//metoda do pobierania wcześniej dodanych wiadomości 
	//do zmiennej sesyjnej
	public static function getMessages() {
		if(isset($_SESSION['flash_notification'])) {
			$messages = $_SESSION['flash_notification'];
			unset($_SESSION['flash_notification']);
			
			return $messages;
		}
	}
}