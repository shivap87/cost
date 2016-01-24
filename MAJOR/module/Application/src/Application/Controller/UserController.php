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
use Zend\Db\ResultSet\ResultSet;


class UserController extends AbstractActionController
{
	
	protected $userTable;
	protected $seachcriteriaTable;
	protected $savedsearchresultTable;
	protected $planTable;
	protected $serviceMasterTable;
	protected $essayTable;
	
	
	
	public function getPlanTable()
	{
		if (!$this->planTable) {
			$sm = $this->getServiceLocator();
			$this->planTable = $sm->get('PlanService');
		}
		return $this->planTable;
	}
	
	public function getUserTable()
	{
		if (!$this->userTable) {
			$sm = $this->getServiceLocator();
			$this->userTable = $sm->get('UserService');
		}
		return $this->userTable;
	}
	
	public function getSeachcriteriaTable()
	{
		if (!$this->seachcriteriaTable) {
			$sm = $this->getServiceLocator();
			$this->seachcriteriaTable = $sm->get('SearchCriteriaService');
		}
		return $this->seachcriteriaTable;
	}
	
	public function getSavedSearchResultTable()
	{
		if (!$this->savedsearchresultTable) {
			$sm = $this->getServiceLocator();
			$this->savedsearchresultTable = $sm->get('SavedSearchResultService');
		}
		return $this->savedsearchresultTable;
	}

	public function getServiceMasterTable()
	{
		if (!$this->serviceMasterTable) {
			$sm = $this->getServiceLocator();
			$this->serviceMasterTable = $sm->get('ServiceMasterService');
		}
		return $this->serviceMasterTable;
	}
	public function getEssayTable()
    {
        if (!$this->essayTable) {
            $sm = $this->getServiceLocator();
            $this->essayTable = $sm->get('EssayService');
        }
        return $this->essayTable;
    }

	public function registerAction(){
		$this->layout("layout/empty");
	}
	
	public function forgotAction(){
		$this->layout('layout/empty');
	}

	
	public function changepasswordAction(){
	
		$this->layout('layout/user');
	
		
		
		
	}
	
	
	public function changepassserviceAction(){
		$session = new Container ( 'user' );
		$username = $session->username;
		$uid = $session->userid;
		
		if($uid!=null && $uid!=''){
		$oldpassword = $this->params()->fromPost('oldpassword');
		$newpassword = $this->params()->fromPost('newpassword');
		$confirmpassword = $this->params()->fromPost('confirmpassword');
		
		$flag = $this->getUserTable()->checkOldPassword($uid,$oldpassword);
		
		if($flag=='0'){
				
			$view = new JsonModel(array('error'=>'Your current password is not matching'));
			return $view;
				
		}
		
		if($newpassword!=$confirmpassword && $newpassword!=''){

			$view = new JsonModel(array('error'=>'Confirm password is not matching with New password'));
			return $view;
			
		}
		
		
		$res= $this->getUserTable()->updatePassword($uid,$newpassword);
		if($res){
		
			$view = new JsonModel(array('success'=>'Your password has been changed successfully'));
			return $view;
				
		}
		}else{
			$view = new JsonModel(array('error'=>'Please login to application before changing the password'));
			return $view;
		}
		
	}
	
	public function preferenceAction(){
		
		$this->layout('layout/user');
		$session = new Container ( 'user' );
		$username = $session->username;
		$uid = $session->userid;
		
		$isgen=0;
		$isuser=0;
		$avatar="";
		
		
		
		
		$request = $this->getRequest();
		
		if($request->isPost()){
			/*
			 * 
			 */
			
			$email = $this->params()->fromPost('email');
			$avatar = $this->params()->fromPost('avatar');
			
			$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
			
			if($email!=null && $email!=''){
			$sql = "UPDATE tbl_user SET email_id = '$email' WHERE user_id ='$uid'";
			$session->username=$email;
			$stmt = $adapter->createStatement($sql);
			$stmt->prepare();
			$result = $stmt->execute();
			}

			
			if($this->params()->fromPost('gennoti')=="general"){
				
				$isgen=1;
				
			}
			if($this->params()->fromPost('usernoti')=='user'){
				
				$isuser=1;
			}
			
			$sqlupdate = "UPDATE tbl_user SET isgensub = '$isgen',isusersub='$isuser' WHERE user_id ='$uid'";
			
			$stmt = $adapter->createStatement($sqlupdate);
			$stmt->prepare();
			$result = $stmt->execute();
			
			if($avatar!=''){
				$sql1 = "UPDATE tbl_user SET profile_picture = '$avatar' WHERE user_id ='$uid'";
				$session->profile_picture=$avatar;
				
				 $stmt = $adapter->createStatement($sql1);
				 $stmt->prepare();
				 $result = $stmt->execute(); 
			}
			
			
			
			
			$view = new ViewModel(array('success'=>'success','avatar'=>$avatar,'email'=>$email,"isgensub"=>$isgen,"isusersub"=>$isuser));
			return $view;
		}
		$result = $this->getUserTable()->getUserByUID($uid);
		foreach ($result as $row){
			$isgen = $row->isgensub;
			$isuser=$row->isusersub;
		}
		$view = new ViewModel(array('avatar'=>$avatar,'email'=>$username,"isgensub"=>$isgen,"isusersub"=>$isuser));
		return $view;
		
	}
	
