<?php
namespace Core;

class Error {  //handler do błędów i wyjątków

	//Zamiana błędow na wyjątki
     //* int $level  		Error level
     //* string $message  	Error message
     //* string $file  		Filename the error was raised in
     //* int $line  		Line number in the file	 
    public static function errorHandler($level, $message, $file, $line) {
        if (error_reporting() !== 0) {  // to keep the @ operator working
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

	//Handler wyjątków
    public static function exceptionHandler($exception)
    {
		//Kod 404 (not found) lub 500 (general error
		$code = $exception->getCode();
		if ($code != 404) {
			$code = 500;
		}
		http_response_code($code);
		
		//const SHOW_ERRORS = true / wersja deweloperska
		if (\App\Config::SHOW_ERRORS) {   
            echo "<h1>Fatal error</h1>";
            echo "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
            echo "<p>Message: '" . $exception->getMessage() . "'</p>";
            echo "<p>Stack trace:<pre>" . $exception->getTraceAsString() . "</pre></p>";
            echo "<p>Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
        } 
		//const SHOW_ERRORS = false / wersja użytkownika
		else {   
            $log = dirname(__DIR__) . '/logs/' . date('Y-m-d') . '.txt';
            ini_set('error_log', $log);

            $message = "Uncaught exception: '" . get_class($exception) . "'";
            $message .= " with message '" . $exception->getMessage() . "'";
            $message .= "\nStack trace: " . $exception->getTraceAsString();
            $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();

            error_log($message);
			View::renderTemplate("$code.html");
        }
    }
}
?>
