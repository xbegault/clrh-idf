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
 * Joomleague Component TeamPlayers Model
 *
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueModelTeamPlayers extends JoomleagueModelList
{
	var $_identifier = "teamplayers";

	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where=$this->_buildContentWhere();
		$orderby=$this->_buildContentOrderBy();
		$query='	SELECT	ppl.firstname,
							ppl.lastname,
							ppl.nickname,
							ppl.height,
							ppl.weight, ppl.id, tp.id as tpid, 
							ppl.id AS person_id,
							tp.*,
							tp.project_position_id,
							u.name AS editor
					FROM #__joomleague_person AS ppl
					INNER JOIN #__joomleague_team_player AS tp ON tp.person_id=ppl.id
					LEFT JOIN #__users AS u ON u.id=tp.checked_out '
					. $where
					. $orderby;
					return $query;
	}

	function _buildContentOrderBy()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$filter_order		= $mainframe->getUserStateFromRequest($option.'tp_filter_order',		'filter_order',		'ppl.ordering',	'cmd');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest($option.'tp_filter_order_Dir',	'filter_order_Dir',	'',				'word');
		if ($filter_order=='ppl.lastname')
		{
			$orderby=' ORDER BY ppl.lastname '.$filter_order_Dir;
		}
		else
		{
			$orderby=' ORDER BY '.$filter_order.' '.$filter_order_Dir.',ppl.lastname ';
		}
		return $orderby;
	}

	function _buildContentWhere()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$project_id=$mainframe->getUserState($option.'project');
		$team_id=$mainframe->getUserState($option.'project_team_id');
		$filter_state	= $mainframe->getUserStateFromRequest( $option . 'tp_filter_state', 'filter_state', '', 'word' );
		$search			= $mainframe->getUserStateFromRequest($option.'tp_search',		'search',		'',		'string');
		$search_mode	= $mainframe->getUserStateFromRequest($option.'tp_search_mode','search_mode',	'',		'string');
		$search=JString::strtolower($search);
		$where=array();
		$where[]='tp.projectteam_id= '.$team_id;
		$where[]="ppl.published = '1'";
		if ($search)
		{
			if ($search_mode)
			{
				$where[]='(LOWER(ppl.lastname) LIKE '.$this->_db->Quote($search.'%') .
							'OR LOWER(ppl.firstname) LIKE '.$this->_db->Quote($search.'%') .
							'OR LOWER(ppl.nickname) LIKE '.$this->_db->Quote($search.'%').')';
			}
			else
			{
				$where[]='(LOWER(ppl.lastname) LIKE '.$this->_db->Quote('%'.$search.'%').
							'OR LOWER(ppl.firstname) LIKE '.$this->_db->Quote('%'.$search.'%') .
							'OR LOWER(ppl.nickname) LIKE '.$this->_db->Quote('%'.$search.'%').')';
			}
		}

		if ( $filter_state )
		{
			if ( $filter_state == 'P' )
			{
				$where[] = 'tp.published = 1';
			}
			elseif ($filter_state == 'U' )
			{
				$where[] = 'tp.published = 0';
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
			$query="	UPDATE #__joomleague_team_player
						SET project_position_id='" .		$data['project_position_id'.$cid[$x]] .	"',
							jerseynumber='" .		$data['jerseynumber'.$cid[$x]] .	"',
							checked_out=0,
							checked_out_time=0
							WHERE id=" .			$cid[$x];
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
	 * Method to return the players array (projectid,teamid)
	 *
	 * @access  public
	 * @return  array
	 * @since 0.1
	 */
	function getPersons()
	{
		$query="	SELECT	id AS value,
							lastname,
							firstname,
							info,
							weight,
							height,
							picture,
							birthday,
							notes,
							nickname,
							knvbnr,
							country
					FROM #__joomleague_person
					WHERE published = '1'
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
		$query="	SELECT id AS value, name AS text
					FROM #__joomleague_division
					WHERE project_id=$project_id ORDER BY name ASC ";
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

		$query="	SELECT pp.id AS value,name AS text
					FROM #__joomleague_position AS p
					LEFT JOIN #__joomleague_project_position AS pp ON pp.position_id=p.id
					WHERE pp.project_id=$project_id AND p.persontype=1
					ORDER BY ordering ";
		$this->_db->setQuery($query);
		if (!$result=$this->_db->loadObjectList())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		foreach ($result as $position){$position->text=JText::_($position->text);}
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
	 * @param array int player ids
	 * @param int team id
	 * @return int number of row inserted
	 */
	function storeAssigned($cid, $projectteam_id)
	{
		if (!count($cid) || !$projectteam_id){return 0;}
		$query="	SELECT	p.id
					FROM #__joomleague_person AS p
					INNER JOIN #__joomleague_team_player AS tp ON tp.person_id=p.id
					WHERE tp.projectteam_id=".$this->_db->Quote($projectteam_id)." AND p.published = '1'";
		$this->_db->setQuery($query);
		$current=$this->_db->loadColumn();
		$added=0;
		foreach ($cid AS $pid)
		{
			if (!in_array($pid,$current))
			{

				$tblTeamplayer = JTable::getInstance( 'Teamplayer', 'Table' );
				$tblTeamplayer->person_id		= $pid;
				$tblTeamplayer->projectteam_id	= $projectteam_id;
				$tblTeamplayer->published		= 1;
				
				$tblProjectTeam = JTable::getInstance( 'Projectteam', 'Table' );
				$tblProjectTeam->load($projectteam_id);
					
				if ( !$tblTeamplayer->check() )
				{
					$this->setError( $tblTeamplayer->getError() );
					continue;
				}
				// Get data from player
				$query = "	SELECT picture, position_id
							FROM #__joomleague_person AS pl
							WHERE pl.id=". $this->_db->Quote($pid);
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
						$tblTeamplayer->project_position_id = $resPrjPosition->id;
					}

					$tblTeamplayer->picture			= $person->picture;
					$tblTeamplayer->projectteam_id	= $projectteam_id;

				}
				$query = "	SELECT max(ordering) count
							FROM #__joomleague_team_player";
				$this->_db->setQuery( $query );
				$tp = $this->_db->loadObject();
				
				$tblTeamplayer->ordering = (int) $tp->count + 1;
				if ( !$tblTeamplayer->store() )
				{
					$this->setError( $tblTeamplayer->getError() );
					continue;
				}
				$added++;
			}
		}

		return $added;
	}

	/**
	 * remove specified players from team
	 * @param $cids player ids
	 * @return int count of removed
	 */
	function remove($cids)
	{
		$count=0;
		foreach($cids as $cid)
		{
			$object=&$this->getTable('teamplayer');
			if ($object->canDelete($cid) && $object->delete($cid))
			{
				$count++;
			}
			else
			{
				$this->setError(JText::sprintf('COM_JOOMLEAGUE_ADMIN_TEAMSTAFFS_MODEL_ERROR_REMOVE_TEAMPLAYER',$object->getError()));
			}
		}
		return $count;
	}

}
?>