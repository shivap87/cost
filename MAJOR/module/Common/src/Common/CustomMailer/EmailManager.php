<?php
namespace Common\CustomMailer;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mail;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Config\Config;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Mail\Transport;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Common\ActionForms\ExplorerForm;
use Common\Utilities;
use Common\Utilities\Convertors;
use Common\Utilities\Constants;
class EmailManager 
{
	
	public $options = array(
			'host' => 'smtp.gmail.com',
			'port' => 587,
			'connection_class' => 'login',
			'connection_config' => array(
					'username' => 'Essay_admin@uceazy.com',
					'password' => 'counselor#1',
					'ssl'      => 'tls',
					
			));

	function sendPersonalStatement($to,$name,$body){
		$smtpOptions = new Transport\SmtpOptions($this->options);
		$smtp = new Transport\Smtp($smtpOptions);
		$htmlBody=$body;;
		$htmlPart = new MimePart($htmlBody);
		$htmlPart->type = "text/html";
		$body = new MimeMessage();
		$body->setParts(array($htmlPart));
		$mail = new Mail\Message();
		$mail->setBody($body);
		$mail->setFrom('noreply@uceazy.com', 'noreply@uceazy.com');
		$mail->addTo($to, $name);
		$mail->setSubject('UCEazy - Your personal statement summary ');
		$smtp->send($mail);
		
	}
	
	function sendMail($to,$name,$key){
		
	$smtpOptions = new Transport\SmtpOptions($this->options);
	$smtp = new Transport\Smtp($smtpOptions);
	$link = "<a href='".Constants::SITE_BASE_URL."/?action=verify&key=".$key."'>link</a>";
	$htmlBody=$this->getSignUpTemplate();
	$explore_link= "<a href='".Constants::SITE_BASE_URL."/search/advexplorer'>EXPLORE</a>";
	$plan_link= "<a href='".Constants::SITE_BASE_URL."/plan/index'>PLAN</a>";
	$review_link= "<a href='".Constants::SITE_BASE_URL."/index/essayreview'>REVIEW</a>";
	$htmlBody = str_replace("<<ACTIVATELINK>>", $link, $htmlBody);
	$htmlBody = str_replace("<<ADV_EXPLORE>>", $explore_link, $htmlBody);
	$htmlBody = str_replace("<<PLAN>>", $plan_link, $htmlBody);
	$htmlBody = str_replace("<<REVIEW>>", $review_link, $htmlBody);
	$htmlPart = new MimePart($htmlBody);
	$htmlPart->type = "text/html";
	$body = new MimeMessage();
	$body->setParts(array($htmlPart));
	$mail = new Mail\Message();
	$mail->setBody($body);
	$mail->setFrom('noreply@uceazy.com', 'noreply@uceazy.com');
	$mail->addTo($to, $name);
	$mail->setSubject('Activate Your UCEazy Account');
	$smtp->send($mail);
	
	}
	
