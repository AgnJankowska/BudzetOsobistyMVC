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
}