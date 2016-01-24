<?php
namespace Common\Services;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Common\Utilities\ConvertPDOtoArray;

class TestService implements ServiceLocatorAwareInterface
{
	protected $servicelocator;
	public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
	{
		$this->servicelocator = $serviceLocator;
	}
	
	public function getServiceLocator()
	{
		return $this->servicelocator;
	}
	
	public function fetchNewsContent()
	{
		$newsDAOObject=$this->getServiceLocator()->get('TestDAOGateway');
		$res= $newsDAOObject->queryAll();
		//var_dump($res);
		$res1=ConvertPDOtoArray::convertTestPDOtoTestArray($res);
		return $res1;
	}
	
	function LogMessage($message) {
		if (Constants::IS_LOG) {
			$logger = new \Zend\Log\Logger ();
			$writer = new \Zend\Log\Writer\Stream ( Constants::LOG_FILE );
			$logger->addWriter ( $writer );
			$logger->info ( $message );
		}
	}
	
}