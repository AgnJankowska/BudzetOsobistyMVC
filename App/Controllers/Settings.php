<?php

namespace App\Controllers;
use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Tables;
use \App\Models\Profile;
use \App\Models\Expenses;
use \App\Models\Incomes;

class Settings extends Authenticated {
	
	protected $show1;
	protected $show2;
	protected $show3;
	protected $show4;
	
	protected function before() {
		parent::before();
	}
	
	public function indexAction() {
		$payMethod = Tables::getPayMethods(Auth::getUser());
		$expensesCategory = Tables::getExpenseCategory(Auth::getUser());		
		$incomesCategory = Tables::getIncomeCategory(Auth::getUser());		
		
		View::renderTemplate('Settings/index.html', [
			'pay' => $payMethod,
			'exp' => $expensesCategory,
			'inc' => $incomesCategory,
			'show1' => $this->show1,
			'show2' => $this->show2,
			'show3' => $this->show3,
			'show4' => $this->show4
		]);
		$this->show1 = false;
		$this->show2 = false;
		$this->show3 = false;
		$this->show4 = false;
	}

	//USERS
	public function updateNameAction() {
		if(Profile::updateName($_POST, Auth::getUser())) {		
			Flash::addMessage('Zmieniono nazwę');
		}
		$this->show1 = true;
		$this->index();		
	}
	
	public function updatePasswordAction() {
		if(Profile::updatePassword($_POST, Auth::getUser())) {		
			Flash::addMessage('Zmieniono hasło');
		}
		$this->show1 = true;
		$this->index();		
	}

	//INCOMES
	public function updateIncomesCategoryAction() {
		
		$icomesCategory = Tables::getIncomeCategory(Auth::getUser());			
		if(Tables::updateIncomesCategory($_POST, $icomesCategory, Auth::getUser())) {		
			Flash::addMessage('Zmieniono nazwę');
		}
		else {
			Flash::addMessage('Taka nazwa już istnieje, wprowadź inną nazwę', Flash::WARNING);
		}
		$this->show2 = true;
		$this->index();
	}
	
	public function deleteIncomesCategoryAction() {
		
		if(Tables::deleteIncomesCategory($_POST, Auth::getUser())) {		
			Flash::addMessage('Usunięto wskazaną kategorię przychodów');
		}
		$this->show2 = true;
		$this->index();
	}

	public function addIncomesCategoryAction() {
		
		$icomesCategory = Tables::getIncomeCategory(Auth::getUser());	
		if(Tables::addIncomesCategory($_POST, $icomesCategory, Auth::getUser())) {		
			Flash::addMessage('Dodano nową kategorię przychdów');
		}
		else {
			Flash::addMessage('Taka nazwa już istnieje, wprowadź inną nazwę', Flash::WARNING);
		}	
		$this->show2 = true;
		$this->index();
	}

	//EXPENSES
	public function deleteLimitExpensesCategory() {
							
		if(Tables::deleteLimitExpensesCategory($_POST, Auth::getUser())) {		
			Flash::addMessage('Usunięto wskazany limit');
		}
		$this->show3 = true;
		$this->index();
	}
	
	public function limitExpensesCategoryAction() {
					
		if(Tables::limitExpensesCategory($_POST, Auth::getUser())) {		
			Flash::addMessage('Dodano limit');
		}
		$this->show3 = true;
		$this->index();
	}
	
	public function updateExpensesCategoryAction() {
		
		$expensesCategory = Tables::getExpenseCategory(Auth::getUser());			
		if(Tables::updateExpensesCategory($_POST, $expensesCategory, Auth::getUser())) {		
			Flash::addMessage('Zmieniono nazwę');
		}
		else {
			Flash::addMessage('Taka nazwa już istnieje, wprowadź inną nazwę', Flash::WARNING);
		}
		$this->show3 = true;
		$this->index();
	}
	
	public function deleteExpensesCategoryAction() {
		
		if(Tables::deleteExpensesCategory($_POST, Auth::getUser())) {		
			Flash::addMessage('Usunięto wskazaną kategorię wydatków');
		}
		$this->show3= true;
		$this->index();
	}