	public function resetpasswordAction(){
		$this->layout('layout/empty');
		$request = $this->getRequest();
		if ($request->isGet()) {
			$key= $this->getRequest()->getQuery("key");
			$email= $this->getRequest()->getQuery("email");
			if($this->getUserTable()->getUserByKeyAndEmail($key,$email)){
				$view = new ViewModel(array("success"=>"Please proceed with password reset","email"=>$email,"key"=>$key));
				return $view;
			}else{
				$view = new ViewModel(array("error"=>"Reset password action key is not matchining. Please try again later"));
				return $view;
			}
		}
		if ($request->isPost()) {
			$username= $this->params()->fromPost('username');
			$password = $this->params()->fromPost('password');
			$repassword = $this->params()->fromPost('repassword');
			$key= $this->params()->fromPost('key');
			if($key==""){
				$view = new ViewModel(array("error"=>"Something wrong in reset password. Contact support center","email"=>$username,"postaction"=>true));
				return $view;
			}
			if($password!=$repassword){
			$view = new ViewModel(array("error"=>"Password is not matching","email"=>$username,"postaction"=>true));
			return $view;
			}else{
				$this->getUserTable()->resetByEmailandKey($username,$key,$password);
				$view = new ViewModel(array("success"=>"Password has been changed successfully","email"=>"","postaction"=>true));
				return $view;
			}
		}
	}
	
	
	public function logoutserviceAction(){
		session_start();
		$tmpsession = new Container('tmp');
		$tmpsession->exploreForm=null;
		session_destroy();
		
		$view = new JsonModel(array("success"=>"Your session has been ended."));
		return $view;
		
		//return $this->redirect()->toUrl('/');
	}
	
	public function logoutAction(){
		session_start();
		
		$tmpsession = new Container('tmp');
		$tmpsession->exploreForm=null;
		
		session_destroy();
		return $this->redirect()->toUrl('https://uceazy.com/?feedback=yes');
	}
	
	public function forgetpassAction()
	{
		$request = $this->getRequest();
		if ($request->isPost()) {
			try{
			$username= $this->params()->fromPost('username');
			
			if (!filter_var($username, FILTER_VALIDATE_EMAIL) === false) {
				
		}else{
			$result = new JsonModel(array(
						
					'error'=>"Invaild email address",
			));
			return  $result;
		}
			
			$key=md5(date('Y-m-d hh:mm:ss'));
			$this->getUserTable()->updateKeyByEmail($username,$key);
			$em=new EmailManager();
			$em->sendForgetPasswordMail($username, $key);
			
			$result = new JsonModel(array(
					
					'success'=>"We've sent an email. Please click the link in the email to reset your password",
			));
			return  $result;
			
			/* $view = new ViewModel(array("success"=>"Password reset information send to given email address"));
			return $view; */
			}catch(Exception $e){
				$result = new JsonModel(array(
							
						'error'=>"System internal error. Try some times later",
				));
			}
		}
	}
	
