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
use Zend\Math\Rand;
use Zend\Escaper\Escaper;


class BuyserviceController extends AbstractActionController {

    protected $buyerTable;
    protected $essayTable;
    protected $serviceMasterTable;
    protected $couponTable;
    
    
    public function getBuyerTable()
    {
        if (!$this->buyerTable) {
            $sm = $this->getServiceLocator();
            $this->buyerTable = $sm->get('BuyerService');
        }
        return $this->buyerTable;
    }
    
	public function getEssayTable()
    {
        if (!$this->essayTable) {
            $sm = $this->getServiceLocator();
            $this->essayTable = $sm->get('EssayService');
        }
        return $this->essayTable;
    }

    public function getServiceMasterTable()
    {
    	if (!$this->serviceMasterTable) {
    		$sm = $this->getServiceLocator();
    		$this->serviceMasterTable = $sm->get('ServiceMasterService');
    	}
    	return $this->serviceMasterTable;
    }

    public function getcouponTable()
    {
    	if (!$this->couponTable) {
    		$sm = $this->getServiceLocator();
    		$this->couponTable = $sm->get('CouponService');
    	}
    	return $this->couponTable;
    }
    
   	public function buyEssayAction(){  	
        $session = new Container ('user');
        $username = $session->username;
        $uid = $session->userid;
    	$eligible='no';
        if ($uid == '' || $uid == null) {
            return $this->redirect ()->toUrl ( '/user/signin' );
        }
        $this->layout ( "layout/user" );       
               
        $open_essays=$this->getEssayTable()->get_openEssays($uid);         
        if($open_essays->count()<=0){
        	$eligible='yes';
        }
        $service_res=$this->getServiceMasterTable()->getServices_data("service_name","Essay Service");
        $service_id=$service_cost=0;$service_name='';
        foreach ($service_res as $row){
        	$service_id=$row->service_id;
        	$service_name=$row->service_name;
        	$service_cost=$row->service_cost;
        }
        $sys_data=array();$coupon_avil='no';
        $service_cnt=$this->getEssayTable()->getservicecount_user($uid,$service_id);        
        
        /*if($service_cnt->count()>0){   
	        $res_data=$this->getcouponTable()->get_system_coupon();
	        foreach ($res_data as $row){
	        	$sys_data=$row;
	        }unset($row);	  
        }
        if(count($sys_data)>0){
        	$coupon_avil='yes';
        }
        
        
        $view = new ViewModel ( array (
            'uid' => $uid,
        	'eligible'=>$eligible,
        	'cnt'=>$open_essays->count(),
        	'is_desktop'=>$this->findDevice(),
        	'service_id'=>$service_id,
        	'service_name'=>$service_name,
        	'service_cost'=>$service_cost,
        	'coupon_avil'=>$coupon_avil,
        	'coupon_data'=>$sys_data,       
        ) );
        */
        $coup_data=array();
        if($service_cnt->count()>0){
        	$coup_data_res=$this->getcouponTable()->get_general_coupon("recursiveUser");
        	
        }else {
        	$coup_data_res=$this->getcouponTable()->get_general_coupon("firstTimeuser");
        }
        foreach ($coup_data_res as $row){
        	$coup_data=$row;
        }unset($row);        
        
        $view = new ViewModel ( array (
            'uid' => $uid,
        	'eligible'=>$eligible,        	
        	'is_desktop'=>$this->findDevice(),
        	'service_id'=>$service_id,
        	'service_name'=>$service_name,
        	'service_cost'=>$service_cost,
        	'coupon_data'=>$coup_data,
        
        	       
        ) );
        
        return $view;
   	}
    
    
    
    function cancelAction() {
        $this->layout ( "layout/user" );
        $session = new Container ( 'user' );
        $username = $session->username;
        $uid = $session->userid;
    
        if ($uid == '' || $uid == null) {
            return $this->redirect ()->toUrl ( '/user/signin' );
        }
        $temp= substr((Rand::getString(32, 'abcdefghijklmnopqrstuvwxyz', true)),0,5);
        $session->cancel_flag = $temp;
        return $this->redirect ()->toUrl ( '/buyservice/buyEssay?cancel-flag='.$temp );
    
    }
    
    
    
