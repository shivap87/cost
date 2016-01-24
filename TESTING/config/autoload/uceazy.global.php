<?php
return array(
	'logprop' => array("priority"=>3,"logtype"=>"db"
				,"pattern"=>'%timestamp% %priorityName% (%priority%): %message%'),
	'mailprop'=>array("send"=>"enoreply@csueazy.com",
						"receive"=>"noreply@csueazy.com"
						,"subject"=>"Critical Message",
						"host"=>"www.smtp.google.com",
						"port"=>546,
						"Type"=>"ssl",
						"Username"=>"ecurrency",
						"Password"=>"ecurrency"),
		);