<?php
namespace Common\Utilities;
use Zend\Session\SessionManager;
use Zend\Session\Container;
class Languages{
	
	static $langarray = array(
			'en'=>array('signin'=>"Sign in",
					'index_select_lang'=>'Select Language',
					'index_l_signup'=>"Sign Up"
			),
			'sp'=>array('signin'=>'Registrarse',
					'index_select_lang'=>'Seleccione Idioma',
					'index_l_signup'=>'Contratar'
			)
	);
	
	static function getLocaleText($key){
		$session = new Container('global');
		 
		return Languages::$langarray[$session->lang][$key];
	}
}