    function paymentCompleteAction() {
        $this->layout ( "layout/user" );
        $session = new Container ( 'user' );
        $username = $session->username;
        $uid = $session->userid;
    
        if ($uid == '' || $uid == null) {
            return $this->redirect ()->toUrl ( '/user/signin' );
        }
        
        if(isset($_POST) && isset($_POST['payment_status'])  && isset($_POST['custom']) && $_POST['custom']!=''){
            $data=array();
            $status=0;            
            $payment_date=date("Y-m-d H:i:s", strtotime(trim($_POST['payment_date'])));
            $payment_status=trim($_POST['payment_status']);
            $transaction_id=trim($_POST['txn_id']);
            $payment_type=trim($_POST['payment_type']);
            $payment_fee=trim($_POST['payment_fee']);
            $txn_type=trim($_POST['txn_type']);
            $payment_gross=trim($_POST['payment_gross']);
            $curency_type=trim($_POST['mc_currency']);
            $payer_email=trim($_POST['payer_email']);
            $service_details=explode("&&", trim($_POST['custom']));
            $service_id=$service_details[0];
            $service_name=$service_details[1];
            $user_id=$service_details[2];
            if(isset($service_details[3])){
            	$coupon_id=$service_details[3];
            }else{
            	$coupon_id=1;
            }            
            
            $service_res=$this->getServiceMasterTable()->getServices_data("service_id",$service_id);
            foreach ($service_res as $row){
            	$service_price=$row->service_cost;
            }unset($row);
            if(isset($session->coupon_used) && $session->coupon_used=='yes'){
            	$service_price=$session->ser_cost;
            }
            
            if(!($payment_gross>=$service_price) && $user_id==$uid && $service_id>0 && $service_name!='' ){
            	$payment_status='custom';
            }
        		
            if($payment_status=="Completed" || $payment_status=="In progress" || $payment_status=="Processing" || $payment_status=='custom'){
            	$order_data=$this->getBuyerTable()->check_order($uid,$transaction_id,$payment_date);
            	if($order_data->count()<=0){
            		$data=array(
	                "user_id"=>$user_id,
	            	"service_id"=>$service_id,
	                "payment_date"=>$payment_date,
	                "payment_status"=>$payment_status,
	                "transaction_id"=>$transaction_id,
	                "payment_type"=>$payment_type,
	                "payment_fee"=>$payment_fee,
	                "txn_type"=>$txn_type,
	                "payment_gross"=>$payment_gross,
	                "curency_type"=>$curency_type,
	            	"user_email"=>$username,
	            	"paypal_id"=>$payer_email,
	            	"serviceType"=>$service_name,
	            	"coupon_id"=>$coupon_id,
	            	"is_accessed"=>"no"	            
	            	);
	            	 
	            	 
	            	 
	            	$buy_row_id=$this->getBuyerTable()->insertBuyer($data);

	            	if($buy_row_id>0){
	            		if($payment_status=="Completed" || $payment_status=="In progress" || $payment_status=="Processing" ){
	            			$e_pay_stat='paid';
	            		}
	            		else if($payment_status==='custom'){
	            			$e_pay_stat='un-paid';
	            		}else{
	            			$e_pay_stat='un-paid';
	            		}
	            		$data=array(
			        "user_id" => $uid,
			        "buy_id"=>$buy_row_id,
	            	"service_id"=>$service_id,
			        "user_input1" => '',
			        "user_input2" => '',
			        "essay_comment1" => "",
			        "essay_comment2" => "",
			        "feedback" =>"",
			        "submit_date"=>'',
			        "review_date"=>"",
			        "status" => $e_pay_stat
	            		);
	            		$last_id=$this->getEssayTable()->insertEssay($data);
	            	}
	            	if($e_pay_stat=='paid'){
	            		$em=new EmailManager();
	            		$em->send_note_to_user("purchased",$username,"user");
	            		$temp= substr((Rand::getString(32, 'abcdefghijklmnopqrstuvwxyz', true)),0,5);
	            		$session->success_flag = $temp;
	            		return $this->redirect ()->toUrl ( '/buyservice/paymentStatus?flag='.$temp );
	            	}else{
	            		$temp= substr((Rand::getString(32, 'abcdefghijklmnopqrstuvwxyz', true)),0,5);
	            		$session->custom_flag = $temp;
	            		return $this->redirect ()->toUrl ( '/buyservice/buyEssay?flag='.$temp );
	            		 
	            	}
            	}
            	else{
            		$temp= substr((Rand::getString(32, 'abcdefghijklmnopqrstuvwxyz', true)),0,5);
            		$session->success_flag = $temp;
            		return $this->redirect ()->toUrl ( '/buyservice/paymentStatus?flag='.$temp );
            		 
            	}
            	 
            }else{

            	$temp= substr((Rand::getString(32, 'abcdefghijklmnopqrstuvwxyz', true)),0,5);
            	$session->failed_flag = $temp;
            	$session->failed_reason = $payment_status;
            	return $this->redirect ()->toUrl ( '/buyservice/buyEssay?fail-flag='.$temp );
            	 
            }

        }else{
        	$res=$this->getBuyerTable()->is_service_accessed($uid);
        	$row_id=0;
        	foreach ($res as $row){
        		if($row->id>0){
        			$row_id=$row->user_id;
        			break;
        		}unset($row);
        	}
        	return $this->redirect ()->toUrl ('/buyservice/essay');

        }
        return $this->redirect ()->toUrl ('/buyservice/buyEssay');

    }
    
    
     function essayAction() {
     	$escaper = new Escaper('utf-8');
     	$this->layout ( "layout/user" );
        $session = new Container ( 'user' );
        $username = $session->username;
        $uid = $session->userid;    		
        
        if ($uid == '' || $uid == null) {
            return $this->redirect ()->toUrl ( '/user/signin' );
        }
        
        
        
        $mode=trim ( $this->params ()->fromPost ( 'mode' ) );
        if($mode=="insert" || $mode=="update"){        	
        	$status=0;
        	$user_text1= htmlspecialchars(trim ( $this->params ()->fromPost ( 'box1_data' ) ));
        	$user_text2= htmlspecialchars(trim ( $this->params ()->fromPost ( 'box2_data' ) ));
        	$uid=trim ( $this->params ()->fromPost ( 'uid' ) );
        	$pay_row_id=trim ( $this->params ()->fromPost ( 'pay_row_id' ) );
        	$action=trim ( $this->params ()->fromPost ( 'clicked_btn' ) );
        	$essay_id=trim ( $this->params ()->fromPost ( 'essay_id' ) );
        	$user_text1=$escaper->escapeHtml($user_text1);
        	$user_text2=$escaper->escapeHtml($user_text2);
        	
	        $data=array(
	        "user_id" => $uid,
	        "buy_id"=>$pay_row_id,
	        "user_input1" => $user_text1,
	        "user_input2" => $user_text2,
	        "essay_comment1" => "",
	        "essay_comment2" => "",
	        "feedback" =>"",
	        "submit_date"=>date("y-m-d"),
	        "review_date"=>"",
	        "status" => "submitted"
	        );	       
	        if(isset($action) && $action=='save'){
	        	$data['status']='saved';
	        	if($mode=='update'){
	        		$last_id=$this->getEssayTable()->updateEssay($data,$essay_id);
	        		$last_id= $last_id>0 ? $last_id : 1;
	        	}	 
	        	 $view = new JsonModel ( array (
				"status" => $last_id,
	        	"essay_status"=>"saved",
	        	
	        	) );
	        	return $view;
	        }
	        if(isset($action) && $action=='submit'){
	        	if($mode=='update'){
	        		$last_id=$this->getEssayTable()->updateEssay($data,$essay_id);
	        		$last_id= $last_id>0 ? $last_id : 1;
	        	}
		        if($last_id>0 && $essay_id>0){
		        	$to=Constants::ESSAY_ALERT_EMAIL;
		        	$em=new EmailManager();
		        	$em->send_note_to_essayTeam($uid,$essay_id,html_entity_decode($user_text1),html_entity_decode($user_text2),$to);
		        	$em->send_note_to_user("submitted",$username,"user");
		        	$em->send_note_to_user("submitted",Constants::ESSAY_GROUP_EMAIL,"group");
		        	
		        }
		         
		        $view = new JsonModel ( array (
					"status" => $last_id,
		        	"essay_status"=>"submitted",
		        ) );
		        return $view;
	       }
        }else{
        	$res=$this->getBuyerTable()->is_service_accessed($uid);        	
        	$buy_row_id=0;
        	foreach ($res as $row){
        		if($row->id>0){
        			$buy_row_id=$row->id;       			
        		}
        	}unset($row);
        	if($buy_row_id>0){
        		$essay_data=$this->getEssayTable()->get_essay("buy_id",$buy_row_id,$uid);
        		$flag='';
        		if($essay_data->count()<=0){
        			$flag="paid";
        			$mode='insert';
        		}else{
        			$flag="saved";
        			$mode='update';
        		}        		
        		$view = new ViewModel ( array (
	            'uid' => $uid,
				'buy_row_id'=>$buy_row_id,
	        	'essay_status' =>$flag,
	        	'essay_data'=>$essay_data,
        		'mode'=>$mode,        
	        	) );
	        	return $view;
        	}else{
        		return $this->redirect ()->toUrl ('/buyservice/buyEssay');
        	}
        }
     }
     