	public function addExpensesCategoryAction() {
		
		$expensesCategory = Tables::getExpenseCategory(Auth::getUser());	
		if(Tables::addExpensesCategory($_POST, $expensesCategory, Auth::getUser())) {		
			Flash::addMessage('Dodano nową kategorię wydatków');
		}
		else {
			Flash::addMessage('Taka nazwa już istnieje, wprowadź inną nazwę', Flash::WARNING);
		}	
		$this->show3 = true;
		$this->index();
	}
	
	//PAY METHODS
	public function updatePayMethodsAction() {
		
		$payMethod = Tables::getPayMethods(Auth::getUser());			
		if(Tables::updatePayMethods($_POST, $payMethod, Auth::getUser())) {		
			Flash::addMessage('Zmieniono nazwę');
		}
		else {
			Flash::addMessage('Taka nazwa już istnieje, wprowadź inną nazwę', Flash::WARNING);
		}
		$this->show4 = true;
		$this->index();
	}

	public function deletePayMethodsAction() {
		
		if(Tables::deletePayMethods($_POST, Auth::getUser())) {		
			Flash::addMessage('Usunięto wskazaną metodę płatności');
		}
		$this->show4 = true;
		$this->index();
	}

	public function addPayMethodsAction() {
		
		$payMethod = Tables::getPayMethods(Auth::getUser());	
		if(Tables::addPayMethods($_POST, $payMethod, Auth::getUser())) {		
			Flash::addMessage('Dodano nową metodę płatności');
		}
		else {
			Flash::addMessage('Taka nazwa już istnieje, wprowadź inną nazwę', Flash::WARNING);
		}	
		$this->show4 = true;
		$this->index();
	}
	
	public function replaceIncomeCategoryAction() {
		
		$incomesCategory = Tables::getIncomeCategory(Auth::getUser());
		$incomesCategoryWithoutDeleted = [];
				
		foreach ($incomesCategory as $income) {
			if($income->id != $_POST['deletedId']) {
				array_push($incomesCategoryWithoutDeleted, $income);
			}
			else {
				$incomeDeletedName = $income->name;
			}
		}
		
		View::renderTemplate('Settings/replaceInc.html', [
			'inc' => $incomesCategoryWithoutDeleted,
			'inc_deleted_ID' => $_POST['deletedId'],
			'inc_deleted_name' => $incomeDeletedName
		]);
	}
	
	public function changeCategoryOfIncome() {
				
		Tables::changeCategoryOfIncome($_POST, Auth::getUser());
		$this->deleteIncomesCategory();
	}
	
	public function replaceExpenseCategoryAction() {
		
		$expensesCategory = Tables::getExpenseCategory(Auth::getUser());
		$expensesCategoryWithoutDeleted = [];
				
		foreach ($expensesCategory as $expense) {
			if($expense->id != $_POST['deletedId']) {
				array_push($expensesCategoryWithoutDeleted, $expense);
			}
			else {
				$expenseDeletedName = $expense->name;
			}
		}
		
		View::renderTemplate('Settings/replaceExp.html', [
			'exp' => $expensesCategoryWithoutDeleted,
			'exp_deleted_ID' => $_POST['deletedId'],
			'exp_deleted_name' => $expenseDeletedName
		]);
	}
	
	public function changeCategoryOfExpense() {
				
		Tables::changeCategoryOfExpense($_POST, Auth::getUser());
		$this->deleteExpensesCategory();
	}
	
	public function replacePayMethodAction() {
		
		$payMethods = Tables::getPayMethods(Auth::getUser());
		$payMethodsWithoutDeleted = [];
				
		foreach ($payMethods as $pay) {
			if($pay->id != $_POST['deletedId']) {
				array_push($payMethodsWithoutDeleted, $pay);
			}
			else {
				$payDeletedName = $pay->name;
			}
		}
		
		View::renderTemplate('Settings/replacePay.html', [
			'pay' => $payMethodsWithoutDeleted,
			'pay_deleted_ID' => $_POST['deletedId'],
			'pay_deleted_name' => $payDeletedName
		]);
	}
	
	public function changePayMethod() {
				
		Tables::changePayMethod($_POST, Auth::getUser());
		$this->deletePayMethods();
	}
	
}
