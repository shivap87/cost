<?php
namespace Common\DAOGateway;
use Zend\Db\TableGateway\TableGateway;

class EssayDAO
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}
	
	public function insertEssay($input){	    
	    $affectedRows=$this->tableGateway->insert($input);	    
	    return $this->tableGateway->lastInsertValue;
	    	   
	}

	public function getAllEssay($uid){	
		$resultSet = $this->tableGateway->select(array('user_id' => $uid));		
		return $resultSet;
	}
	
	
	public function updateEssayFeedback($value,$id){		
		$rowsaffected=$this->tableGateway->update(array("status"=>"completed","feedback"=>$value),array('essay_id' => $id));		
		return $rowsaffected;
	}
	
	
	public function get_essay($field,$row_id,$uid){
		$row_id=trim($row_id);
		$resultSet = $this->tableGateway->select(array($field => $row_id,"user_id" => $uid));		
		return $resultSet;
	}
	
	public function updateEssay($data,$id){
		$rowsaffected=$this->tableGateway->update(array("user_input1"=>$data['user_input1'],"user_input2"=>$data['user_input2'],'submit_date'=>$data['submit_date'],"status"=>$data['status']),array('essay_id' => $id));		
		return $rowsaffected;
	}
	
	public function get_essay_history($uid){		
		$sqlSelect = $this->tableGateway->getSql()->select();		
		$sqlSelect->where->equalTo('tbl_essay.user_id', $uid);		
		$sqlSelect->join('tbl_buyservice', "tbl_buyservice.id = tbl_essay.buy_id ", array('payment_gross'), 'inner');
		$sqlSelect->order('tbl_essay.essay_id DESC');
		$statement = $this->tableGateway->getSql()->prepareStatementForSqlObject($sqlSelect);		
		$resultSet = $statement->execute();			
		return $resultSet;
	}
	
	public function get_openEssays($uid){
		$res=$this->tableGateway->select(function($select) use($uid) {		
	    $select->where->equalTo('user_id', $uid);
	    $select->where("(status= 'paid' OR status='saved' OR status='submitted')");	        	
		});		
		return $res;
	}
	
	public function getservicecount_user($uid,$sid){		
		$res=$this->tableGateway->select(function($select) use($uid,$sid) {		
	    $select->where->equalTo('user_id', $uid);
	    $select->where->equalTo('service_id', $sid);
	    $select->where("(status= 'reviewed' OR status='completed')");	        	
		});		
		return $res;
		
	}
	
	
	public function getSQLQueryString(){
		return $this->tableGateway->getSql()->getSqlPlatform()->getSqlString($this->tableGateway->getAdapter()->getPlatform());
	}
	
	
}
