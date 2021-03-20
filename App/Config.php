<?php
namespace App;

class Config {
    const DB_HOST = 'localhost';  //host
    const DB_NAME = 'mvclogin';   //name
    const DB_USER = 'root';       //user
    const DB_PASSWORD = '';       //password
	
	const SHOW_ERRORS = false;    //pokazywanie wiadomości o błędach
	
	//wygenerowany kod ze strony RandomKeygen - CodeIgniter Encryption Keys
	//korzystamy z niego przy generowaniu tokena
	const SECRET_KEY = 'JL2iblij4vAbsBJUkJbR6MimzskD2bcW';
	
	//dane do wysłania emaila do użytkowników
	const HOST_EMAIL = 'mail.agnieszkajankowska.pl ';
	const USER_NAME_EMAIL = 'budget@agnieszkajankowska.pl';
	const PASSWORD_EMAIL = 'xxx';
}
