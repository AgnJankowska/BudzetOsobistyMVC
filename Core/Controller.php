<?php
//dodajemy przestrzen nazw poniewaz plik jest w folderze Core
namespace Core;
use \App\Auth;
use \App\Flash;

abstract class Controller
{
	//tablica z parametrami pobranymi z trasy
    protected $route_params = [];

	//konstruktor klasy
    public function __construct($route_params) {
        $this->route_params = $route_params;
    }

	//funkcja magiczna __call wywoływana zawsze gdy wywołana będzie akcja prywatna / nieistniejąca. Używamy jej aby dodać action filter
	public function __call($name, $args) {
        $method = $name . 'Action';

        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                call_user_func_array([$this, $method], $args);
                $this->after();
            }
        } else {
            throw new \Exception("Method $method not found in controller.".get_class($this));
        }
    }

    //funckja w której umieścimy before-filter czyli kod który ma się wykonac przed każdym uruchomieniem dowolnej funkcji 
    protected function before() {
    }

    //funckja w której umieścimy after-filter czyli kod który ma się wykonac po każdym uruchomieniu dowolnej funkcji 
    protected function after() {
    }	
	
	//dla ułatwienia stworzyliśmy funckję która będzie dostepna w każdym kontrolerze
	//po to aby przekierowywanie na inne strony było prostrze i nie trzeba było
	//za każdym razem pisać skomplikowanej formuły z naglowiem header, który
	//przy odświeżeniu strony nie wyświeli się komunikatu o ponownym przesłaniu //danych do bazy
	public function redirect($url) {
		header('Location: http://' . $_SERVER['HTTP_HOST'] . $url, true, 303);
		exit;
	}
	
	//funckja którą będzie można używać w każdym controlerze aby sprawdzić czy strona którą chce odwiedzić użytkwonik może być mu wyświetlona, czyli czy użytkownik ma prawo dostepu do tej strony bo jest np. zalogowany
	public function requireLogin() {
		if (! Auth::getUser()) {
			
			//dodanie wiadomości flash typu INFO
			Flash::addMessage('Please login to access that page', Flash::INFO);
			Auth::rememberRequestedPage();
			$this -> redirect('/login');
		}
	}
}
?>