<?php

namespace App\Controllers;
use \Core\View;
use \App\Auth;
use \App\Models\Tables;
use \App\Models\Expenses;

class Expense extends Authenticated {
	
	protected $added_expense;
	
	protected function before() {
		parent::before();
		$this->added_expense = false;
	}
	
	public function addAction() {
		$expenses = new Expenses($_POST);
		
		if($expenses->addExpense(Auth::getUser())) {		
			//dodanie wydatku do bazy
			$this->added_expense = true;
			$this->newAction();
		}
			
		else {
			//ponowne wyświeltenie pliku widoku new.html
			$this->new();
		}
	}
		
	public function newAction() {
		
		$payMethod = Tables::getPayMethods(Auth::getUser());
		$expensesCategory = Tables::getExpenseCategory(Auth::getUser());

		View::renderTemplate('Expense/new.html', [
			'pay' => $payMethod,
			'exp' => $expensesCategory,
			'added_expense' => $this->added_expense
		]);
	}
}
