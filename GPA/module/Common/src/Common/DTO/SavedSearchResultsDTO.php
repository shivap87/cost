<?php

namespace Common\DTO;
class SavedSearchResultsDTO{
	public $id;
	public $searchcriteria_id;
	public $resulttype;
	public $collegename;
	
	public $relativeCOA;
	public $onCampusCOA;
	public $size;
	public $universitySystem;
	public $environment;
	
	
	
	
	
	
	
	public function exchangeArray($data)
	{
		$this->id=(!empty($data['id'])) ? $data['id'] : null;
		$this->searchcriteria_id=(!empty($data['searchcriteria_id'])) ? $data['searchcriteria_id'] : null;
		$this->resulttype=(!empty($data['resulttype'])) ? $data['resulttype'] : null;
		$this->collegename=(!empty($data['collegename'])) ? $data['collegename'] : null;
		
		$this->collegename=(!empty($data['relativeCOA'])) ? $data['relativeCOA'] : null;
		$this->collegename=(!empty($data['onCampusCOA'])) ? $data['onCampusCOA'] : null;
		$this->collegename=(!empty($data['size'])) ? $data['size'] : null;
		$this->collegename=(!empty($data['universitySystem'])) ? $data['universitySystem'] : null;
		$this->collegename=(!empty($data['environment'])) ? $data['environment'] : null;
		
	}
}