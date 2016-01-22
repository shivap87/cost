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
use Common\Utilities\Convertors;
class AdminController extends AbstractActionController
{
	
	function crAction(){
		
		$email=$this->getRequest()->getQuery("access");
		$isAccess = Convertors::checkReportAccess($email);
		if($isAccess == false){
			return $this->redirect()->toUrl('/');
		}
		
		$this->layout("layout/empty");
		
		$adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
		$totalChannel = "select regchannel, count(*) as total from tbl_user where regchannel is not null group by regchannel";
		
		$stmt = $adapter->createStatement($totalChannel);
		$stmt->prepare();
		$totalResut = $stmt->execute();
		
		$signupUserSQL="SELECT DATE_FORMAT(regdate,'%m-%d-%Y') as RegDate, COUNT(CASE WHEN regchannel = 'Facebook' then 1 ELSE NULL END) as FaceBook, COUNT(CASE WHEN regchannel = 'Email' then 1 ELSE NULL END) as Email, COUNT(CASE WHEN regchannel = 'PTA' then 1 ELSE NULL END) as 'PTA' , COUNT(CASE WHEN regchannel = 'SchoolCounsellors' then 1 ELSE NULL END) as 'SchoolCounsellors', COUNT(CASE WHEN regchannel = 'WEB' then 1 ELSE NULL END) as 'WEB', COUNT(CASE WHEN regchannel = 'SingTao' then 1 ELSE NULL END) as 'SingTao', COUNT(CASE WHEN regchannel = 'Youtube' then 1 ELSE NULL END) as 'Youtube' , COUNT(CASE WHEN regchannel = 'FremontHighSchool' then 1 ELSE NULL END) as 'FremantSchool', COUNT(CASE WHEN regchannel = 'FriendsFamily' then 1 ELSE NULL END) as 'FriendFamily' from tbl_user where regdate is not null and regchannel !='' GROUP BY DATE_FORMAT(regdate,'%m-%d-%Y')";
		
		$signInUserSQL = "SELECT DATE_FORMAT(lastlogin,'%m-%d-%Y') as LastLogin,COUNT(CASE WHEN regchannel = 'Facebook' then 1 ELSE NULL END) as 'FaceBook', COUNT(CASE WHEN regchannel = 'Email' then 1 ELSE NULL END) as 'Email',COUNT(CASE WHEN regchannel = 'PTA' then 1 ELSE NULL END) as 'PTA' ,COUNT(CASE WHEN regchannel = 'SchoolCounsellors' then 1 ELSE NULL END) as 'SchoolCounsellors' ,COUNT(CASE WHEN regchannel = 'WEB' then 1 ELSE NULL END) as 'WEB' ,COUNT(CASE WHEN regchannel = 'SingTao' then 1 ELSE NULL END) as 'SingTao',COUNT(CASE WHEN regchannel = 'Youtube' then 1 ELSE NULL END) as 'Youtube',COUNT(CASE WHEN regchannel = 'FremontHighSchool' then 1 ELSE NULL END) as 'FremantSchool',COUNT(CASE WHEN regchannel = 'FriendsFamily' then 1 ELSE NULL END) as 'FriendFamily' from tbl_user where lastlogin is not null and regchannel !='' GROUP BY DATE_FORMAT(lastlogin,'%m-%d-%Y') ";
		
		
		$channelTraficSQL = "select channelname,count(*) as total from tbl_channel_access GROUP BY channelname";
		
		$channelTrafficdDaywiseSQL="SELECT DATE_FORMAT(accesstime,'%m-%d-%Y') as RegDate, COUNT(CASE WHEN channelname = 'Facebook' then 1 ELSE NULL END) as FaceBook, COUNT(CASE WHEN channelname = 'Email' then 1 ELSE NULL END) as Email, COUNT(CASE WHEN channelname = 'PTA' then 1 ELSE NULL END) as 'PTA' , COUNT(CASE WHEN channelname = 'SchoolCounsellors' then 1 ELSE NULL END) as 'SchoolCounsellors', COUNT(CASE WHEN channelname = 'WEB' then 1 ELSE NULL END) as 'WEB', COUNT(CASE WHEN channelname = 'SingTao' then 1 ELSE NULL END) as 'SingTao', COUNT(CASE WHEN channelname = 'Youtube' then 1 ELSE NULL END) as 'Youtube' , COUNT(CASE WHEN channelname = 'FremontHighSchool' then 1 ELSE NULL END) as 'FremantSchool', COUNT(CASE WHEN channelname = 'FriendsFamily' then 1 ELSE NULL END) as 'FriendFamily' from tbl_channel_access where channelname !='' GROUP BY DATE_FORMAT(accesstime,'%m-%d-%Y')";
		

		$stmt = $adapter->createStatement($channelTraficSQL);
		$stmt->prepare();
		$channelTraficResut = $stmt->execute();
		
		$stmt = $adapter->createStatement($signupUserSQL);
		$stmt->prepare();
		$signupResult = $stmt->execute();
		
		$stmt = $adapter->createStatement($signInUserSQL);
		$stmt->prepare();
		$signInResult = $stmt->execute();
		
		
		$completeListSQL="SELECT `email_id`,DATE_FORMAT(regdate,'%m-%d-%Y') as regdate,DATE_FORMAT(lastlogin,'%m-%d-%Y') as lastlogin,regchannel FROM `tbl_user` WHERE regchannel is not null order by regchannel";
		$stmt = $adapter->createStatement($completeListSQL);
		$stmt->prepare();
		$completeList = $stmt->execute();
		
		$stmt = $adapter->createStatement($channelTrafficdDaywiseSQL);
		$stmt->prepare();
		$trafficdaywise = $stmt->execute();
		
		return new ViewModel(array("total"=>$totalResut,'channeltrafic'=>$channelTraficResut,'signup'=>$signupResult,'signin'=>$signInResult,"completeList"=>$completeList,'trafficdaywise'=>$trafficdaywise));
		
		
		
	}
	
}