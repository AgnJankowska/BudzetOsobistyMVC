<?php

namespace App\Controllers;
use \Core\View;
use \App\Auth;
use \App\Models\Tables;
use \App\Models\Incomes;

class Income extends Authenticated {
	
	protected function before() {
		parent::before();
		$this->user = Auth::getUser();
		unset($_SESSION['added_income']);
	}
	
	public function newAction() {
		$incomesCategory = Tables::getIncomeCategory($this->user);

		View::renderTemplate('Income/new.html', [
			'inc' => $incomesCategory	
		]);
	}
	
	public function addAction() {
		$incomes = new Incomes($_POST);
		
		if($incomes->addIncome($this->user)) {		
			//dodanie wydatku do bazy
			$_SESSION['added_income'] = true;	
			$this->newAction();
		}
			
		else {
			//ponowne wyświeltenie pliku widoku new.html
			$this->new();
		}
	}
}