	public function verifyAction(){
		$request = $this->getRequest();
		if ($request->isGet()) {
			$key= $this->getRequest()->getQuery("key");
			$returncode=$this->getUserTable()->updateVerification($key);
     		if($returncode){
			 	$view = new ViewModel(array('success'=>'Successfully verified your email id'));
			 	return $view;
			 }else{
			 	$view = new ViewModel(array('error'=>'Invalid key'));
			 	return $view;
			 }
		}
	}
	
public function signupserviceAction(){

		$session = new Container('user');
		 $username= $session->username;
		 $userid=$session->userid;
			
		 $authusername = $session->username;
		 $network = $session->network;
		 $profilepic=$session->profilepic;
		 
		 $username= $this->params()->fromPost('username');
		 $password = $this->params()->fromPost('password');
		 $repassword = $this->params()->fromPost('repassword');
		 
		 $agecertify = $this->params()->fromPost('agecertify');
		 
		
		 
		 if($network!=''){
		 	if($authusername!=$username){
		 		$result = new JsonModel(array(
		 				'error' => "Authentication email is not maching"
		 		));
		 		return $result;
		 	}
		 }
		 
		 if (filter_var($username, FILTER_VALIDATE_EMAIL) === false) {
		 $result = new JsonModel(array(
		 'error' => "Please enter the valid email address."
		 ));
		 return $result;
		 }
		 
		 if($password!=$repassword && $password!=""){
		 $result = new JsonModel(array(
		 'error' => "Password is not matching.",
		 ));
		 return $result;
		 }

		 
		 if($agecertify == null || $agecertify == ''){
		 	$result = new JsonModel(array(
		 			'error' => "Please certify that your age atleast 13 and above.",
		 	));
		 	
		 	return $result;
		 }
		 
		 
		 if(!$this->getUserTable()->checkEmailAvailable($username)){
		 $key=md5(date('Y-m-d hh:mm:ss'));
		 
		 $session = new Container('global');
		 $regchannel=$session->channel;
		 if($network!=''){
		 $userid=$this->getUserTable()->insertStudent($username,$password,$key,Constants::ROLE_STUDENT,$regchannel,$profilepic,1);
		 }else{
		 	$userid=$this->getUserTable()->insertStudent($username,$password,$key,Constants::ROLE_STUDENT,$regchannel,$profilepic,0);
		 }
		 $this->getPlanTable()->insertUID($userid);
		 	
		 if($network!=''){
		 	$result = new JsonModel(array(
		 			'success' => "You have successfully registered.",
		 	));
		 	return $result;
		 }else{
		 $mail=new EmailManager();
		 $mail->sendMail($username, $username, $key);
		 $result = new JsonModel(array(
		 'success' => "You have successfully registered. Please check your Inbox for activation e-mail. Check the spam folder in case you don't see our email in your Inbox.",
		 ));
		 return $result;
		 }
		 }else{
		 $result = new JsonModel(array(
		 'error' => "This email ID already exists. Please enter a different one.",
		 ));
		 return $result;
		 }
		 	
		 //}
		 
	}
	public function signupAction()
	{
		$session = new Container('user');
		
		$session->network = "";
		
		$this->layout('layout/empty');
		
	
	}	
	public function confirmAction(){
		
	}
	
	public function homeAction()
	
	{
		$session = new Container('user');
		$tmpsession = new Container('tmp');
		
		$view = new ViewModel(array("username"=>$session->username,"userid"=>$session->userid));
		return $view;
	}
	
	
	public function signinserviceAction(){
		
		 $username= $this->params()->fromPost('username');
		 $password = $this->params()->fromPost('password');
		 $result=$this->getUserTable()->checkLogin($username,$password);
		 	
		 if(count($result)>0){
		 $session = new Container('user');
		 $session->username = $username;
		 
		 foreach ($result as $row) {
		 	$session->notification_data=$this->getNotifyData("cnt",$row->user_id);
		 	$session->profile_picture=$row->profile_picture;
		 	$session->userid= $row->user_id ;
		 	
		 	$this->getUserTable()->updateLastLogin($row->user_id);
		 
		 $result = new JsonModel(array(
		 'success' => "Successfully logged in",
		 ));
		 return $result;
		 }
		 }else{
		 $result = new JsonModel(array(
		 'error' => "Invalid login Attempt . Please provide valid credentials to login.",
		 ));
		 return $result;
		 } 
	}
	
	public function signinAction()
	{
		$this->layout('layout/empty');
		/* $username= $this->params()->fromPost('username');
		$password = $this->params()->fromPost('password');
		$result=$this->getUserTable()->checkLogin($username,$password);
		 
		if(count($result)>0){
			$session = new Container('user');
			$session->username = $username;
			foreach ($result as $row) {
				$session->userid= $row->user_id ;
				$result = new JsonModel(array(
						'success' => "",
				));				
				$session->notification_data=$this->getNotifyData("cnt",$row->user_id);
				return $result;
			}
		}else{
			$result = new JsonModel(array(
					'error' => "Invalid login Attempt . Please provide valid credentials to login.",
			));
			return $result;
		} */
	}

