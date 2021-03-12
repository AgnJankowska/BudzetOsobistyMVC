<?php

namespace App\Models;
use PDO; 
use \Core\View;

class Incomes extends \Core\Model {
	
	public function addIncome($user) {
		$amount = filter_input(INPUT_POST, 'amount');
		$date = filter_input(INPUT_POST, 'date');
		$category = filter_input(INPUT_POST, 'category');
		$comment = filter_input(INPUT_POST, 'comment');
		
		$sql = 'INSERT INTO incomes VALUES (NULL, :prep_user_id, :prep_category, :prep_amount, :prep_date, :prep_comment)';
		$db = static::getDB();	
		$stmt = $db->prepare($sql);		

		$stmt->bindValue(':prep_user_id', $user->id, PDO::PARAM_INT);
		$stmt->bindValue(':prep_category', $category, PDO::PARAM_STR);
		$stmt->bindValue(':prep_amount', $amount, PDO::PARAM_STR);
		$stmt->bindValue(':prep_date', $date , PDO::PARAM_STR);
		$stmt->bindValue(':prep_comment', $comment, PDO::PARAM_STR);
		
		return $stmt->execute();	
	}
	
	public static function getIncomeAssignetToUser($user, $date_start, $date_end) {
		
		$sql = 'SELECT incomes_category_assigned_to_users.name, SUM(incomes.amount) AS "amountSum" FROM incomes, incomes_category_assigned_to_users WHERE incomes.user_id = :prep_user_id AND incomes_category_assigned_to_users.user_id = incomes.user_id AND incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id AND incomes.date_of_income BETWEEN :prep_startDate AND :prep_endDate GROUP BY incomes_category_assigned_to_users.name ORDER BY amountSum DESC';
		$db = static::getDB();	
		$stmt = $db->prepare($sql);		

		$stmt->bindValue(':prep_user_id', $user->id, PDO::PARAM_INT);
		$stmt->bindValue(':prep_startDate', $date_start, PDO::PARAM_STR);
		$stmt->bindValue(':prep_endDate', $date_end, PDO::PARAM_STR);
		
		$stmt->execute();	
		
		$_SESSION['incomes'] = $stmt->fetchAll();
		return array_sum(array_column($_SESSION['incomes'], 'amountSum'));
	}
}