	function getSignUpTemplate(){
		$msg="<html>
		<head>
		<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
		<title>UCEazy - Your Result</title>
		<meta name='viewport' content='width=device-width, initial-scale=1.0'/>
		</head>
		<body style='margin: 0; padding: 0; '>
		<table align='center' border='0' cellpadding='0' cellspacing='0' width='800' style='border-collapse: collapse; '>
		<tr>
		<td>
		Hi there,
		</td>
		</tr>
		<tr>
		<td>
				<br>
				Welcome to UCEazy! We are thrilled to be a part of your college application process, and hope you find our tools helpful.
				<br><br>
				
				You are receiving this email because you indicated interest in creating an account with UCEazy. <br>
				If that is the case, please follow this <<ACTIVATELINK>>   to activate your account.
				<br><br>
				Now that you’ve got an account, you have access to our EXPLORE and PLAN modules.
				<br><br> 
				In EXPLORE, you can create your perfect college list. Just enter your school information, test scores, and what types of colleges or programs you prefer, and our algorithm will find the best CSU and UC schools for you. Use <<ADV_EXPLORE>>  now!
				<br><br>
				In PLAN, you will find nine sections that help you compile all your information for applying to college. Planning is the best way to be prepared and apply successfully, so we wanted to help you cover your bases. Start your <<PLAN>>  now!
				<br/><br>
				In PLAN, you’ll see our Personal Statement section. This will help you craft those tough Personal Statements for UC applications. We also offer the UCEazy Essay Review, a paid service that provides in-depth feedback on personal statements. To read more about UCEazy Essay Review, click here <<REVIEW>>. 
		</td>
		<tr>
				<td>
				<br>
				That about does it. Good luck on your college applications, and thanks for using our service!
				</td>
				</tr>
				
		<tr>
				<td>
				<br>
				Sincerely,
				</td>
				</tr>		
				
		<tr>
				<td>
				
-UCEazy Team
				</td>
				</tr>			
			</table></body></html>	
				";
		
		return $msg;
		
		
	}
	function generateSendResultTemplate($explore){
		$msg="<html>
		<head>
		<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
		<title>UCEazy - Your Result</title>
		<meta name='viewport' content='width=device-width, initial-scale=1.0'/>
		</head>
		<body style='margin: 0; padding: 0; '>
		<table align='center' border='0' cellpadding='0' cellspacing='0' width='800' style='border-collapse: collapse; '>
		<tr>
		<td align='center' style='padding:20px;'>
		<img src='".Constants::SITE_BASE_URL."/img/logo-uceazy.png' class='img-responsive' width='169' height='90'>
		</td>
		</tr>
		<tr>
		<td style='background:#c4211a; '>
		<h3 style='font-family: Arial, Helvitica, sans-serif; color:#fff;' align='center'>Simple Explore - Result</h3>
		</td>
		</tr>
		<tr>
		<td>
		<table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='padding:20px; background:#efefef; font-family: Arial, Helvitica, sans-serif; font-size:14px;'>
		<tr>
		<td style='padding:5px; border-bottom:1px solid #fff;'> Current State:</td>
		<td style='padding:5px; border-bottom:1px solid #fff;'>".Convertors::getStateDescription($explore->getState())."</td>
		</tr>
		<tr>
		<td style='padding:5px; border-bottom:1px solid #fff;'> California County:</td>
		<td style='padding:5px; border-bottom:1px solid #fff;'>".$explore->getCounty()."</td>
		</tr>
				<tr>
		<td style='padding:5px; border-bottom:1px solid #fff;'> Weighted GPA:</td>
		<td style='padding:5px; border-bottom:1px solid #fff;'>".$explore->getGpa()."</td>
		</tr>
		<tr>
		<td style='padding:5px; border-bottom:1px solid #fff;'> Standardized Test:</td>
		<td style='padding:5px; border-bottom:1px solid #fff;'>".strtoupper($explore->getExam())."</td>
		</tr>
		";
			if($explore->getExam()=='act'){
				
			$msg .="<tr>
		<td style='padding:5px; border-bottom:1px solid #fff;'> ACT Composite:</td>
		<td style='padding:5px; border-bottom:1px solid #fff;'>".$explore->getAct()."</td>
		</tr>";	
			$msg .="<tr>
		<td style='padding:5px; border-bottom:1px solid #fff;'> ACT Combined English/Writing:</td>
		<td style='padding:5px; border-bottom:1px solid #fff;'>".$explore->getActew()."</td>
		</tr>";
				
		}elseif($explore->getExam()=='sat'){
				
			$msg .="<tr>
		<td style='padding:5px; border-bottom:1px solid #fff;'> Critical Reading :</td>
		<td style='padding:5px; border-bottom:1px solid #fff;'>".$explore->getsatread()."</td>
		</tr>";
			$msg .="<tr>
		<td style='padding:5px; border-bottom:1px solid #fff;'> Math:</td>
		<td style='padding:5px; border-bottom:1px solid #fff;'>".$explore->getsatmath()."</td>
		</tr>";
			$msg .="<tr>
		<td style='padding:5px; border-bottom:1px solid #fff;'> Writing:</td>
		<td style='padding:5px; border-bottom:1px solid #fff;'>".$explore->getsatwrite()."</td>
		</tr>";
		}elseif ($explore->getExam()=='psat'){
			
			$msg .="<tr>
		<td style='padding:5px; border-bottom:1px solid #fff;'> Critical Reading:</td>
		<td style='padding:5px; border-bottom:1px solid #fff;'>".$explore->getpsatread()."</td>
		</tr>";
			$msg .="<tr>
		<td style='padding:5px; border-bottom:1px solid #fff;'> Math:</td>
		<td style='padding:5px; border-bottom:1px solid #fff;'>".$explore->getpsatmath()."</td>
		</tr>";
			$msg .="<tr>
		<td style='padding:5px; border-bottom:1px solid #fff;'> Writing:</td>
		<td style='padding:5px; border-bottom:1px solid #fff;'>".$explore->getpsatwrite()."</td>
		</tr>";
			
		}	
					
		$msg .="</table>
		</td>
		</tr>
		<tr>
		<td>
		<table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='padding:0px 20px 20px 20px; background:#efefef; font-family: Arial, Helvitica, sans-serif; font-size:14px;'>
		<tr>
		<td colspan='5' style='color:#c4211a; padding-bottom:10px;'><h3>My List </h3></td>
		</tr>
		<tr bgcolor='#fff'>
		<td align='center' valign='top'>
		<table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='font-family: Arial, Helvitica, sans-serif;'>
		<tr>
		<td style='padding:5px; background:#9adcfd; border-bottom:1px solid #efefef;'><strong>Reach</strong></td>
		</tr>";
		
		$safety = $explore->getSafety();
		$reach = $explore->getReach();
		$target=$explore->getTarget();
		
		if(count($reach)>0){
			foreach ($reach as $val){
		$msg .="<tr>
		<td style='padding:5px; background:#fff; border-bottom:1px solid #efefef;'>$val</td>
		</tr>";
			}}else{
				$msg .="<tr>
				<td style='padding:5px; background:#fff; border-bottom:1px solid #efefef;'>No Records Found</td>
				</tr>";
			}
			
		$msg .="</table>
		</td>
		<td style='background:#efefef;'>&nbsp;</td>
		<td align='center' valign='top'>
		<table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='font-family: Arial, Helvitica, sans-serif;'>
		<tr>
		<td style='padding:5px; background:#FFA500;'><strong>Target</strong></td>
		</tr>";
		

		if(count($target)>0){
				
			foreach ($target as $val){
				$msg .= "<tr>
		<td style='padding:5px; background:#fff; border-bottom:1px solid #efefef;'>$val</td>
		</tr>";
			}
		
		}else{
			$msg .= "<tr>
		<td style='padding:5px; background:#fff; border-bottom:1px solid #efefef;'>No Records Found</td>
		</tr>";
		}
		
		
		
		$msg .= "</table>
		</td>
		<td style='background:#efefef;'>&nbsp;</td>
		<td align='center' valign='top'>
		<table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='font-family: Arial, Helvitica, sans-serif;'>
		<tr>
		<td style='padding:5px; background:#00ff00;'><strong>Safety</strong></td>
		</tr>";
		
		if(count($safety)>0){
				
			foreach ($safety as $val){
				$msg .= "<tr>
		<td style='padding:5px; background:#fff; border-bottom:1px solid #efefef;'>$val</td>
		</tr>";
			}
		
		}else{
			$msg .= "<tr>
		<td style='padding:5px; background:#fff; border-bottom:1px solid #efefef;'>No Records Found</td>
		</tr>";
		}
		
		
			$msg .= "</table>
		</td>
		</tr>
		</table>
		</td>
		</tr>
		<tr>
		<td style='padding:10px; text-align:center;'><a href='#'>www.uceazy.com</a>	</td>
		</tr>
		</table>
		</body>
		</html>";
		return $msg;
	}

	
	function generateSendAdvanceResultTemplate($explore){
		
$msg="<html >
 <head>
  <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
  <title>UCEazy - Advance Explore - Your Result</title>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
</head>
<body style='margin: 0; padding: 0; background:#fafafa;'>
 <table align='center' border='0' cellpadding='0' cellspacing='0' width='800' style='border-collapse: collapse; '>
	 <tr>
	  <td align='center' style='padding:20px;'>
	   	<img src='".Constants::SITE_BASE_URL."/img/logo-uceazy.png' class='img-responsive' width='169' height='90'>
	  </td>
	 </tr>
	 <tr>
	  <td style='background:#c4211a; '>
	   	<h3 style='font-family: Arial, Helvitica, sans-serif; color:#fff;' align='center'>Advanced Explore - Result</h3>
	  </td>
	 </tr>
	 <tr>
	  	<td>
		 	<table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='padding:20px; background:#fff; font-family: Arial, Helvitica, sans-serif; font-size:14px;'>
		 		<tr>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'> Current State:</td>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'>".Convertors::getStateDescription($explore->getState())."</td>
		 		</tr>
		 		<tr>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'> California County:</td>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'>".$explore->getCounty()."</td>
		 		</tr>
		 					<tr>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'> Weighted GPA:</td>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'>".$explore->getGpa()."</td></tr>
		 		<tr>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'> Standardized Test:</td>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'>".strtoupper($explore->getExam())."</td>
		 		</tr>
		 		";
		
		if($explore->getExam()=='act'){
			$msg .= "<tr>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>ACT Composite</td>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>".$explore->getAct()."</td>
			  </tr>";
			$msg .= "<tr>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>ACT English Writing</td>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>".$explore->getActew()."</td>
			  </tr>";
		
		}elseif($explore->getExam()=='sat'){
		
			$msg .= "<tr>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>Critical Reading </td>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>".$explore->getsatread()."</td>
			  </tr>";
		
			$msg .= "<tr>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>Math</td>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>".$explore->getsatmath()."</td>
			  </tr>";
		
			$msg .= "<tr>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>Writing</td>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>".$explore->getsatwrite()."</td>
			  </tr>";
		
		}elseif ($explore->getExam()=='psat'){
			$msg .= "<tr>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>Critical Reading </td>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>".$explore->getpsatread()."</td>
			  </tr>";
		
			$msg .= "<tr>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>Math</td>
			 <td style='padding:5px; border-bottom:1px solid #eee;'>".$explore->getpsatmath()."</td>
			  </tr>";
		
			$msg .= "<tr>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>Writing</td>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>".$explore->getpsatwrite()."</td>
			  </tr>";
		}
		
		 	
		 		$msg .= "<tr>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'> Applying to:</td>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'>".Convertors::getUS($explore->getus())."</td>
		 		</tr>";
		 		if($explore->getMajor()=="-1"){
		 			$msg .= "<tr>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>Major</td>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>Undeclared</td>
			  </tr>";
		 		
		 		}else{
		 			$msg .= "<tr>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>Major</td>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>".$explore->getMajor()."</td>
			  </tr>";
		 		}
		 		
		 		$msg .= "
		 		<tr>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'> Campus Size:</td>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'>".Convertors::getCSize($explore->getcsize())."</td>
		 		</tr>
		 		<tr>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'> Campus Preference:</td>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'>".Convertors::getCPref($explore->getcpref())."</td>
		 		</tr>
		 	</table>
	  	</td>
	 </tr>
	 <tr>
	 	<td>
	 		<table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='padding:0px 20px 20px 20px; background:#fff; font-family: Arial, Helvitica, sans-serif; font-size:14px;'>
		 		<tr>
		 			<td width='10%' style='color:#c4211a; padding-bottom:10px;'><h3>My List :</h3></td>
		 			<td width='26%'  style='color:#000; padding-bottom:10px;'><strong> </strong></td>
		 			<td width='10%' align='left' style='color:#c4211a; padding-bottom:10px; font-size:20px;'>  </td>
		 			<td width='11%' style='color:000; padding-bottom:10px;'><strong></strong> </td>
		 			<td width='43%' style='color:#c4211a; padding-bottom:10px; font-size:20px;'>  </td>
		 		</tr>
		 		<tr bgcolor='#fafafa'>
		 			<td colspan='5' align='center' valign='top' style='border:1px solid #eee;'>
		 				<table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='font-family: Arial, Helvitica, sans-serif;'>
		 					<tr>
		 						<td align='left' colspan='6' style='padding:5px; background:#9adcfd; border-bottom:1px solid #efefef;'><strong>Reach</strong></td>
		 					</tr>
		 					<tr bgcolor='#ddd'>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>College</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>Cost - Staying with Relatives</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>Cost - On Campus</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>Size</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>University System</strong></td>
		 						<td align='ce' valign='middle' style='padding:5px; color:#222;'><strong>Environment</strong></td>
		 					</tr>";
		 					
		 $safety = $explore->getSafety();
		$reach = $explore->getReach();
		$target=$explore->getTarget();
		if(count($reach)>0){
				
			foreach ($reach as $val){
				$msg .= "<tr bgcolor='#fff'>
							<td align='center' valign='middle' style='padding:5px; '> ".$val['name']."</td>
							<td align='center' valign='middle' style='padding:5px; '>$ ".number_format(intval($val['relativeCOA']), 0, '.', ',')."</td>
							<td align='center' valign='middle' style='padding:5px; '>$ ".number_format(intval($val['onCampusCOA']), 0, '.', ',')."</td>
							<td align='center' valign='middle' style='padding:5px; '>".number_format(intval($val['size']), 0, '.', ',')."</td>
							<td align='center' valign='middle' style='padding:5px; '>".$val['universitySystem']."</td>
							<td align='center' valign='middle' style='padding:5px; '>".$val['environment']."</td>
					</tr>";
			}
		
		}else{
			$msg .= "<tr bgcolor='#fff'><td colspan=6 >No College found</td></tr>";
		}
		 					
		 					
		 				$msg .= "</table>
		 			</td>
		 		</tr>	 
		 		<tr><td height='20'></td></tr>
		 		<tr bgcolor='#fafafa'>
		 			<td colspan='5' align='center' valign='top' style='border:1px solid #eee;'>
		 				<table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='font-family: Arial, Helvitica, sans-serif;'>
		 					<tr>
		 						<td align='left' colspan='6' style='padding:5px; background:#FFA500; border-bottom:1px solid #FFA500;'><strong>Target</strong></td>
		 					</tr>
		 					<tr bgcolor='#ddd'>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>College</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>Cost - Staying with Relatives</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>Cost - On Campus</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>Size</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>University System</strong></td>
		 						<td align='ce' valign='middle' style='padding:5px; color:#222;'><strong>Environment</strong></td>
		 					</tr>";
		 						
		 						if(count($target)>0){
			
			foreach ($target as $val){
				$msg .= "<tr bgcolor='#fff'>
							<td align='left' valign='middle' style='padding:5px ; '>".$val['name']."</td>
							<td align='left' valign='middle' style='padding:5px ; '>$ ".number_format(intval($val['relativeCOA']), 0, '.', ',')."</td>
							<td align='left' valign='middle' style='padding:5px ; '>$ ".number_format(intval($val['onCampusCOA']), 0, '.', ',')."</td>
							<td align='left' valign='middle' style='padding:5px ; '>".number_format(intval($val['size']), 0, '.', ',')."</td>
							<td align='left' valign='middle' style='padding:5px ; '>".$val['universitySystem']."</td>
							<td align='left' valign='middle' style='padding:5px ; '>".$val['environment']."</td>
					</tr>";
			}
		
		}else{
			$msg .= "<tr bgcolor='#fff'><td align='left' colspan=6 valign='middle' style='padding:5px ; '>No College found</td></tr>";
		}
				$msg .= "</table>
		 			</td>
		 		</tr>
		 		<tr><td height='20'></td></tr>
		 		<tr bgcolor='#fafafa'>
		 			<td colspan='5' align='center' valign='top' style='border:1px solid #eee;'>
		 				<table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='font-family: Arial, Helvitica, sans-serif;'>
		 					<tr>
		 						<td align='left' colspan='6' style='padding:5px; background:#00ff00; border-bottom:1px solid #00ff00;'><strong>Safety</strong></td>
		 					</tr>
		 					<tr bgcolor='#ddd'>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>College</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>Cost - Staying with Relatives</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>Cost - On Campus</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>Size</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>University System</strong></td>
		 						<td align='ce' valign='middle' style='padding:5px; color:#222;'><strong>Environment</strong></td>
		 					</tr>";
						
						if(count($safety)>0){
			
			foreach ($safety as $val){
				$msg .= "<tr bgcolor='#fff'>
							<td align='left' valign='middle' style='padding:5px ; '>".$val['name']."</td>
							<td align='left' valign='middle' style='padding:5px ; '>$ ".number_format(intval($val['relativeCOA']), 0, '.', ',')."</td>
							<td align='left' valign='middle' style='padding:5px ; '>$ ".number_format(intval($val['onCampusCOA']), 0, '.', ',')."</td>
							<td align='left' valign='middle' style='padding:5px ; '>".number_format(intval($val['size']), 0, '.', ',')."</td>
							<td align='left' valign='middle' style='padding:5px ; '>".$val['universitySystem']."</td>
							<td align='left' valign='middle' style='padding:5px ; '>".$val['environment']."</td>
					</tr>";
			}
		
		}else{
			$msg .= "<tr bgcolor='#fff'><td align='left' valign='middle' colspan=6 style='padding:5px ; '>No College found</td></tr>";
		}
		 					
		 				$msg .= "</table>
		 			</td>
		 		</tr>
		 	</table>
	 	</td>
	 </tr>
	<tr>
		<td style='padding:10px; text-align:center;'><a href='#'>www.uceazy.com</a>	</td>
	</tr>
</table>
</body>
</html>";
		return $msg;
	}
	
	
	
