<?php

namespace App\Controllers;
use \Core\View;
use \App\Auth;
use \App\Models\Tables;
use \App\Models\Incomes;

class Income extends Authenticated {

	protected $added_income;
	
	protected function before() {
		parent::before();
		$this->added_income = false;
	}
	
	public function addAction() {
		$incomes = new Incomes($_POST);
		
		if($incomes->addIncome(Auth::getUser())) {		
			//dodanie wydatku do bazy
			$this->added_income = true;
			$this->newAction();
		}
			
		else {
			//ponowne wyświeltenie pliku widoku new.html
			$this->new();
		}
	}
		
	public function newAction() {
		
		$incomesCategory = Tables::getIncomeCategory(Auth::getUser());

		View::renderTemplate('Income/new.html', [
			'inc' => $incomesCategory,
			'added_income' => $this->added_income
		]);
	}
}