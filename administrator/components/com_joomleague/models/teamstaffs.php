<?php
/**
 * @copyright	Copyright (C) 2006-2014 joomleague.at. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
require_once (JPATH_COMPONENT.DS.'models'.DS.'list.php');

/**
 * Joomleague Component TeamStaffs Model
 *
 * @author	Kurt Norgaz <kurtnorgaz@web.de>
 * @package	JoomLeague
 * @since	1.5.01a
 */
class JoomleagueModelTeamStaffs extends JoomleagueModelList
{
	var $_identifier = "teamstaffs";

	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where=$this->_buildContentWhere();
		$orderby=$this->_buildContentOrderBy();
		$query='	SELECT	ppl.firstname,
							ppl.lastname,
							ppl.nickname,
							ts.*,
							ts.project_position_id,
							u.name AS editor
					FROM #__joomleague_person AS ppl
					INNER JOIN #__joomleague_team_staff AS ts on ts.person_id=ppl.id
					LEFT JOIN #__users AS u ON u.id=ts.checked_out '
					. $where
					. $orderby;
					return $query;
	}

	function _buildContentOrderBy()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$filter_order		= $mainframe->getUserStateFromRequest($option.'ts_filter_order',		'filter_order',		'ppl.ordering',	'cmd');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest($option.'ts_filter_order_Dir',	'filter_order_Dir',	'',				'word');
		if ($filter_order=='ppl.lastname')
		{
			$orderby=' ORDER BY ppl.lastname '.$filter_order_Dir;
		}
		else
		{
			$orderby=' ORDER BY '.$filter_order.' '.$filter_order_Dir.', ppl.lastname ';
		}
		return $orderby;
	}

	function _buildContentWhere()
	{
		$option 		= 'com_joomleague';
		$mainframe		= JFactory::getApplication();
		$project_id		= $mainframe->getUserState($option.'project');
		$team_id		= $mainframe->getUserState($option.'project_team_id');
		$filter_state	= $mainframe->getUserStateFromRequest( $option . 'ts_filter_state', 'filter_state', '', 'word' );
		$search			= $mainframe->getUserStateFromRequest($option.'ts_search', 'search', '', 'string');
		$search_mode	= $mainframe->getUserStateFromRequest($option.'ts_search_mode','search_mode', '', 'string');
		$search			= JString::strtolower($search);
		$where=array();
		$where[]='ts.projectteam_id='.$team_id;
		$where[]="ppl.published = '1'";
		if ($search)
		{
			if ($search_mode)
			{
				$where[]='LOWER(lastname) LIKE '.$this->_db->Quote($search.'%');
			}
			else
			{
				$where[]='LOWER(lastname) LIKE '.$this->_db->Quote('%'.$search.'%');
			}
		}

		if ( $filter_state )
		{
			if ( $filter_state == 'P' )
			{
				$where[] = 'ts.published = 1';
			}
			elseif ($filter_state == 'U' )
			{
				$where[] = 'ts.published = 0';
			}
		}

		$where=(count($where) ? ' WHERE '.implode(' AND ',$where) : '');
		return $where;
	}

	/**
	 * Method to update checked project teams
	 *
	 * @access	public
	 * @return	boolean	True on success
	 *
	 */
	function storeshort($cid,$data)
	{
		$result=true;
		for ($x=0; $x < count($cid); $x++)
		{
			$query="	UPDATE #__joomleague_team_staff
						SET project_position_id='".$data['project_position_id'.$cid[$x]]."',
							checked_out=0,
							checked_out_time=0
							WHERE id=".$cid[$x];
			$this->_db->setQuery($query);
			if(!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				$result= false;
			}
		}
		return $result;
	}

	/**
	 * Method to return the teams array (id,name)
	 *
	 * @access  public
	 * @return  array
	 * @since 0.1
	 */
	function getPersons()
	{
		$query="	SELECT	id AS value,
							lastname,
							nickname,
							firstname,
							info,
							team_id,
							weight,
							height,
							picture,
							birthday,
							position_id,
							notes,
							nickname,
							knvbnr,
							nation
					FROM #__joomleague_person
					WHERE team_id = 0 AND published = '1'
					ORDER BY firstname ASC ";
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $result;
	}

	/**
	 * Method to return a divisions array (id,name)
	 *
	 * @access  public
	 * @return  array
	 * @since 0.1
	 */
	function getDivisions()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$project_id=$mainframe->getUserState($option.'project');
		$query="SELECT id AS value, name AS text FROM #__joomleague_division WHERE project_id=$project_id ORDER BY name ASC ";
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return $result;
	}

	/**
	 * Method to return a positions array (id,position)
		*
		* @access  public
		* @return  array
		* @since 0.1
		*/
	function getPositions()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$project_id=$mainframe->getUserState($option.'project');
		$query="	SELECT ppos.id AS value, pos.name AS text
					FROM #__joomleague_position AS pos
					INNER JOIN #__joomleague_project_position AS ppos ON ppos.position_id=pos.id
					WHERE ppos.project_id=$project_id AND pos.persontype=2
					ORDER BY ordering ";
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		foreach ($result as $position){
			$position->text=JText::_($position->text);
		}
		return $result;
	}

	/**
	 * return list of project teams for select options
	 *
	 * @return array
	 */
	function getProjectTeamList()
	{
		$query='	SELECT	t.id AS value,
							t.name AS text
					FROM #__joomleague_team AS t
					INNER JOIN  #__joomleague_project_team AS tt ON tt.team_id=t.id
					WHERE tt.project_id='.$this->_project_id;
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * add the specified persons to team
	 *
	 * @param array int teamstaff ids
	 * @param int team id
	 * @return int number of row inserted
	 */
	function storeAssigned($cid,$projectteam_id)
	{
		if (!count($cid) || !$projectteam_id){return 0;}
		$query="	SELECT	pt.id
					FROM #__joomleague_person AS pt
					INNER JOIN #__joomleague_team_staff AS r ON r.person_id=pt.id
					WHERE r.projectteam_id=".(int)$projectteam_id." AND pt.published = '1'";
		$this->_db->setQuery($query);
		$current=$this->_db->loadColumn();
		$added=0;
		foreach ($cid AS $pid)
		{
			if (!in_array($pid,$current))
			{
				$tblTeamstaff = JTable::getInstance('Teamstaff','Table');
				$tblTeamstaff->person_id		= $pid;
				$tblTeamstaff->projectteam_id	= $projectteam_id;
				$tblTeamstaff->published		= 1;
				
				$tblProjectTeam = JTable::getInstance( 'Projectteam', 'Table' );
				$tblProjectTeam->load($projectteam_id);

				if (!$tblTeamstaff->check())
				{
					$this->setError($tblTeamstaff->getError());
					continue;
				}
				//Get data from person
				$query = "	SELECT picture, position_id
							FROM #__joomleague_person AS pl
							WHERE pl.id=". $this->_db->Quote($pid)."
							pl.published = '1'";
				$this->_db->setQuery( $query );
				$person = $this->_db->loadObject();
				if ( $person )
				{
					$query = "SELECT id FROM #__joomleague_project_position ";
					$query.= " WHERE position_id = " . $this->_db->Quote($person->position_id);
					$query.= " AND project_id = " . $this->_db->Quote($tblProjectTeam->project_id);
					$this->_db->setQuery($query);
					if ($resPrjPosition = $this->_db->loadObject())
					{
						$tblTeamstaff->project_position_id = $resPrjPosition->id;
					}
						
					$tblTeamstaff->picture			= $person->picture;
					$tblTeamstaff->projectteam_id	= $projectteam_id;
						
				}
				if (!$tblTeamstaff->store())
				{
					$this->setError($tblTeamstaff->getError());
					continue;
				}
				$added++;
			}
		}
		return $added;
	}

	/**
	 * remove staffs from team
	 * @param $cids staff ids
	 * @return int count of staffs removed
	 */
	function remove($cids)
	{
		$count=0;
		foreach ($cids as $cid)
		{
			$object=&$this->getTable('teamstaff');
			if ($object->canDelete($cid) && $object->delete($cid))
			{
				$count++;
			}
			else
			{
				$this->setError(JText::sprintf('COM_JOOMLEAGUE_ADMIN_TEAMSTAFFS_MODEL_ERROR_REMOVE_STAFF',$object->getError()));
			}
		}
		return $count;
	}

}
?>