	function generateSendAdvanceResultTemplateALL($explore,$list){
	
		$msg="<html>
 <head>
  <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
  <title>UCEazy - Advance Explore - Your Result</title>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'/>
</head>
<body style='margin: 0; padding: 0; background:#fafafa;'>
 <table align='center' border='0' cellpadding='0' cellspacing='0' width='800' style='border-collapse: collapse; '>
	 <tr>
	  <td align='center' style='padding:20px;'>
	   	<img src='".Constants::SITE_BASE_URL."/img/logo-uceazy.png' class='img-responsive' width='169' height='90'>
	  </td>
	 </tr>
	 <tr>
	  <td style='background:#c4211a; '>
	   	<h3 style='font-family: Arial, Helvitica, sans-serif; color:#fff;' align='center'>Advanced Explore - Result</h3>
	  </td>
	 </tr>
	 <tr>
	  	<td>
		 	<table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='padding:20px; background:#fff; font-family: Arial, Helvitica, sans-serif; font-size:14px;'>
		 		<tr>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'> Current State:</td>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'>".Convertors::getStateDescription($explore->getState())."</td>
		 		</tr>
		 		<tr>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'> California County:</td>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'>".$explore->getCounty()."</td>
		 		</tr>
		 					<tr>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'> Weighted GPA:</td>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'>".$explore->getGpa()."</td></tr>
		 		<tr>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'> Standardized Test:</td>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'>".strtoupper($explore->getExam())."</td>
		 		</tr>
		 		";
	
		if($explore->getExam()=='act'){
			$msg .= "<tr>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>ACT Composite</td>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>".$explore->getAct()."</td>
			  </tr>";
			$msg .= "<tr>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>ACT English Writing</td>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>".$explore->getActew()."</td>
			  </tr>";
	
		}elseif($explore->getExam()=='sat'){
	
			$msg .= "<tr>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>Critical Reading </td>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>".$explore->getsatread()."</td>
			  </tr>";
	
			$msg .= "<tr>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>Math</td>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>".$explore->getsatmath()."</td>
			  </tr>";
	
			$msg .= "<tr>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>Writing</td>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>".$explore->getsatwrite()."</td>
			  </tr>";
	
		}elseif ($explore->getExam()=='psat'){
			$msg .= "<tr>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>Critical Reading </td>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>".$explore->getpsatread()."</td>
			  </tr>";
	
			$msg .= "<tr>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>Math</td>
			 <td style='padding:5px; border-bottom:1px solid #eee;'>".$explore->getpsatmath()."</td>
			  </tr>";
	
			$msg .= "<tr>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>Writing</td>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>".$explore->getpsatwrite()."</td>
			  </tr>";
		}
	
	
		$msg .= "<tr>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'> Applying to:</td>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'>".Convertors::getUS($explore->getus())."</td>
		 		</tr>";
		if($explore->getMajor()=="-1"){
			$msg .= "<tr>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>Major</td>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>Undeclared</td>
			  </tr>";
			 
		}else{
			$msg .= "<tr>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>Major</td>
			  <td style='padding:5px; border-bottom:1px solid #eee;'>".$explore->getMajor()."</td>
			  </tr>";
		}
		 
		$msg .= "
		 		<tr>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'> Campus Size:</td>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'>".Convertors::getCSize($explore->getcsize())."</td>
		 		</tr>
		 		<tr>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'> Campus Preference:</td>
		 			<td style='padding:5px; border-bottom:1px solid #eee;'>".Convertors::getCPref($explore->getcpref())."</td>
		 		</tr>
		 	</table>
	  	</td>
	 </tr>
	 <tr>
	 	<td>
	 		<table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='padding:0px 20px 20px 20px; background:#fff; font-family: Arial, Helvitica, sans-serif; font-size:14px;'>
		 		<tr>
		 			<td width='10%' style='color:#c4211a; padding-bottom:10px;'><h3>My List :</h3></td>
		 			<td width='26%'  style='color:#000; padding-bottom:10px;'><strong> </strong></td>
		 			<td width='10%' align='left' style='color:#c4211a; padding-bottom:10px; font-size:20px;'>  </td>
		 			<td width='11%' style='color:000; padding-bottom:10px;'><strong></strong> </td>
		 			<td width='43%' style='color:#c4211a; padding-bottom:10px; font-size:20px;'>  </td>
		 		</tr>";
		

		$safety = $explore->getSafety();
		$reach = $explore->getReach();
		$target=$explore->getTarget();
		if(in_array("reach", $list)){
		 					
		 					
		 		$msg .="<tr bgcolor='#fafafa'>
		 			<td colspan='5' align='center' valign='top' style='border:1px solid #eee;'>
		 				<table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='font-family: Arial, Helvitica, sans-serif;'>
		 					<tr>
		 						<td align='left' colspan='6' style='padding:5px; background:#9adcfd; border-bottom:1px solid #efefef;'><strong>Reach</strong></td>
		 					</tr>
		 					<tr bgcolor='#ddd'>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>College</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>Cost - Living with Parent/Guardian ($)</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>Cost - Living on Campus ($)</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>Size</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>University System</strong></td>
		 						<td align='ce' valign='middle' style='padding:5px; color:#222;'><strong>Environment</strong></td>
		 					</tr>";
	
		if(count($reach)>0){
	
			foreach ($reach as $val){
				$msg .= "<tr bgcolor='#fff'>
							<td align='center' valign='middle' style='padding:5px; '> ".$val['name']."</td>
							<td align='center' valign='middle' style='padding:5px; '>$ ".number_format(intval($val['relativeCOA']), 0, '.', ',')."</td>
							<td align='center' valign='middle' style='padding:5px; '>$ ".number_format(intval($val['onCampusCOA']), 0, '.', ',')."</td>
							<td align='center' valign='middle' style='padding:5px; '>".number_format(intval($val['size']), 0, '.', ',')."</td>
							<td align='center' valign='middle' style='padding:5px; '>".$val['universitySystem']."</td>
							<td align='center' valign='middle' style='padding:5px; '>".$val['environment']."</td>
					</tr>";
			}
	
		}else{
			$msg .= "<tr bgcolor='#fff'><td colspan=6 >No College found</td></tr>";
		}
	
	
		$msg .= "</table>
		 			</td>
		 		</tr>
		 		<tr><td height='20'></td></tr>";
		
		}
		if(in_array("target", $list)){	
		 		$msg .="<tr bgcolor='#fafafa'>
		 			<td colspan='5' align='center' valign='top' style='border:1px solid #eee;'>
		 				<table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='font-family: Arial, Helvitica, sans-serif;'>
		 					<tr>
		 						<td align='left' colspan='6' style='padding:5px; background:#FFA500; border-bottom:1px solid #FFA500;'><strong>Target</strong></td>
		 					</tr>
		 					<tr bgcolor='#ddd'>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>College</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>Cost - Living with Parent/Guardian ($)</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>Cost - Living on Campus ($)</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>Size</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>University System</strong></td>
		 						<td align='ce' valign='middle' style='padding:5px; color:#222;'><strong>Environment</strong></td>
		 					</tr>";
			
		if(count($target)>0){
				
			foreach ($target as $val){
				$msg .= "<tr bgcolor='#fff'>
							<td align='left' valign='middle' style='padding:5px ; '>".$val['name']."</td>
							<td align='left' valign='middle' style='padding:5px ; '>$ ".number_format(intval($val['relativeCOA']), 0, '.', ',')."</td>
							<td align='left' valign='middle' style='padding:5px ; '>$ ".number_format(intval($val['onCampusCOA']), 0, '.', ',')."</td>
							<td align='left' valign='middle' style='padding:5px ; '>".number_format(intval($val['size']), 0, '.', ',')."</td>
							<td align='left' valign='middle' style='padding:5px ; '>".$val['universitySystem']."</td>
							<td align='left' valign='middle' style='padding:5px ; '>".$val['environment']."</td>
					</tr>";
			}
	
		}else{
			$msg .= "<tr bgcolor='#fff'><td align='left' colspan=6 valign='middle' style='padding:5px ; '>No College found</td></tr>";
		}
		$msg .= "</table>
		 			</td>
		 		</tr>
		 		<tr><td height='20'></td></tr>";
		
		}
		
		if(in_array("safety", $list)){
		 		$msg .="<tr bgcolor='#fafafa'>
		 			<td colspan='5' align='center' valign='top' style='border:1px solid #eee;'>
		 				<table align='center' border='0' cellpadding='0' cellspacing='0' width='100%' style='font-family: Arial, Helvitica, sans-serif;'>
		 					<tr>
		 						<td align='left' colspan='6' style='padding:5px; background:#00ff00; border-bottom:1px solid #00ff00;'><strong>Safety</strong></td>
		 					</tr>
		 					<tr bgcolor='#ddd'>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>College</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>Cost - Living with Parent/Guardian ($)</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>Cost - Living on Campus ($)</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>Size</strong></td>
		 						<td align='left' valign='middle' style='padding:5px; color:#222;'><strong>University System</strong></td>
		 						<td align='ce' valign='middle' style='padding:5px; color:#222;'><strong>Environment</strong></td>
		 					</tr>";
	
		if(count($safety)>0){
				
			foreach ($safety as $val){
				$msg .= "<tr bgcolor='#fff'>
							<td align='left' valign='middle' style='padding:5px ; '>".$val['name']."</td>
							<td align='left' valign='middle' style='padding:5px ; '>$ ".number_format(intval($val['relativeCOA']), 0, '.', ',')."</td>
							<td align='left' valign='middle' style='padding:5px ; '>$ ".number_format(intval($val['onCampusCOA']), 0, '.', ',')."</td>
							<td align='left' valign='middle' style='padding:5px ; '>".number_format(intval($val['size']), 0, '.', ',')."</td>
							<td align='left' valign='middle' style='padding:5px ; '>".$val['universitySystem']."</td>
							<td align='left' valign='middle' style='padding:5px ; '>".$val['environment']."</td>
					</tr>";
			}
	
		}else{
			$msg .= "<tr bgcolor='#fff'><td align='left' valign='middle' colspan=6 style='padding:5px ; '>No College found</td></tr>";
		}
	
		$msg .= "</table>
		 			</td>
		 		</tr>
		 	</table>
	 	</td>
	 </tr>";
		}
	$msg .="<tr>
		<td style='padding:10px; text-align:center;'><a href='#'>www.uceazy.com</a>	</td>
	</tr>
</table>
</body>
</html>";
		return $msg;
	}
	
