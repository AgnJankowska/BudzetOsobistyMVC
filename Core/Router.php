<?php
//dodajemy przestrzen nazw poniewaz plik jest w folderze Core
namespace Core;

class Router
{
	//WŁAŚCIWOŚĆ tablica asocjacyjna - tablica routingu
	protected $routes = [];
	
	//WŁAŚCIWOŚĆ tablica do zapisywania parametrów z dopasowanej trasy
	protected $params = [];
	
	//METODA dodająca trasę do tablicy routingu
	//$params to tablica zawierająca elementy:controller i akcja oraz ewentualnie zmienna id
	public function add($route, $params = []){
        // Usunięcie slashy z trasy
        $route = preg_replace('/\//', '\\/', $route);

        // Konwertowanie zmiennej {controller} na wyrażenie regularne}
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);

        // Konwertowanie zmiennej {id:/d} na wyrażenie regularne
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

        // Dodanie ograniczników na początku i końcu oraz brak rozróżnienia na duże i małe litery
        $route = '/^' . $route . '$/i';

        $this->routes[$route] = $params;
	}
	
	//METODA pobierająca całą tablice routingu
	public function getRoutes(){
		return $this->routes;
	}
	
	//DOPASOWANIE ZAAWANSOWANE
    public function match($url)
    {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }

                $this->params = $params;
                return true;
            }
        }

        return false;
    }

	
	//METODA do pobierania aktualnie dopasowanych parametrów (kontrolera i akcji)
	public function getParams(){
		return $this->params;
	}
	
	//dopasowanie trasy do klasy (kontrolera) i metody(akcji)
	public function dispatch($url)
    {
        $url = $this->removeQueryStringVariables($url);
		if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->convertToStudlyCaps($controller);
			$controller = $this->getNamespace().$controller;
			
            if (class_exists($controller)) {
                $controller_object = new $controller($this->params);

                $action = $this->params['action'];
                $action = $this->convertToCamelCase($action);

                    if (preg_match('/action$/i', $action) == 0) {
						$controller_object->$action();
					} 
					else {
						throw new \Exception("Method $action in controller $controller cannot be called directly - remove the Action suffix to call this method");
					}
            } else {
			throw new \Exception("Controller class $controller not found");
            }
        } else {
            throw new \Exception('No route matched.', 404);
        }
    }

	//funkcja zamieniająca nazwe-klasy na NazweKlasy
    protected function convertToStudlyCaps($string) {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

	//funkcja zamieniająca nazwe-metody na nazweMetody
    protected function convertToCamelCase($string) {
        return lcfirst($this->convertToStudlyCaps($string));
    }
	
	//usunięcie query string variable z adresu URL
	protected function removeQueryStringVariables($url)
    {
        if ($url != '') {
            $parts = explode('&', $url, 2);

            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }

        return $url;
    }
	
	
	protected function getNamespace()
    {
        $namespace = 'App\Controllers\\';

        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . '\\';
        }

        return $namespace;
    }
	
}

?>