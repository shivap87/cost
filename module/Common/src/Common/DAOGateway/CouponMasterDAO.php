<?php
namespace Common\DAOGateway;
use Zend\Db\TableGateway\TableGateway;

class CouponMasterDAO
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}
	
	public function getCoupon($field,$val){
		$resultSet = $this->tableGateway->select(array($field => $val));		
		return $resultSet;
	}
	
	public function get_system_coupon(){
		
		$res=$this->tableGateway->select(function($select){
			$select->where->equalTo('coupon_status', 'valid');
			$select->where->equalTo('coupon_type', 'system');
			$select->limit(1);
		});
		return $res;
	}
	
	public function get_general_coupon($code){
		
		$res=$this->tableGateway->select(function($select) use($code){
			$select->where->equalTo('coupon_status', 'valid');
			$select->where->equalTo('code', $code);
			$select->limit(1);
		});
		return $res;
	}
	
	
	public function getSQLQueryString(){
		return $this->tableGateway->getSql()->getSqlPlatform()->getSqlString($this->tableGateway->getAdapter()->getPlatform());
	}
	
}
