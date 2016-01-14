<?php
namespace Common;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Common\Model\Album;
use Common\Model\AlbumTable;
use Common\DTO\TestDTO;
use Common\DAOGateway\UserDAO;
use Common\DTO\UserDTO;
use Common\DTO\SearchCriteriaDTO;
use Common\DAOGateway\SearchCriteriaDAO;
use Common\DTO\SavedSearchResultsDTO;
use Common\DAOGateway\SavedSearchResultsDAO;
use Common\DAOGateway\ShareresultDAO;
use Common\DTO\ShareresultDTO;
use Common\DAOGateway\SimplefeedbackDAO;
use Common\DTO\SimplefeedbackDTO;
use Common\DAOGateway\AdvancefeedbackDAO;
use Common\DTO\AdvancefeedbackDTO;
use Common\DTO\PlanDTO;
use Common\DAOGateway\PlanDAO;
use Common\DAOGateway\OrganizerDAO;
use Common\DTO\OrganizerDTO;
use Common\DAOGateway\BuyserviceDAO;
use Common\DTO\BuyserviceDTO;
use Common\DAOGateway\EssayDAO;
use Common\DTO\EssayDTO;
use Common\DTO\ServiceMasterDTO;
use Common\DAOGateway\ServiceMasterDAO;
use Common\DTO\CouponMasterDTO;
use Common\DAOGateway\CouponMasterDAO;



