<?php

namespace App\Controllers;
use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Expenses;
use \App\Models\Incomes;

class Balance extends Authenticated {
	
	protected $range;
	protected $showed;
	
	protected function before() {
		parent::before();
		$this->range = false;
	}
	
	protected function setExpenses() {
		$this->expenses = Expenses::getExpenseAssignetToUser(Auth::getUser(), $this->date_start, $this->date_end);
		$_SESSION['chartData'] = $this->expenses;
		return $this->expenses;
	}
	
	protected function setIncomes() {
		$this->incomes = Incomes::getIncomeAssignetToUser(Auth::getUser(), $this->date_start, $this->date_end);
		return $this->incomes;
	}
	
	public function indexAction() {

		$this->range = filter_input(INPUT_POST, 'range');		
		$this->checkGivenDate();
				
		$expense_sum = array_sum(array_column($this->setExpenses(), 'amountSum'));	
		$income_sum = array_sum(array_column($this->setIncomes(), 'amountSum'));
		
		$savings = $income_sum - $expense_sum;
		
		$expense_sum_format = number_format($expense_sum ,2, '.', ' ');
		$income_sum_format = number_format($income_sum ,2, '.', ' ');
		$savings_format = number_format($savings ,2, '.', ' ');
		
		View::renderTemplate('Balance/index.html', [
			'range' => $this->range,
			'showed' => $this->showed,
			'date_start' => $this->date_start,
			'date_end' => $this->date_end,
			'expenses' => $this->expenses,
			'incomes' => $this->incomes,
			'expense_sum' => $expense_sum_format,
			'income_sum' => $income_sum_format,
			'savings' => $savings_format
		]);
		
		$this->range = true;
		$this->showed = false;
	}
	
	public function checkGivenDate() {
		$this->date_start = filter_input(INPUT_POST, 'date_start');
		$this->date_end = filter_input(INPUT_POST, 'date_end');
		
		if(($this->date_start) > ($this->date_end))	{
			Flash::addMessage('Nieprawidłowy zakres dat', Flash::WARNING);
		}
	}
	
	public function form() {
		$this->showed = true;
		$this->index();
	}

	public function jsonEncodeAction() {
		echo json_encode($_SESSION['chartData']);
	}

}