     function updateEssayFeedbackAction(){
     	$escaper = new Escaper('utf-8');
     	$this->layout ( "layout/user" );
        $session = new Container ( 'user' );
        $username = $session->username;
        $uid = $session->userid;
    
        if ($uid == '' || $uid == null) {
            return $this->redirect ()->toUrl ( '/user/signin' );
        }
     	$mode=trim ( $this->params ()->fromPost ( 'mode' ) );
        if($mode=='feedback_submit'){
        	$status=0;
        	$feed_text=htmlspecialchars(trim ( $this->params ()->fromPost ( 'feedback_text' ) ));        	
        	$essay_id=trim ( $this->params ()->fromPost ( 'essay_id' ) );
        	$pay_row_id=trim ( $this->params ()->fromPost ( 'pay_row_id' ) );
        	$feed_text=$escaper->escapeHtml($feed_text);
        	$status=$this->getEssayTable()->updateEssayFeedback($feed_text,$essay_id);
        	$status1=$this->getBuyerTable()->service_status_update($pay_row_id); 
        	if($status>0){
        		$em=new EmailManager();        		
        		$em->send_note_to_user("submitted",$username,"feedBack");
        		$em->send_feedbackToAdmin($essay_id,$feed_text,Constants::ESSAY_ALERT_EMAIL);
        	}
        	$view = new JsonModel ( array (
				"status" => $status,
        		"essay_status"=>"completed"
        	 
	        ) );
	        return $view;
        }
        
     }
     
     
     function viewessayAction(){
     	 
     	$this->layout ( "layout/user" );
     	$session = new Container ( 'user' );
     	$username = $session->username;
     	$uid = $session->userid;
     	if ($uid == '' || $uid == null) {
     		return $this->redirect ()->toUrl ( '/user/signin' );
     	}
     	
     	if(isset($_GET['essay']) && $_GET['essay']!=''){
     		$status=0;
     		$essay_id=trim($_GET['essay']);
     		$essay_data=$this->getEssayTable()->get_essay("essay_id",$essay_id,$uid);
     		 
     		if($essay_data->count()>0) {
     			$view = new ViewModel ( array (
				"essay_data" =>$essay_data,
        		'avil_data'=>'yes',
     			) );
     			return $view;
     			 
     		}else{
     			$view = new ViewModel ( array (
        		'avil_data'=>'no',    			  

     			) );
     			return $view;
     		}

     	}
     }
     
