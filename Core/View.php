<?php
namespace Core;
use \App\Auth;
use \App\Flash;

class View
{
	//metoda wyświetlająca plik widoku z wykorzystaniem silnika Twig
	//korzystająca z metody getTemplate()
    public static function renderTemplate($template, $args1 = []) {
        echo static::getTemplate($template, $args1);
    }
	
	//metoda pobierająca plik widoku z wykorzystaniem silnika Twig
	//na koniec tej metody nie ma polecenia echo więc nie służy ona
	//do WYŚWIETLANIA a jedynie do POBIERANIA
    public static function getTemplate($template, $args1 = [])
    {
        static $twig = null;

        if ($twig === null) {
            $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/App/Views');
            $twig = new \Twig\Environment($loader);
			//dodajemt wszystkie zmienne sesyjne do użycia w bibliotece TWIG
			$twig->addGlobal('session', $_SESSION);
			//dodajemy metodę z jednej z klas którą chcemy użyć w pliku widoku
			$twig->addGlobal('current_user', Auth::getUser());
			$twig->addGlobal('flash_messages', Flash::getMessages());
        }

        return $twig->render($template, $args1);
    }
}
?>
