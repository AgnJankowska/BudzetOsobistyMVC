<?php

namespace App\Models;
use PDO;
use App\Token;
use App\Auth;

class Tables extends \Core\Model {
	
	public static function copyDefaultIncomes($user) {
		
		$sql = 'INSERT INTO incomes_category_assigned_to_users(user_id, name) SELECT :prep_user_id, name FROM incomes_category_default ';
		$db = static::getDB();	
		$stmt = $db->prepare($sql);		

		$stmt->bindValue(':prep_user_id', $user->id, PDO::PARAM_INT);
		$stmt->execute();
		
		$stmt_alter = $db->prepare('alter table incomes_category_assigned_to_users AUTO_INCREMENT=4');
		$stmt_alter->execute();			
	}
	
	public static function copyDefaultExpenses($user) {
		
		$sql = 'INSERT INTO expenses_category_assigned_to_users(user_id, name) SELECT :prep_user_id, name FROM expenses_category_default ';
		$db = static::getDB();	
		$stmt = $db->prepare($sql);		

		$stmt->bindValue(':prep_user_id', $user->id, PDO::PARAM_INT);
		$stmt->execute();
		
		$stmt_alter = $db->prepare('alter table expenses_category_assigned_to_users AUTO_INCREMENT=16');
		$stmt_alter->execute();
	}
	
	public static function copyDefaultPaymentMethod($user) {
		
		$sql = 'INSERT INTO payment_methods_assigned_to_users(user_id, name) SELECT :prep_user_id, name FROM  payment_methods_default ';
		$db = static::getDB();	
		$stmt = $db->prepare($sql);		

		$stmt->bindValue(':prep_user_id', $user->id, PDO::PARAM_INT);
		$stmt->execute();
		
		$stmt_alter = $db->prepare('alter table payment_methods_assigned_to_users AUTO_INCREMENT=3');
		$stmt_alter->execute();
	}
	