     function getHistoryAjaxAction(){
     	
     	$this->layout ( "layout/user" );
     	$session = new Container ( 'user' );
     	$username = $session->username;
     	$uid = $session->userid;
     	if ($uid == '' || $uid == null) {
     		return $this->redirect ()->toUrl ( '/user/signin' );
     	}
     	$content='';
     	$action=trim ( $this->params ()->fromPost ( 'action' ) );
     	if($action=='from_ajax'){
     		$history_data=$this->getEssayTable()->get_essay_history($uid);     		
     		$history_data_cpy=array();
     		foreach ($history_data as $row){
     			$history_data_cpy[]=$row;
     		}unset($row);
     		if(count($history_data_cpy)>0){
     			 $ii=1;
     			 foreach ($history_data_cpy as $data){ 
					$content.='<tr>
						<td>'. $ii.'</td>
						<td> Service-'.$ii.'</td>';
						
						if( $this->validateDate($data['submit_date'])){
							$s_date=date("m-d-Y",strtotime($data['submit_date']));
							$str='';
						}else{
							$s_date="- -";
							$str='align="center"';
						}						
						$content.='<td '.  $str.'>'. $s_date.'</td>';
						
						if( $this->validateDate($data['review_date'])){
								$r_date=date("m-d-Y",strtotime($data['review_date']));
								$str='';										
								}else{
									$r_date="- -";
									$str='align="center"';	
								}
						$content.='<td '.  $str.'>'. $r_date.'</td>
						<td>$'.$data['payment_gross'].'</td>
						<td>'. ucfirst($data['status'])
						.'<td>';
						 if($data['status']=='completed' || $data['status']=='reviewed'){
						$content.='<a href="/buyservice/viewessay?essay='. $data['essay_id'].'">View</a>';
						 }elseif ($data['status']=='un-paid'){
						 $content.='<a href="/buyservice/buyEssay">View</a>';
						 
						 }else{
							$content.='<a href="/buyservice/essay">View</a>';
						 }
						$content.='</td>
						
					</tr>';
					
					 $ii+=1;
     			 }unset($data);
     		}else{
     			$content="<tr><td colspan='7'>No Data Found<td>";
     		} 
     	}else{
     		$content="<tr><td colspan='7'>INVALID REQUEST<td>";
     	}
     	$view = new JsonModel(array("result"=>$content));
		return $view;
     
     } 
     
