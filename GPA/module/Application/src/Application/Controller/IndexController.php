<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Common\CustomController\CustomController;
use Zend\Db\ResultSet\ResultSet;
use Zend\Session\SessionManager;
use Zend\Session\Container;
use Zend\Log;
use Common\Utilities\Constants;
use Zend\View\Model\JsonModel;
use Common\API\SocialMedia;
use Zend\Config\Reader\Json;
class IndexController extends AbstractActionController
{
	protected $userTable;
	
	public function getUserTable()
	{
		if (!$this->userTable) {
			$sm = $this->getServiceLocator();
			$this->userTable = $sm->get('UserService');
		}
		return $this->userTable;
	}
	public function subserviceAction(){

		$email = trim ( $this->params ()->fromPost ( 'email' ) );
		/* $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
		 $sql = "INSERT INTO  subscribers (id, email) VALUES (NULL, '$email')";
		 $stmt = $adapter->createStatement($sql);
		 $stmt->prepare();
		 $result = $stmt->execute();
		 */
		$userid=$this->getUserTable()->insertStudent($email,"","",Constants::ROLE_GUEST);

		$view = new JsonModel(array("status"=>1));
		return $view;
	}

	public function contactserviceAction(){
		$name = trim ( $this->params ()->fromPost ( 'name' ) );
		$phone = trim ( $this->params ()->fromPost ( 'phone' ) );
		$email = trim ( $this->params ()->fromPost ( 'email' ) );
		$comments = trim ( $this->params ()->fromPost ( 'comments' ) );
		$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
		$sql = "INSERT INTO contactus (id, name, phone, email, comments) VALUES (NULL, '$name', '$phone', '$email', '$comments');";
		$stmt = $adapter->createStatement($sql);
		$stmt->prepare();
		$result = $stmt->execute();
		$view = new JsonModel(array("status"=>1));
		return $view;
	}


	public function faqAction(){

	}

	public function termsAction(){

	}

	public function privacyAction(){

	}

	public function tAction(){
		$oauth = new SocialMedia();
			
		$oauth->provider="Facebook";
		$oauth->client_id = "482962811753376";
		$oauth->client_secret = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
		$oauth->scope="email,publish_stream,status_update,friends_online_presence,user_birthday,user_location,user_work_history";
		$oauth->redirect_uri  ="http://ngiriraj.com/socialMedia/oauthlogin/facebook.php";
			
		$oauth->Initialize();
			
		$code = "facebook";
			
		if(empty($code)) {
			$oauth->Authorize();
		}else{
			$oauth->code = $code;
			#	print $oauth->getAccessToken();
			$getData = json_decode($oauth->getUserProfile());
			$oauth->debugJson($getData);

		}

		return new JsonModel(array("aa"=>"aa"));
	}

	public function indexAction()
	{
		 
		 
		 
		 
		 
		/*
		 $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');

		 $stmt = $adapter->createStatement('select * from planmodule');

		 $stmt->prepare();

		 $result = $stmt->execute();

		 $resultSet = new ResultSet();

		 $resultSet->initialize($result);

		 print_r($resultSet->toArray());
		 */
		 
		$session = new Container('global');
		 
		if($session->lang=='' || $session->lang==null){
			$session->lang="en";
		}
		 
		 
		$lang=$this->getRequest()->getQuery("lang");
		if($lang!=''){
			$session->lang=$lang;
		}
		$action=$this->getRequest()->getQuery("action");
		 
		if($action=="verify"){

			$key= $this->getRequest()->getQuery("key");
			$returncode=$this->getUserTable()->updateVerification($key);
			if($returncode){
				$view = new ViewModel(array('action'=>'verify','success'=>'Successfully verified your email id'));
				return $view;
			}else{
				$view = new ViewModel(array('action'=>'verify','error'=>'Invalid verification key'));
				return $view;
			}
		}
		 
		if($action=="unsubscribe"){
			$key= $this->getRequest()->getQuery("key");
			$mode = $this->getRequest()->getQuery("mode");


			$returncode=$this->getUserTable()->updateUnSubscription($key,$mode);

			if($returncode){
				$view = new ViewModel(array('action'=>'unsubscribe','success'=>'Successfully verified your email id'));
				return $view;
			}else{
				$view = new ViewModel(array('action'=>'unsubscribe','error'=>'Invalid verification key'));
				return $view;
			}
			 

		}
		 
		$view = new ViewModel(array(
    			'action' => $action,
		));
		return $view;
	}

	function LogMessage($message){
		if(Constants::IS_LOG){
			$logger = new \Zend\Log\Logger();
			$writer = new \Zend\Log\Writer\Stream(Constants::LOG_FILE);
			$logger->addWriter($writer);
			$logger->info($message);
		}
	}






}