<?php
/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * Contributor(s): YetiForce.com
 * *********************************************************************************** */

/**
 * Portal ListView Model Class
 */
class Portal_ListView_Model extends Vtiger_ListView_Model
{

	public function getListViewEntries(Vtiger_Paging_Model $pagingModel, $searchResult = false)
	{
		$db = PearDatabase::getInstance();
		$moduleModel = Vtiger_Module_Model::getInstance('Portal');

		$query = $this->getQuery();

		$startIndex = $pagingModel->getStartIndex();
		$pageLimit = $pagingModel->getPageLimit();

		$orderBy = $this->get('orderby');
		$sortOrder = $this->get('sortorder');

		if (!empty($orderBy)) {
			if ($sortOrder === 'ASC') {
				$query->orderBy([$orderBy => SORT_ASC]);
			} else {
				$query->orderBy([$orderBy => SORT_DESC]);
			}
		}
		$query->limit($pageLimit);
		$query->offset($startIndex);
		$dataReader = $query->all();

		$listViewEntries = [];
		foreach ($dataReader as $row) {
			$listViewEntries[$row['portalid']] = [];
			$listViewEntries[$row['portalid']]['portalname'] = $row['portalname'];
			$listViewEntries[$row['portalid']]['portalurl'] = $row['portalurl'];
			$listViewEntries[$row['portalid']]['createdtime'] = Vtiger_Date_UIType::getDisplayDateValue($row['createdtime']);
		}
		$index = 0;
		foreach ($listViewEntries as $recordId => $record) {
			$record['id'] = $recordId;
			$listViewRecordModels[$recordId] = $moduleModel->getRecordFromArray($record, $dataReader[$index++]);
		}

		return $listViewRecordModels;
	}

	public function getQuery()
	{
		$query = (new \App\Db\Query())
			->select(['portalid', 'portalname', 'portalurl', 'createdtime'])
			->from('vtiger_portal');
		$searchValue = $this->getForSql('search_value');
		if (!empty($searchValue)) {
			$query->where(['like', 'portalname', $searchValue]);
		}
		return $query;
	}

	public function calculatePageRange($record, $pagingModel)
	{
		$pageLimit = $pagingModel->getPageLimit();
		$page = $pagingModel->get('page');

		$startSequence = ($page - 1) * $pageLimit + 1;
		$endSequence = $startSequence + count($record) - 1;
		$recordCount = Portal_ListView_Model::getRecordCount();

		$pageCount = intval($recordCount / $pageLimit);
		if (($recordCount % $pageLimit) != 0)
			$pageCount++;
		if ($pageCount == 0)
			$pageCount = 1;
		if ($page < $pageCount)
			$nextPageExists = true;
		else
			$nextPageExists = false;

		$result = array(
			'startSequence' => $startSequence,
			'endSequence' => $endSequence,
			'recordCount' => $recordCount,
			'pageCount' => $pageCount,
			'nextPageExists' => $nextPageExists,
			'pageLimit' => $pageLimit
		);

		return $result;
	}

	public function getRecordCount()
	{
		return $this->getQuery()->count();
	}
}