     function validateDate($date)     {
     	 $d=date("Y",strtotime($date));     
     	return $d>=2015; 
     }
     
     
     function findDevice(){

     	$tablet_browser = 0;
     	$mobile_browser = 0;
     	$device="desktop";
     	if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
     		$tablet_browser++;
     	}

     	if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
     		$mobile_browser++;
     	}

     	if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
     		$mobile_browser++;
     	}

     	$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
     	$mobile_agents = array(
		    'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
		    'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
		    'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
		    'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
		    'newt','noki','palm','pana','pant','phil','play','port','prox',
		    'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
		    'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
		    'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
		    'wapr','webc','winw','winw','xda ','xda-');

     	if (in_array($mobile_ua,$mobile_agents)) {
     		$mobile_browser++;
     	}

     	if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0) {
     		$mobile_browser++;
     		//Check for tablets on opera mini alternative headers
     		$stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
     		if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
     			$tablet_browser++;
     		}
     	}

     	if ($tablet_browser > 0) {
     		// do something for tablet devices
     		$device='tablet';
     	}
     	else if ($mobile_browser > 0) {
     		// do something for mobile devices
     		$device='mobile';
     	}
     	else {
     		// do something for everything else
     		$device='desktop';
     	}
     	return $device;

     }
     
    /* function applyCouponAction(){
     	$service_id=$couponCode='';
     	$this->layout ( "layout/user" );
     	$session = new Container ( 'user' );
     	$username = $session->username;
     	$uid = $session->userid;

     	if ($uid == '' || $uid == null) {
     		return $this->redirect ()->toUrl ( '/user/signin' );
     	}
     	$service_id=(trim ( $this->params ()->fromPost ( 'ser_id' ) ));
     	$couponCode=(trim ( $this->params ()->fromPost ( 'coup_val' ) ));
     	$code_res=$this->getcouponTable()->getCoupon("code",$couponCode);
     	$service_res=$this->getServiceMasterTable()->getServices_data("service_id",$service_id);
     	if($service_res->count()>0){
     		foreach ($service_res as $row){
     			$service_cost=$row->service_cost;
     		}unset($row);
     		 
     	}

     	if($code_res->count()>0 && $service_res->count()>0){
     		foreach ($code_res as $row){
     			$coup_id=$row->coupon_id;
     			$coupon_status=$row->coupon_status;
     			$start_date=$row->start_date;
     			$end_date=$row->end_date;
     			$discount_value=$row->discount_value;
     			$discount_type=$row->discount_type;
     			$curency_type=$row->curency_type;     			
     			$coupon_type=$row->coupon_type;
     			$service_type_ids=$row->service_type_ids;
     		}unset($row);
     		$id_list=array();
     		$id_list=explode(",", $service_type_ids);
     		$today = date('Y-m-d');
     		$today=date('Y-m-d', strtotime($today));
     		$start_date = date('Y-m-d', strtotime($start_date));
     		$end_date = date('Y-m-d', strtotime($end_date));

     		if($coupon_status=="expired" || (!($today>=$start_date && $today<=$end_date))){
     			$view = new JsonModel ( array (
				"status" => "expired",
     			) );
     			return $view;
     		}else if(!in_array($service_id, $id_list)){
     			$view = new JsonModel ( array (
				"status" => "notFor",
     			) );
     			return $view;
     		}else{

     			if($coupon_type=='system'){
     				$service_cnt=$this->getEssayTable()->getservicecount_user($uid,$service_id);
     				if($service_cnt->count()<=0){
     					$view = new JsonModel ( array (
						"status" => "noteligible",		     			
     					) );
     					return $view;
     				}
     				 
     			}
     			if($discount_type=="flat"){
     				$service_cost=$service_cost-$discount_value;
     			}
     			if($discount_type=="percentage"){
     				$service_cost=round($service_cost-(($service_cost*$discount_value)/100),2);
     			}
     			$session->coupon_used="yes";
     			$session->ser_cost=$service_cost;
     			$view = new JsonModel ( array (
				"status" => "valid",
     			"new_cost"=>$service_cost,
     			"coup_id"=>$coup_id
     			) );
     			return $view;     			 
     		}
     	}else{
     		$view = new JsonModel ( array (
				"status" => "invalid",
     		) );
     		return $view;
     	}
     	$view = new JsonModel ( array (
				"status" => "invalid",
     	) );
     	return $view;
     }*/

     
     public function paymentStatusAction(){
     	$view = new ViewModel ();
     			return $view;
     	
     }
     
     public function notifyOthersAction(){
     	$this->layout ( "layout/user" );
     	$session = new Container ( 'user' );
     	$username = $session->username;
     	$uid = $session->userid;

     	if ($uid == '' || $uid == null) {
     		return $this->redirect ()->toUrl ( '/user/signin' );
     	}
     	
     	$id=(trim ( $this->params ()->fromPost ( 'mail_id' ) ));
     	if($id!=''){
     		$em=new EmailManager();
     		$em->send_note_to_user("notify_other",$id,"user");
     		 
     		$view = new JsonModel ( array (
				"status" => "sent",
     		) );
     		return $view;
     	}else {
     		$view = new JsonModel ( array (
				"status" => "invalid",
     		) );
     		return $view;
     	}
     	
     	
     }
      

}
