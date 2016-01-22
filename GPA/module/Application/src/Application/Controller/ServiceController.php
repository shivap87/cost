<?php
namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractActionController;
use Common\API\RESTJSONManager;
use Common\Utilities\Constants;
use Common\Utilities\Convertors;
use Zend\Session\SessionManager;
use Zend\Session\Container;

use Common\CustomController\CustomController;
use Common\CustomMailer;
use Common\CustomMailer\EmailManager;
use Zend\Http\Request;
use Zend\Http\Client;
use Zend\Stdlib\Parameters;
use Common\ActionForms\ExplorerForm;
use Common\API\RESTManager;
use Zend\Db\Sql\Select;


use Zend\Db\ResultSet\ResultSet;
class ServiceController extends AbstractActionController
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
	
	function indexAction(){
		return new JsonModel(array("error"=>"No Access"));
	}
	
	function insertupdateSocialAuthAction(){
		
		$name=$this->params()->fromPost('name');
		$network=$this->params()->fromPost('network');
		$email=$this->params()->fromPost('email');
		$profile=$this->params()->fromPost('profilepic');
		$token=$this->params()->fromPost('token');
		
		
		$session = new Container('user');
		$session->username = $email;
		$session->network = $network;
		$session->profilepic=$profile;
		
		
		// Check the key already present or not 
		$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
		$sql = "select * from social_auth where email='$email' and network='$network'";
		
		$stmt = $adapter->createStatement($sql);
		$stmt->prepare();
		$total = $stmt->execute();
		
		if(count($total)>0){
			// user already authenticated. show message and redirect to auto login
			
			$flag=$this->getUserTable()->checkEmailAvailable($email);
			
			// 1 - eamil available in user table
			// 0 - email not available in user table
			
			if($flag){
				$result=$this->getUserTable()->checkLoginSocial($email);
				
				//print_r($result);
				foreach ($result as $row) {
					$session->notification_data=$this->getNotifyData("cnt",$row->user_id);
					$session->profile_picture=$row->profile_picture;
					$session->userid= $row->user_id ;
					$this->getUserTable()->updateLastLogin($row->user_id);
				}
				return new JsonModel(array('signupstatus'=>1,'errorcode'=>0,'errordesc'=>'success'));
			}else{
				return new JsonModel(array('signupstatus'=>0,'errorcode'=>0,'errordesc'=>'success'));
			}
		}else{
		// Insert SQL 
		
			
		$insertsql = "INSERT INTO `social_auth` (`id`, `email`, `network`, `name`, `profilepic`, `token`) VALUES (NULL, '$email', '$network', '$name', '$profile', '$token')";
		$stmt = $adapter->createStatement($insertsql);
		$stmt->prepare();
		$result = $stmt->execute();
		
		
		
		}
		return new JsonModel(array('signupstatus'=>0,'errorcode'=>0,'errordesc'=>'success'));
		
	}
	
	public function getNotifyData($flag,$uid){
	
		$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
		$flag=trim ($flag);
		$uid=trim ($uid);
		//$flag=trim ( $this->params ()->fromPost ( 'flag' ) );
		//$uid=trim ( $this->params ()->fromPost ( 'user_id' ) );
		if($flag=='cnt'){
			$stmt = $adapter->createStatement("SELECT count(user_notification_history.user_id) as cnt
					FROM notification_master LEFT JOIN user_notification_history ON notification_master.notify_id=user_notification_history.notify_id where notification_master.end_date >= CURDATE()
					and user_notification_history.is_read='no' and user_notification_history.user_id=$uid");
		}if($flag=="all"){
	
			$stmt = $adapter->createStatement("SELECT notification_master.*, user_notification_history.*
					FROM notification_master LEFT JOIN user_notification_history ON notification_master.notify_id=user_notification_history.notify_id where notification_master.end_date >= CURDATE()
					and user_notification_history.user_id=$uid order by user_notification_history.is_read");
	
		}
		$stmt->prepare();
		$result = $stmt->execute();
		$resultSet = new ResultSet();
		$resultSet->initialize($result);
		$data=$resultSet->toArray();
		if($flag=="all"){
			foreach($data as $key => $value)
			{
				$data[$key]['start_date'] = $this->Timesince($value['start_date']);
			}
		}
		return $data;
	}
	
	function inboundAction(){
		
		
		$serviceid = $this->getRequest()->getQuery('sid');
		$channelid = $this->getRequest()->getQuery('cid');
		$session = new Container('global');
		$session->channel = Convertors::getChannelName($channelid);
		$channalname=Convertors::getChannelName($channelid);
		$servicename = Convertors::getServiceName($serviceid);
		
		$currentTime = date('Y-m-d H:i:s');
		
		
		$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
		$sql = "INSERT INTO `tbl_channel_access` (`id`, `channelname`, `servicename`, `accesstime`) VALUES (NULL, '$channalname', '$servicename', '$currentTime')";
		$stmt = $adapter->createStatement($sql);
		$stmt->prepare();
		$result = $stmt->execute();
		
		
		if($servicename!=""){
			return $this->redirect ()->toUrl ( $servicename );
		}else{
			return $this->redirect ()->toUrl ( '/' );
		}
		
		return new JsonModel(array());
	}
	
	function getschoolsByCountyAction(){
		
		
		$county = $this->params()->fromPost('county');
		
		$county = str_replace(" ", "%20", $county);
		
		$allschools="[]";
		try{
		$api = new RESTJSONManager();
		
		$url = Constants::SCHOOLS_BY_COUNTY;
		
		$url = str_replace("<COUNTY>", $county, $url);
		
		$result = $api->PlanJSONManager("GET", $url, null, null);
		
		$allschools = $result->getBody();
		
		}catch (\Exception $e){
			
			
		}
		return new JsonModel(array("schools"=>$allschools,"county"=>$county));
		
	}
	
	
}