	public function indexAction()
	{		
		$session = new Container('user');		
		$userid=$session->userid;
		$this->layout('layout/user');
		
		$username = $session->username;
		$uid = $session->userid;
		
		if ($uid == '' || $uid == null) {
			return $this->redirect ()->toUrl ( '/' );
		}
		$service_id=1;
		$open_essays=$this->getEssayTable()->get_openEssays($uid);
		$service_cnt=$this->getEssayTable()->getservicecount_user($uid,$service_id);
		
    	$notification_data=$this->getNotifyData("all",$userid);
    	$view = new ViewModel(array(
    			'notification_data' =>$notification_data,
    			'open_essay_cnt' =>$open_essays->count(),
    			'service_cnt' => $service_cnt->count(),
    	));
		
		
		return $view;
		//return new ViewModel();
	}
	
	function getAllServiceAction(){
		$service_data=array();
		$service_res=$this->getServiceMasterTable()->getAllServices();
		foreach ($service_res as $row){
			$service_data[]=$row;
		}
		$view = new JsonModel(array("service_data"=> $service_data,"cnt"=>count($service_data)));		
		return $view;	
	}
	
	
	function readNotificationAction(){
		$session = new Container('user');
		$userid=$session->userid;
		$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
		$note_id=trim ( $this->params ()->fromPost ( 'note_id' ) );	
		$stmt = $adapter->createStatement("UPDATE user_notification_history SET is_read = 'yes' WHERE id = $note_id");
		$stmt->prepare();
		$result = $stmt->execute();
		$session->notification_data=$this->getNotifyData("cnt",$userid);	
		$view = new JsonModel(array("result"=>$result->count()));		
		return $view;		
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
	
function Timesince($original)
{
  $ret_txt  = '';
  $dateobj1 = new \DateTime();
  $now      = $dateobj1->format('Y-m-d H:i:s');
  
  $dateobj2 = new \DateTime($original);
  $past     = $dateobj2->format('Y-m-d H:i:s');
  $date1    = new \DateTime($past);
  $date2    = new \DateTime($now);
  $interval = $date1->diff($date2);
  if ($interval->y > 0) {    
	 $ret_txt = '<small class="label label-default"><i class="fa fa-clock-o"></i>'. $interval->y . '&nbsp;year</small>';
  } else if ($interval->m > 0) {
    if ($interval->m == 1)      
	 $ret_txt = '<small class="label label-default"><i class="fa fa-clock-o"></i>'. $interval->m . '&nbsp;month</small>';
    if ($interval->m > 1)      
	 $ret_txt = '<small class="label label-default"><i class="fa fa-clock-o"></i>'. $interval->m . '&nbsp;months</small>';	
  } else if ($interval->d > 0) {
    $day = $interval->d;
    if ($day > 0 && $day <= 6) {     
	  $ret_txt = '<small class="label label-warning"><i class="fa fa-clock-o"></i>'. $day . '&nbsp;day</small>';
    }
    if ($day >= 7) {
      $week = round($day / 7);
      if ($week == 0)
        $ret_txt = ' 1&nbsp;Week ';
		 $ret_txt = '<small class="label label-primary"><i class="fa fa-clock-o"></i> 1&nbsp;week</small>';
      if ($week >= 1)      
		 $ret_txt = '<small class="label label-primary"><i class="fa fa-clock-o"></i>'. $week . '&nbsp;weeks</small>';
    }

  } else if ($interval->d == 0 && $interval->h > 0) {    
	$ret_txt = '<small class="label label-info"><i class="fa fa-clock-o"></i>'. $interval->h . '&nbsp;hours</small>';
  } else if ($interval->i >= 0) {
    $ret_txt = '<small class="label label-primary"><i class="fa fa-clock-o"></i>'. $interval->i . '&nbsp;mins</small>';
	
  }
  return $ret_txt;
}
	/* function readNotificationAction(){
		$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
		$note_id=trim ( $this->params ()->fromPost ( 'note_id' ) );	
		$stmt = $adapter->createStatement("UPDATE user_notification_history SET is_read = 'yes' WHERE id = $note_id");
		$stmt->prepare();
		$result = $stmt->execute();		
		$view = new JsonModel(array("result"=>$result->count()));
		return $view;		
	} */

public function essayserviceAction(){
$view = new ViewModel();
$this->layout ( "layout/user" );

return $view;

}

public function buyAction(){
$view = new ViewModel();
$this->layout ( "layout/user" );

return $view;

}
	
}