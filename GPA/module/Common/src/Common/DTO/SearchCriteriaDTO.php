<?php
namespace Common\DTO;
class SearchCriteriaDTO
{
	public $id;
	public $tbl_user_user_id;
	public $exam;
	public $gpa;
	public $actc;
	public $actew;
	public $satcr;
	public $satm;
	public $satw;
	public $psatcr;
	public $psatm;
	public $psatw;
	public $location;
	
	public $searchtype;
	public $us;
	public $freelunch;
	public $noca;
	public $major;
	public $csize;
	public $cpref;
	
	
	public function exchangeArray($data)
	{
		$this->id     = (!empty($data['id'])) ? $data['id'] : null;
		$this->tbl_user_user_id     = (!empty($data['tbl_user_user_id'])) ? $data['tbl_user_user_id'] : null;
		$this->exam     = (!empty($data['exam'])) ? $data['exam'] : null;
		$this->gpa     = (!empty($data['gpa'])) ? $data['gpa'] : null;
		$this->actc     = (!empty($data['actc'])) ? $data['actc'] : null;
		$this->actew     = (!empty($data['actew'])) ? $data['actew'] : null;
		$this->satcr     = (!empty($data['satcr'])) ? $data['satcr'] : null;
		$this->satm     = (!empty($data['satm'])) ? $data['satm'] : null;
		$this->satw     = (!empty($data['satw'])) ? $data['satw'] : null;
		$this->psatcr     = (!empty($data['psatcr'])) ? $data['psatcr'] : null;
		$this->psatm     = (!empty($data['psatm'])) ? $data['psatm'] : null;
		$this->psatw     = (!empty($data['psatw'])) ? $data['psatw'] : null;
		$this->location     = (!empty($data['location'])) ? $data['location'] : null;
		
		$this->location     = (!empty($data['searchtype'])) ? $data['searchtype'] : null;
		$this->location     = (!empty($data['us'])) ? $data['us'] : null;
		$this->location     = (!empty($data['freelunch'])) ? $data['freelunch'] : null;
		$this->location     = (!empty($data['noca'])) ? $data['noca'] : null;
		$this->location     = (!empty($data['major'])) ? $data['major'] : null;
		$this->location     = (!empty($data['csize'])) ? $data['csize'] : null;
		$this->location     = (!empty($data['cpref'])) ? $data['cpref'] : null;
		
	
	}
}
