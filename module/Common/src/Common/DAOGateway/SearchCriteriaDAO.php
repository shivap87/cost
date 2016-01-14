<?php
namespace Common\DAOGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
class SearchCriteriaDAO{
	
	protected $tableGateway;
	
	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}
	
	public function fetchAll()
	{
		$resultSet = $this->tableGateway->select();
		return $resultSet;
	}
	
	//(NULL, 'act', '3.46', '34', '', '', '', '', '', '', '', '')
	
	
	public function insertAdvanceRow($tbl_user_user_id,$exam,$gpa,$actc,$actew,$satcr,$satm,$satw,$psatcr,$psatm,$psatw,$location,$searchtype,$us,$freelunch,$noca,$major,$csize,$cpref){
		$arr=array('tbl_user_user_id'=>$tbl_user_user_id,
				"exam"=>$exam,
				"gpa"=>$gpa,
				"actc"=>$actc,
				"actew"=>$actew,
				"satcr"=>$satcr,
				"satm"=>$satm,
				"satw"=>$satw,
				"psatcr"=>$psatcr,
				"psatm"=>$psatm,
				"psatw"=>$psatw,
				"location"=>$location,
				"searchtype"=>$searchtype,
				"us"=>$us,
				"freelunch"=>$freelunch,
				"noca"=>$noca,
				"major"=>$major,
				"csize"=>$csize,
				"cpref"=>$cpref
				
		);
		
		$affectedRows=$this->tableGateway->insert($arr);
		
		if($affectedRows == 1){
			$res=$this->tableGateway->select(function(Select $select) use ($tbl_user_user_id){
				$select->where(array("tbl_user_user_id"=>$tbl_user_user_id))
				->order('insertdate DESC');
			});
			$row=$res->current();
			return $row->id;
		}
		return $affectedRows;
	}
	
	public function insertRow($tbl_user_user_id,$exam,$gpa,$actc,$actew,$satcr,$satm,$satw,$psatcr,$psatm,$psatw,$location){
		$arr=array('tbl_user_user_id'=>$tbl_user_user_id,
				"exam"=>$exam,
				"gpa"=>$gpa,
				"actc"=>$actc,
				"actew"=>$actew,
				"satcr"=>$satcr,
				"satm"=>$satm,
				"satw"=>$satw,
				"psatcr"=>$psatcr,
				"psatm"=>$psatm,
				"psatw"=>$psatw,
				"location"=>$location);

		$affectedRows=$this->tableGateway->insert($arr);
		
	if($affectedRows == 1){
		$res=$this->tableGateway->select(function(Select $select) use ($tbl_user_user_id){
			$select->where(array("tbl_user_user_id"=>$tbl_user_user_id))
			->order('insertdate DESC');
		});
			$row=$res->current();
			return $row->id;
		}
 		return $affectedRows;
	}
	
}