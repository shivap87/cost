<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Common\CustomController\CustomController;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Common\CustomMailer;
use Common\CustomMailer\EmailManager;
use Zend\Http\Request;
use Zend\Http\Client;
use Zend\Stdlib\Parameters;
use Common\ActionForms\ExplorerForm;
use Common\API\RESTManager;
use Zend\Db\Sql\Select;
use Common\Utilities\Constants;
use Zend\View\Model\JsonModel;
use Common\API\RESTJSONManager;
use Zend\Json\Json;
use Common\Utilities\Convertors;
use Zend\Db\ResultSet\ResultSet;

class PlanController extends AbstractActionController {
	
	function LogMessage($message) {
		if (Constants::IS_LOG) {
			$logger = new \Zend\Log\Logger ();
			$writer = new \Zend\Log\Writer\Stream ( Constants::LOG_FILE );
			$logger->addWriter ( $writer );
			$logger->info ( $message );
		}
	}
	function costsAction() {
		$this->layout ( "layout/user" );
		$result=array();
		$view = new ViewModel ( array (
					"result" => $result,

		) );

		return $view;
	}	
	
}