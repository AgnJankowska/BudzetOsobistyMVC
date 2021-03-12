<?php
namespace Core;
use PDO; //korzystamy z biblioteki PDO
use App\Config;

//połączenie z bazą danych
abstract class Model {
    protected static function getDB() { //metoda statyczna
        static $db = null; //statyczna zmienna
	
		if ($db === null) {
            $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' .
                   Config::DB_NAME . ';charset=utf8';
            $db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);

            // Gdy wystapi błąd pokazany zostanie wyjątek
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
		
        return $db;
    }
}
?>