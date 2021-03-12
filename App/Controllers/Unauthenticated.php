<?php

namespace App\Controllers;

abstract class Unauthenticated extends \Core\Controller {
	
	//zakładamy że każda strona wyświetlana przez ten 
	//kontroler jest dostepna tylko dla zalogowanych użytkowników
	protected function before() {
		$this->requireLogout();
	}
	
}