<?php

namespace Common\DAOGateway;
use Zend\Db\TableGateway\TableGateway;

class RoleDAO
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	}
}
