<?php
namespace App\Controllers;

//zapis skrócony z użyciem use
use \Core\View;
use \App\Auth;

class Home extends \Core\Controller
{
	//odziedziczona funkcja before
	protected function before() {
		$this->user = Auth::getUser();
	}
	
	public function indexAction() {

		if($this->user) {		
			View::renderTemplate('Home/index.html', [
				'user' => $this->user
			]);
		}
			
		else {
			View::renderTemplate('Home/index.html');
		}
	}
}
?>
