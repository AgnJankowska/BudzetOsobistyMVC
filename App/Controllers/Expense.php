<?php

namespace App\Controllers;
use \Core\View;
use \App\Auth;
use \App\Models\Tables;
use \App\Models\Expenses;

class Expense extends Authenticated {
	
	protected function before() {
		parent::before();
		$this->user = Auth::getUser();
		unset($_SESSION['added_expense']);
	}
	
	public function newAction() {
		$payMethod = Tables::getPayMethods($this->user);
		$expensesCategory = Tables::getExpenseCategory($this->user);

		View::renderTemplate('Expense/new.html', [
			'pay' => $payMethod,
			'exp' => $expensesCategory	
		]);
	}
	
	public function addAction() {
		$expenses = new Expenses($_POST);
		
		if($expenses->addExpense($this->user)) {		
			//dodanie wydatku do bazy
			$_SESSION['added_expense'] = true;	
			$this->newAction();
		}
			
		else {
			//ponowne wyświeltenie pliku widoku new.html
			$this->new();
		}
	}
}
