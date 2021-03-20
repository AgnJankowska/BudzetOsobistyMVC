<?php

namespace App\Models;
use PDO; 
use \Core\View;

class Expenses extends \Core\Model {
	
	public function addExpense($user) {
		$amount = filter_input(INPUT_POST, 'amount');
		$date = filter_input(INPUT_POST, 'date');
		$category = filter_input(INPUT_POST, 'category');
		$comment = filter_input(INPUT_POST, 'comment');
		$method = filter_input(INPUT_POST, 'payMethod');
		
		$sql = 'INSERT INTO expenses VALUES (NULL, :prep_user_id, :prep_category, :prep_method, :prep_amount, :prep_date, :prep_comment)';
		$db = static::getDB();	
		$stmt = $db->prepare($sql);		

		$stmt->bindValue(':prep_user_id', $user->id, PDO::PARAM_INT);
		$stmt->bindValue(':prep_category', $category, PDO::PARAM_STR);
		$stmt->bindValue(':prep_method', $method, PDO::PARAM_STR);
		$stmt->bindValue(':prep_amount', $amount, PDO::PARAM_STR);
		$stmt->bindValue(':prep_date', $date , PDO::PARAM_STR);
		$stmt->bindValue(':prep_comment', $comment, PDO::PARAM_STR);
		
		return $stmt->execute();	
	}
	
	public static function getExpenseAssignetToUser($user, $date_start, $date_end) {
		
		$sql = 'SELECT expenses_category_assigned_to_users.name, SUM(expenses.amount) AS "amountSum" FROM expenses, expenses_category_assigned_to_users WHERE expenses.user_id = :prep_user_id AND expenses_category_assigned_to_users.user_id = expenses.user_id AND expenses_category_assigned_to_users.id = expenses.expense_category_assigned_to_user_id AND expenses.date_of_expense BETWEEN :prep_startDate AND :prep_endDate GROUP BY expenses_category_assigned_to_users.name ORDER BY amountSum DESC';
		$db = static::getDB();	
		$stmt = $db->prepare($sql);		

		$stmt->bindValue(':prep_user_id', $user->id, PDO::PARAM_INT);
		$stmt->bindValue(':prep_startDate', $date_start, PDO::PARAM_STR);
		$stmt->bindValue(':prep_endDate', $date_end, PDO::PARAM_STR);
		
		$stmt->execute();	
		
		return $stmt->fetchAll();
	}
}