class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
	/* public function onBootstrap($e)
	{
		
	} */
 public function getServiceConfig()
     {
         return array(
             'factories' => array(
              
                 'TestService' =>  function($sm) {
                 	$tableGateway = $sm->get('TestTableGateway');
                 	$table = new TestDAO($tableGateway);
                 	return $table;
                 },
                 'TestTableGateway' => function ($sm) {
                 	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                 	$resultSetPrototype = new ResultSet();
                 	$resultSetPrototype->setArrayObjectPrototype(new  TestDTO());
                 	return new TableGateway('test', $dbAdapter, null, $resultSetPrototype);
                 },
                 
                 //planmodule
                 
                 'PlanService' =>  function($sm) {
                 	$tableGateway = $sm->get('PlanTableGateway');
                 	$table = new PlanDAO($tableGateway);
                 	return $table;
                 },
                 'PlanTableGateway' => function ($sm) {
                 	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                 	$resultSetPrototype = new ResultSet();
                 	$resultSetPrototype->setArrayObjectPrototype(new  PlanDTO());
                 	return new TableGateway('planmodule', $dbAdapter, null, $resultSetPrototype);
                 },
                 
                 //organizermodule
                  
                 'OrganizerService' =>  function($sm) {
                 	$tableGateway = $sm->get('OrganizerTableGateway');
                 	$table = new OrganizerDAO($tableGateway);
                 	return $table;
                 },
                 'OrganizerTableGateway' => function ($sm) {
                 	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                 	$resultSetPrototype = new ResultSet();
                 	$resultSetPrototype->setArrayObjectPrototype(new  OrganizerDTO());
                 	return new TableGateway('organizermodule', $dbAdapter, null, $resultSetPrototype);
                 },                 
                 
                 //buy service module
                  
                 'BuyerService' =>  function($sm) {
                 	$tableGateway = $sm->get('buyerTableGateway');
                 	$table = new BuyserviceDAO($tableGateway);
                 	return $table;
                 },
                 'buyerTableGateway' => function ($sm) {
                 	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                 	$resultSetPrototype = new ResultSet();
                 	$resultSetPrototype->setArrayObjectPrototype(new  BuyserviceDTO());
                 	return new TableGateway('tbl_buyservice', $dbAdapter, null, $resultSetPrototype);
                 },
                 
                 //Essay module
                  
                 'EssayService' =>  function($sm) {
                 	$tableGateway = $sm->get('essayTableGateway');
                 	$table = new EssayDAO($tableGateway);
                 	return $table;
                 },
                 'essayTableGateway' => function ($sm) {
                 	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                 	$resultSetPrototype = new ResultSet();
                 	$resultSetPrototype->setArrayObjectPrototype(new  EssayDTO());
                 	return new TableGateway('tbl_essay', $dbAdapter, null, $resultSetPrototype);
                 },
                 //service master
                 
                 'ServiceMasterService' =>  function($sm) {
                 	$tableGateway = $sm->get('ServiceMasterTableGateway');
                 	$table = new ServiceMasterDAO($tableGateway);
                 	return $table;
                 },
                 'ServiceMasterTableGateway' => function ($sm) {
                 	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                 	$resultSetPrototype = new ResultSet();
                 	$resultSetPrototype->setArrayObjectPrototype(new  ServiceMasterDTO());
                 	return new TableGateway('tbl_service_master', $dbAdapter, null, $resultSetPrototype);
                 },
                 
                 //coupon master
                 'CouponService' =>  function($sm) {
                 	$tableGateway = $sm->get('CouponTableGateway');
                 	$table = new CouponMasterDAO($tableGateway);
                 	return $table;
                 },
                 'CouponTableGateway' => function ($sm) {
                 	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                 	$resultSetPrototype = new ResultSet();
                 	$resultSetPrototype->setArrayObjectPrototype(new  CouponMasterDTO());
                 	return new TableGateway('tbl_coupon_master', $dbAdapter, null, $resultSetPrototype);
                 },
                 
                 // user
                 
                 'UserService' =>  function($sm) {
                 	$tableGateway = $sm->get('UserTableGateway');
                 	$table = new UserDAO($tableGateway);
                 	return $table;
                 },
                 'UserTableGateway' => function ($sm) {
                 	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                 	$resultSetPrototype = new ResultSet();
                 	$resultSetPrototype->setArrayObjectPrototype(new  UserDTO());
                 	return new TableGateway('tbl_user', $dbAdapter, null, $resultSetPrototype);
                 },
                 
                 
                 // ShareEmail
                 

                 'ShareresultService' =>  function($sm) {
                 	$tableGateway = $sm->get('ShareresultTableGateway');
                 	$table = new ShareresultDAO($tableGateway);
                 	return $table;
                 },
                 'ShareresultTableGateway' => function ($sm) {
                 	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                 	$resultSetPrototype = new ResultSet();
                 	$resultSetPrototype->setArrayObjectPrototype(new  ShareresultDTO());
                 	return new TableGateway('shareresult', $dbAdapter, null, $resultSetPrototype);
                 },
                  
                 // Advance feedback 
                 
                 // simple feedback services
                 'AdvancefeedbackService' =>  function($sm) {
                 	$tableGateway = $sm->get('AdvancefeedbackTableGateway');
                 	$table = new AdvancefeedbackDAO($tableGateway);
                 	return $table;
                 },
                 'AdvancefeedbackTableGateway' => function ($sm) {
                 	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                 	$resultSetPrototype = new ResultSet();
                 	$resultSetPrototype->setArrayObjectPrototype(new  AdvancefeedbackDTO());
                 	return new TableGateway('advancefeedback', $dbAdapter, null, $resultSetPrototype);
                 },
                  
                 
                 // simple feedback services
                 'SimplefeedbackService' =>  function($sm) {
                 	$tableGateway = $sm->get('SimplefeedbackTableGateway');
                 	$table = new SimplefeedbackDAO($tableGateway);
                 	return $table;
                 },
                 'SimplefeedbackTableGateway' => function ($sm) {
                 	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                 	$resultSetPrototype = new ResultSet();
                 	$resultSetPrototype->setArrayObjectPrototype(new  SimplefeedbackDTO());
                 	return new TableGateway('simplefeedback', $dbAdapter, null, $resultSetPrototype);
                 },
                 
                 // search criteria
                 //SavedSearchResultsDAO

                 'SearchCriteriaService' =>  function($sm) {
                 	$tableGateway = $sm->get('SearchCriteriaTableGateway');
                 	$table = new SearchCriteriaDAO($tableGateway);
                 	return $table;
                 },
                 'SearchCriteriaTableGateway' => function ($sm) {
                 	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                 	$resultSetPrototype = new ResultSet();
                 	$resultSetPrototype->setArrayObjectPrototype(new  SearchCriteriaDTO());
                 	return new TableGateway('searchcriteria', $dbAdapter, null, $resultSetPrototype);
                 },
                 
                 'SavedSearchResultService' =>  function($sm) {
                 	$tableGateway = $sm->get('SavedSearchResultTableGateway');
                 	$table = new SavedSearchResultsDAO($tableGateway);
                 	return $table;
                 },
                 'SavedSearchResultTableGateway' => function ($sm) {
                 	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                 	$resultSetPrototype = new ResultSet();
                 	$resultSetPrototype->setArrayObjectPrototype(new  SavedSearchResultsDTO());
                 	return new TableGateway('savedsearchresults', $dbAdapter, null, $resultSetPrototype);
                 },
                 
             ),
         );
     }   
    
}