	public static function getPayMethods($user) {
		
		$sql = 'SELECT * FROM payment_methods_assigned_to_users WHERE user_id = :prep_user_id';
		$db = static::getDB();	
		$stmt = $db->prepare($sql);		

		$stmt->bindValue(':prep_user_id', $user->id, PDO::PARAM_INT);
		
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());	
		$stmt->execute();	
		return $stmt->fetchAll();
	}

	public static function getExpenseCategory($user) {
		
		$sql = 'SELECT * FROM expenses_category_assigned_to_users WHERE user_id = :prep_user_id';
		$db = static::getDB();	
		$stmt = $db->prepare($sql);		

		$stmt->bindValue(':prep_user_id', $user->id, PDO::PARAM_INT);
		
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());	
		$stmt->execute();	
		return $stmt->fetchAll();
	}
	
	public static function getIncomeCategory($user) {
		
		$sql = 'SELECT * FROM incomes_category_assigned_to_users WHERE user_id = :prep_user_id';
		$db = static::getDB();	
		$stmt = $db->prepare($sql);		

		$stmt->bindValue(':prep_user_id', $user->id, PDO::PARAM_INT);
		
		$stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());	
		$stmt->execute();	
		return $stmt->fetchAll();
	}
	
	public function ucfirstUtf8($str) {
		$in =  mb_strtolower($str,"utf8");
		$out = mb_strtoupper(mb_substr($in, 0, 1)).mb_substr($in, 1);
		return $out;
	}

	public static function updateIncomesCategory($data, $incomesCategory, $user) {	
		$tables = new Tables();
		$newName = $tables->ucfirstUtf8($data['newName']);
		$current_id = $data['currentId'];

		foreach($incomesCategory as $category) {
			if($category->name == $newName) {
				return false;
			}
		}		
		$sql = 'UPDATE incomes_category_assigned_to_users SET name=:newName WHERE id=:id AND user_id=:user_id';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':newName', $newName, PDO::PARAM_STR);
		$stmt->bindValue(':id', $current_id, PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $user->id, PDO::PARAM_STR);
				
		return $stmt->execute();
	}
	
	public static function deleteIncomesCategory($data, $user) {		
		$deletedId = $data['deletedId'];
		
		$sql = 'DELETE FROM incomes_category_assigned_to_users WHERE id=:id AND user_id=:user_id';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':id', $deletedId, PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $user->id, PDO::PARAM_STR);
				
		return $stmt->execute();
	}
	
	public static function addIncomesCategory($data, $incomesCategory, $user) {		
		$tables = new Tables();
		$newName = $tables->ucfirstUtf8($data['newName']);
		
		foreach($incomesCategory as $category) {
			if($category->name == $newName) {
				return false;
			}
		}	
		$sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name) VALUES (:user_id, :name)';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':user_id', $user->id, PDO::PARAM_STR);
		$stmt->bindValue(':name', $newName, PDO::PARAM_STR);
				
		return $stmt->execute();
	}	
	
	public static function deleteLimitExpensesCategory($data, $user) {	
		$current_id = $data['currentId'];
		
		$sql = 'UPDATE expenses_category_assigned_to_users SET limitExpense=NULL WHERE id=:id AND user_id=:user_id';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':id', $current_id, PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $user->id, PDO::PARAM_STR);
				
		return $stmt->execute();
	}
	
	public static function limitExpensesCategory($data, $user) {		
		$current_id = $data['currentId'];
		$limit = $data['limit'];
		
		$sql = 'UPDATE expenses_category_assigned_to_users SET limitExpense=:limit WHERE id=:id AND user_id=:user_id';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':id', $current_id, PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $user->id, PDO::PARAM_STR);
		$stmt->bindValue(':limit', $limit, PDO::PARAM_STR);
				
		return $stmt->execute();
	}
	
	public static function updateExpensesCategory($data, $expensesCategory, $user) {	
		$tables = new Tables();
		$newName = $tables->ucfirstUtf8($data['newName']);
		$current_id = $data['currentId'];

		foreach($expensesCategory as $category) {
			if($category->name == $newName) {
				return false;
			}
		}		
		$sql = 'UPDATE expenses_category_assigned_to_users SET name=:newName WHERE id=:id AND user_id=:user_id';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':newName', $newName, PDO::PARAM_STR);
		$stmt->bindValue(':id', $current_id, PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $user->id, PDO::PARAM_STR);
				
		return $stmt->execute();
	}
	
	public static function deleteExpensesCategory($data, $user) {		
		$deletedId = $data['deletedId'];
		
		$sql = 'DELETE FROM expenses_category_assigned_to_users WHERE id=:id AND user_id=:user_id';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':id', $deletedId, PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $user->id, PDO::PARAM_STR);
				
		return $stmt->execute();
	}
	
	public static function addExpensesCategory($data, $expensesCategory, $user) {		
		$tables = new Tables();
		$newName = $tables->ucfirstUtf8($data['newName']);
		
		foreach($expensesCategory as $category) {
			if($category->name == $newName) {
				return false;
			}
		}	
		$sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name) VALUES (:user_id, :name)';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':user_id', $user->id, PDO::PARAM_STR);
		$stmt->bindValue(':name', $newName, PDO::PARAM_STR);
				
		return $stmt->execute();
	}

	public static function updatePayMethods($data, $payMethod, $user) {
		
		$tables = new Tables();
		$newName = $tables->ucfirstUtf8($data['newName']);
		$current_id = $data['currentId'];

		foreach($payMethod as $method) {
			if($method->name == $newName) {
				return false;
			}
		}	
		$sql = 'UPDATE payment_methods_assigned_to_users SET name=:newName WHERE id=:id AND user_id=:user_id';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':newName', $newName, PDO::PARAM_STR);
		$stmt->bindValue(':id', $current_id, PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $user->id, PDO::PARAM_STR);
				
		return $stmt->execute();
	}
	
	public static function deletePayMethods($data, $user) {
		
		$deletedId = $data['deletedId'];
		
		$sql = 'DELETE FROM payment_methods_assigned_to_users WHERE id=:id AND user_id=:user_id';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':id', $deletedId, PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $user->id, PDO::PARAM_STR);
				
		return $stmt->execute();
	}
	
	public static function addPayMethods($data, $payMethod, $user) {
		
		$tables = new Tables();
		$newName = $tables->ucfirstUtf8($data['newName']);
		
		foreach($payMethod as $method) {
			if($method->name == $newName) {
				return false;
			}
		}		
		$sql = 'INSERT INTO payment_methods_assigned_to_users (user_id, name) VALUES (:user_id, :name)';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':user_id', $user->id, PDO::PARAM_STR);
		$stmt->bindValue(':name', $newName, PDO::PARAM_STR);
				
		return $stmt->execute();
	}
	
	public static function changeCategoryOfIncome($data, $user) {
		$deletedCategoryId = $data['deletedId'];
		$selectedCategoryId = $data['category'];
		
		$sql = 'UPDATE incomes SET income_category_assigned_to_user_id=:newCategory WHERE income_category_assigned_to_user_id=:oldCategory AND user_id=:user_id';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':newCategory', $selectedCategoryId, PDO::PARAM_STR);
		$stmt->bindValue(':oldCategory', $deletedCategoryId, PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $user->id, PDO::PARAM_STR);
				
		return $stmt->execute();
	}
	
	public static function changeCategoryOfExpense($data, $user) {
		$deletedCategoryId = $data['deletedId'];
		$selectedCategoryId = $data['category'];
		
		$sql = 'UPDATE expenses SET expense_category_assigned_to_user_id=:newCategory WHERE expense_category_assigned_to_user_id=:oldCategory AND user_id=:user_id';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':newCategory', $selectedCategoryId, PDO::PARAM_STR);
		$stmt->bindValue(':oldCategory', $deletedCategoryId, PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $user->id, PDO::PARAM_STR);
				
		return $stmt->execute();
	}

	public static function changePayMethod($data, $user) {
		$deletedCategoryId = $data['deletedId'];
		$selectedCategoryId = $data['category'];
		
		$sql = 'UPDATE expenses SET payment_method_assigned_to_user_id=:newMethod WHERE payment_method_assigned_to_user_id=:oldMethod AND user_id=:user_id';
		$db = static::getDB();
		$stmt = $db->prepare($sql);
				
		$stmt->bindValue(':newMethod', $selectedCategoryId, PDO::PARAM_STR);
		$stmt->bindValue(':oldMethod', $deletedCategoryId, PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $user->id, PDO::PARAM_STR);
				
		return $stmt->execute();
	}

}