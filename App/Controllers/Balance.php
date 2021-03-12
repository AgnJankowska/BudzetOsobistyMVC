<?php

namespace App\Controllers;
use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Expenses;
use \App\Models\Incomes;

class Balance extends Authenticated {
	
	protected function before() {
		parent::before();
		$this->user = Auth::getUser();
		unset($_SESSION['range']);
	}

	public function indexAction() {

		$_SESSION['range'] = filter_input(INPUT_POST, 'range');		
		$this->checkGivenDate();
	
		$expense_sum = Expenses::getExpenseAssignetToUser($this->user, $this->date_start, $this->date_end);
		$income_sum = Incomes::getIncomeAssignetToUser($this->user, $this->date_start, $this->date_end);
		$savings = $income_sum - $expense_sum;
		
		$expense_sum_format = number_format($expense_sum ,2, '.', ' ');
		$income_sum_format = number_format($income_sum ,2, '.', ' ');
		$savings_format = number_format($savings ,2, '.', ' ');
		
		View::renderTemplate('Balance/index.html', [
			'date_start' => $this->date_start,
			'date_end' => $this->date_end,
			'expense_sum' => $expense_sum_format,
			'income_sum' => $income_sum_format,
			'savings' => $savings_format
		]);
		
		$_SESSION['range'] = true;
		unset($_SESSION['showed']);
	}
	
	public function checkGivenDate() {
		$this->date_start = filter_input(INPUT_POST, 'date_start');
		$this->date_end = filter_input(INPUT_POST, 'date_end');
		
		if(($this->date_start) > ($this->date_end))	{
			Flash::addMessage('Nieprawidłowy zakres dat', Flash::WARNING);
		}
	}
	
	public function form() {
		$_SESSION['showed'] = true;
		$this->index();
	}

	public function jsonEncodeAction() {
		echo json_encode($_SESSION['expenses']);
	}
	
}
