<?php

namespace App;
use App\Models\User;
use App\Models\RememberedLogin;

//klasa służąca do wszystkich poświadczeń dostępności
class Auth {
	
	//funckja służąca do zalogowania użytkowników
	public static function login($user, $remember_me) {
		//korzystamy z funckji session_regenerate_id po to aby zabezpieczyc się przed hakerami którzy mogą korzystać z takiego samego numeru sesji co my. Po zalogowaniu numer sesji jest zmieniany na nowy więc jestesmy zabezpieczeni
		session_regenerate_id(true);
			
		//w pliku public/index.php rozpoczeliśmy sesję do której przeypisujemy teraz zmienne SESYJNE
		//dobrą praktyką jest przypisywać te zmienne w małych porcjach (zamiast całego obiektu $user, jedynie jego niektóre istotne atrybuty
		$_SESSION['user_id'] = $user->id;
		
		//ustanowienie plików cookie które pozwalają na użycie
		//opcji logowania - zapamietaj mój login
		if($remember_me) {
			if($user->rememberLogin()) {
				setcookie('remember_me', $user->remember_token, $user->expiry_timestamp, '/');
			}
		}
	}
	
	//funckja służaca do wylogowywania użytkowników
	public static function logout() {
		//usunięcie wszystkich zmiennych sesyjnych
		$_SESSION = [];

		//usunięcie wszystkich plików cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

		//usunięcie całej sesji
		session_destroy();
        static::forgetLogin();   
	}
	
	//funckja sprawdza czy użytkownik jest zalogowany i pobiera dane wszystie dane użytkownika do obiektu User
    public static function getUser() {
        if (isset($_SESSION['user_id'])) {
            return User::findByID($_SESSION['user_id']);
        } 
		else {
            return static::loginFromRememberCookie();
        }
    }

	
	//funkcja do zapamietywania strony którą chciał odwiedzić
	//niezalogowany użytkownik i która zostanie mu wyświetlona
	//gdy się zaloguje
	public static function rememberRequestedPage() {
		$_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
	}
	
	//funkcja zwracająca dwie możliwości przekierowania 
	//1) strona na która użytkownik chciał wejść LUB
	//2) strona głowna domyślnie
	public static function getReturnToPage() {
		return $_SESSION['return_to'] ?? '/';
	}
	
	//funkcja do automatycznego logowania na podstawie plików cookie po tym jak użytkownik zaznaczy opcję Remember me
    protected static function loginFromRememberCookie() {
        $cookie = $_COOKIE['remember_me'] ?? false;
		
        if ($cookie) {
            $remembered_login = RememberedLogin::findByToken($cookie);

            if ($remembered_login && ! $remembered_login->hasExpired()) {
                $user = $remembered_login->getUser();
                static::login($user, false);

                return $user;
            }
        }
    }
	
	//funckja do zapominania loginu w trakcie wylogowywania
    protected static function forgetLogin() {
        $cookie = $_COOKIE['remember_me'] ?? false;
        if ($cookie) {
            $remembered_login = RememberedLogin::findByToken($cookie);

            if ($remembered_login) {
                $remembered_login->delete();
            }

            setcookie('remember_me', '', time() - 3600);  // set to expire in the past
        }
    }
	
}