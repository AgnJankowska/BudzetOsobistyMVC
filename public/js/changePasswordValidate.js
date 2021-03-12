        //toworzymy nową własną funkcję walidującą hasło 
		$.validator.addMethod('validPassword',
			function(value, element, param) {
				if (value != '') {
					if (value.match(/.*[a-z]+.*/i) == null) {
						return false;
					}
					if (value.match(/.*\d+.*/) == null) {
						return false;
					}
				}
				return true;
			},
			'Hasło musi zawierać minimum jedną cyfrę i literę'
		);
		
		$(document).ready(function() {
			//korzystamy z biblioteki walidacyjnej jQuery
            $('#formPassword').validate({
                //formujemy reguły walidacji do każdego z pól formularza
				rules: {
					//korzystamy ze stworzonej funkcji waliduącej validPassword
                    password: {
                        required: true,
                        minlength: 6,
						validPassword: true
                    }
                },
				messages: {
                    password: {
                        required: 'Pole wymagane',
                        minlength: 'Minimalna ilość znaków to 6',
						validPassword: 'Minimum jedna cyfra i litera'
                    }
				}
            });
			
			$('#inputPassword').hideShowPassword({
				show: false,
				innerToggle: 'focus'
			});	
        });