	function sendResultsALL($actionForm,$email,$list){
		
		try{
		$email=trim($email);
		$smtpOptions = new Transport\SmtpOptions($this->options);
		$smtp = new Transport\Smtp($smtpOptions);
		
			$htmlBody=$this->generateSendAdvanceResultTemplateALL($actionForm,$list);
		
		$htmlPart = new MimePart($htmlBody);
		$htmlPart->type = "text/html";
		$body = new MimeMessage();
		$body->setParts(array($htmlPart));
		$mail = new Mail\Message();
		$mail->setBody($body);
		$mail->setFrom('noreply@uceazy.com', 'noreply@uceazy.com');
		$mail->addTo($email, $email);
		$mail->setSubject('Your Result for All colleges');
		$smtp->send($mail);
		return "1";
		}catch(\Exception $e){
			return 0;
		}
	}
	function sendResults($actionForm,$email){
	
		try{
			$email=trim($email);
			$smtpOptions = new Transport\SmtpOptions($this->options);
			$smtp = new Transport\Smtp($smtpOptions);
			if($actionForm->getsearchType()=="A"){
				$htmlBody=$this->generateSendAdvanceResultTemplate($actionForm);
			}else{
				$htmlBody=$this->generateSendResultTemplate($actionForm);
			}
			$htmlPart = new MimePart($htmlBody);
			$htmlPart->type = "text/html";
			$body = new MimeMessage();
			$body->setParts(array($htmlPart));
			$mail = new Mail\Message();
			$mail->setBody($body);
			$mail->setFrom('noreply@uceazy.com', 'noreply@uceazy.com');
			$mail->addTo($email, $email);
			$mail->setSubject('UCEazy - Your Search Result');
			$smtp->send($mail);
			return "1";
		}catch(\Exception $e){
			echo $e->getMessage();
			return 0;
		}
	}
	
	
	
	
	function sendForgetPasswordMail($to,$key){
	
		$smtpOptions = new Transport\SmtpOptions($this->options);
		$smtp = new Transport\Smtp($smtpOptions);
	
		$htmlBody="Hi there, <br><br>
We got a request to reset your UCEazy password. Please click here <a href='".Constants::SITE_BASE_URL."/user/resetpassword?key=".$key."&email=".$to."'>Activate</a>. <br><br>-- Your friends at UCEazy";
		$htmlPart = new MimePart($htmlBody);
		$htmlPart->type = "text/html";
	
		$body = new MimeMessage();
		$body->setParts(array($htmlPart));
	
	
		$mail = new Mail\Message();
		$mail->setBody($body);
		$mail->setFrom('noreply@uceazy.com', 'noreply@uceazy.com');
		$mail->addTo($to, $to);
		$mail->setSubject('Reset your UCEazy account password');
		$smtp->send($mail);
	
	}
	
	
function send_note_to_essayTeam($uid,$row_id,$user_text1,$user_text2,$to){
		$user_text1=str_replace("\n", "<br/>", $user_text1);
		$user_text2=str_replace("\n", "<br/>", $user_text2);
		$msg="<html>
		<head>
		<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
		<title>UCEazy - Essay Alert</title>
		<meta name='viewport' content='width=device-width, initial-scale=1.0'/>
		<style>
		td{
			width: 34%;
		}
		</style>
		</head>
		<body style='margin: 0; padding: 0; '>
		<table align='center' border='0' cellpadding='0' cellspacing='0' width='600' style='border-collapse: collapse; '>
		<tr>
		<td style='width: 34%;'>
		Hi there,<br><br>
		 The Candidate details as below, 
		</td>
		
		</tr>
		
		<tr>

			<td style='width: 34%;'>
			<br><br>
			<b>user ID</b>
			</td>
			<td>
			<br><br>
			".$uid."
			</td>
			</tr>
			
			<tr>
			
			<td style='width: 34%;'>
			<br><br>
			<b>Essay ID</b>
			</td>
			<td>
			<br><br>
			".$row_id."
			</td>
			</tr>
			
			<tr>
			
			<td style='width: 34%;'>
			<br><br>
			<b>Submitted On</b>
			</td>
			<td>
			<br><br>
			".date('d-m-Y')."
			</td>
			</tr>
			
			<tr>
			
			<td style='width: 34%;'>
			<br><br>
			<b>Essay-1 </b>
			</td>
			<td>
			<br><br>
			".$user_text1."			
			</td>
			</tr>
			
			<tr>
			
			<td style='width: 34%;'>
			<br><br>
			<b>Essay-2</b>
			</td>
			<td>
			<br><br>
			".$user_text2."
			</td>
			</tr>
			
			<tr>
			
			
				
		<tr>
				<td style='width: 34%;'><br>
				Sincerely,
				
				</tr>		
				
		<tr>
				<td>		
				UCEazy
				</td>
				</tr>			
			</table></body></html>	
				";
		
		
		$smtpOptions = new Transport\SmtpOptions($this->options);
		$smtp = new Transport\Smtp($smtpOptions);
	
		
		$htmlPart = new MimePart($msg);
		$htmlPart->type = "text/html";
	
		$body = new MimeMessage();
		$body->setParts(array($htmlPart));
	
	
		$mail = new Mail\Message();
		$mail->setBody($body);
		$mail->setFrom('noreply@uceazy.com', 'noreply@uceazy.com');
		$mail->addTo($to, $to);
		$mail->addCC("jsyadav@gmail.com", "jsyadav@gmail.com");
		$mail->setSubject($row_id);
		$smtp->send($mail);
		
	}
	
	
	function send_note_to_user($flag,$username,$for){		
		$content='';$sub='';$to='';
		
		if($flag=="notify_other" && $for=="user"){
			$name_arr=explode("@", $username);		
			$name=$name_arr[0];			
			$content='Hi '. $name.',<br/><br/>Congratulations on purchasing your essay review! This email is confirmation that we have received payment.
			<br><br><b>Next Steps:</b>
			<br><br>&nbsp; -  The next step is submitting your essays. Once you have both Personal Statements ready, log into your account, head to the Service section (top right), and copy your text into the two boxes we have provided. Double-check everything is exactly how you want it -- then hit the Submit button!
			<br><br>&nbsp; -  Now it’s our turn. We will provide in-depth comments on your Personal Statements within 3 business days of your submission. You will receive an email when we give you the comments, and you’ll be able to read them in your profile.
			<br><br>&nbsp; -  After we provide comments on your Personal Statements, you’ll see a box where you can give us feedback! We want to hear from you. 
			<br><br>That about does it. Thank you for choosing UCEazy Essay Review! If you have any questions, feel free to contact us at essay@uceazy.com. 
			<br><br>
			Thanks,<br/>-UCEazy Team<b/>';
			$to=$username;
			$sub="UCEazy - Essay review serice notification";
			
		}
		if($flag=='purchased' && $for=='user'){
			$name_arr=explode("@", $username);		
			$name=$name_arr[0];			
			$content='Hi '. $name.',<br/><br/>Congratulations on purchasing your essay review! This email is confirmation that we have received payment.
			<br><br><b>Next Steps:</b>
			<br><br>&nbsp; -  The next step is submitting your essays. Once you have both Personal Statements ready, log into your account, head to the Service section (top right), and copy your text into the two boxes we have provided. Double-check everything is exactly how you want it -- then hit the Submit button!
			<br><br>&nbsp; -  Now it’s our turn. We will provide in-depth comments on your Personal Statements within 3 business days of your submission. You will receive an email when we give you the comments, and you’ll be able to read them in your profile.
			<br><br>&nbsp; -  After we provide comments on your Personal Statements, you’ll see a box where you can give us feedback! We want to hear from you. 
			<br><br>That about does it. Thank you for choosing UCEazy Essay Review! If you have any questions, feel free to contact us at essay@uceazy.com. 
			<br><br>
			Thanks,<br/>-UCEazy Team<b/>';
			$to=$username;
			$sub="UCEazy - Essay review service purchase confirmation ";
				
		}
		
		if($flag=='submitted' && $for=='user'){
			$name_arr=explode("@", $username);
			
			$now1 = date("m-d-Y H:i:s");
			$now = date("Y-m-d");
			$last= date('Y-m-d', strtotime($now . ' +3 Weekday'));
			$last=date("m-d-Y", strtotime($last));
			$acc_link="<a href='".Constants::SITE_BASE_URL."/user/signin'>account</a>";
			$name=$name_arr[0];
			$content='Hi '. $name.',<br/><br/>Congratulations! You submitted your Personal Statements! We can’t wait to read them.<br><br>
			Your essay was submitted on'.$now1.' . We have 3 business days to get it back to you, which means you will be hearing from us on or before '.$last.'.
			<br><br>For now, you can relax. Just wait to receive an email that says your Personal Statements have been reviewed, and know they are in excellent hands.
			<br><br>After we have reviewed your Personal Statements, you will get another email from us. You can see our comments in your UCEazy '.$acc_link.'
			<br><br>You’ll also see a box where you can give us feedback! We want to hear what you thought about our process the comments on your Personal Statements.
			<br><br>That about does it. Thank you for choosing UCEazy Essay Review, and for trusting us with your Personal Statements. We know how important they are. If you have any questions, feel free to contact us at essay@uceazy.com.
			<br><br>
			Thanks,<br/>-UCEazy Team<b/>';
			$to=$username;
			$sub="UCEazy - Essay has been submitted to reviewer";
			
		}
		if($flag=='submitted' && $for=='group'){
			$content='Hi UCEazy Essay Team!<br/><br/>Just wanted to let you know that a new essay is available for review. The deadline for this essay is THREE days.<br><br>
			Thanks,<br/>UCEazy<b/>';
			$to=$username;
			$sub="UCEazy - Essay has been submitted for review";
		}
		if($flag=='submitted' && $for=='feedBack'){
			$name_arr=explode("@", $username);
			$name=$name_arr[0];
			$content='Hi '. $name.',<br/><br/>We see you left some feedback for the UCEazy Essay Review team. Thank you! Every bit of information enables us to do our jobs better.
			<br><br>If you enjoyed the first round of UCEazy Essay Review, know that we offer multiple rounds. You may want another look at your current Personal Statements, or you may want to submit new drafts. Either way, we are happy to help. The process will be the same as the last, and we’ll even dig deeper into your Personal Statements.
			<br><br>The additional rounds will be 40% off as well! That means your second (and third and fourth) review will be $59.99 each!
			<br><br><b>Testimonials</b>
			<br><br>If you were happy with our service, please consider letting us use your feedback and/or more comments as testimonial on our website. You’d be contributing the UCEazy family, and encourages others to get the same excellent feedback you got.
			<br><br>If you’d like to leave us a testimonial, please email essay@uceazy.com with the headline “Testimonial for UCEazy Essay Service.” One of our team will coordinate what you’d like to say, and we’ll get it up on our site.
			<br><br>That about does it. Once again, thank you so much for trusting us with your Personal Statements, and for leaving invaluable feedback.
			<br><br>If you have any questions, feel free to contact us at essay@uceazy.com. Good luck on your applications!”
			<br><br>
			Thanks,<br/>-UCEazy Team<b/>';
			$to=$username;
			$sub="UCEazy - Thanks for the feedback";
		}
		
		$smtpOptions = new Transport\SmtpOptions($this->options);
		$smtp = new Transport\Smtp($smtpOptions);	
		$htmlPart = new MimePart($content);
		$htmlPart->type = "text/html";
		$body = new MimeMessage();
		$body->setParts(array($htmlPart));
		$mail = new Mail\Message();
		$mail->setBody($body);
		$mail->setFrom('noreply@uceazy.com', 'noreply@uceazy.com');
		$mail->addTo($to, $to);
		$mail->setSubject($sub);
		$mail->WordWrap = 50;
		$smtp->send($mail);
	}
	
	function send_feedbackToAdmin($essay_id,$feedback_txt,$to){
		
		$content='Hi, <br/><br/> One eaasy got a feedback and the details as below, <br/><br/> Essay Id: '.$essay_id.'<br/><br/>
		User Feedback: '.$feedback_txt.'.<br/><br/>Sincerely,<br/>UCEazy';
		
		$sub=$essay_id." - Feedback";
		
		$smtpOptions = new Transport\SmtpOptions($this->options);
		$smtp = new Transport\Smtp($smtpOptions);	
		$htmlPart = new MimePart($content);
		$htmlPart->type = "text/html";
		$body = new MimeMessage();
		$body->setParts(array($htmlPart));
		$mail = new Mail\Message();
		$mail->setBody($body);
		$mail->setFrom('noreply@uceazy.com', 'noreply@uceazy.com');
		$mail->addTo($to, $to);
		$mail->setSubject($sub);
		$mail->WordWrap = 50;
		$smtp->send($mail);
		
		
	}
	
}
?>