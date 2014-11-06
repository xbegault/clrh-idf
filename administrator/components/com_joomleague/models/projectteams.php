<?php
/**
 * @copyright	Copyright (C) 2006-2014 joomleague.at. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
require_once( JPATH_COMPONENT . DS . 'models' . DS . 'list.php' );

/**
 * Joomleague Component projectteam Model
 *
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueModelProjectteams extends JoomleagueModelList
{
	var $_identifier = "pteams";

	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();

		$query = '	SELECT	pt.id AS projectteamid,
							pt.*,
							t.name,
							t.club_id,
							c.email AS club_email,
							(SELECT count(id)
							FROM #__joomleague_team_player tp
							WHERE projectteam_id = projectteamid and tp.published=1) playercount,
							(SELECT count(id)
							FROM #__joomleague_team_staff ts
							WHERE projectteam_id = projectteamid and ts.published=1) staffcount,
							pt.info
					FROM #__joomleague_project_team AS pt
					LEFT JOIN #__joomleague_team t on pt.team_id = t.id
					LEFT JOIN #__joomleague_club c on t.club_id = c.id
					LEFT JOIN #__joomleague_division d on d.id = pt.division_id
					LEFT JOIN #__joomleague_playground plg on plg.id = pt.standard_playground ' .
		$where . $orderby;

		return $query;
	}

	function _buildContentOrderBy()
	{
		$option = JRequest::getCmd('option');
		$mainframe	= JFactory::getApplication();
		$filter_order		= $mainframe->getUserStateFromRequest( $option . 'tl_filter_order',		'filter_order',		't.name',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option . 'tl_filter_order_Dir',	'filter_order_Dir',	'',			'word' );

		if ( $filter_order == 't.name' )
		{
			$orderby 	= ' ORDER BY t.name ' . $filter_order_Dir;
		}
		else
		{
			$orderby 	= ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ' , t.name ';
		}

		return $orderby;
	}

	function _buildContentWhere()
	{
		$option = JRequest::getCmd('option');
		$mainframe	= JFactory::getApplication();
		$where=array();
		$project_id	= $mainframe->getUserState( $option.'project' );
		$division	= (int) $mainframe->getUserStateFromRequest($option.'tl_division', 'division', 0);
		$where[] 	= ' pt.project_id = ' . $project_id;
		$division=JString::strtolower($division);
		if ($division>0)
		{
			$where[]=' d.id = '.$this->_db->Quote($division);
		}
		$where=(count($where) ? ' WHERE '.implode(' AND ',$where) : '');
		
		return $where;
	}

	/**
	 * Method to update project teams list
	 *
	 * @access	public
	 * @return	boolean	True on success
	 *
	 */
	function store( $data )
	{
		$result = true;
		$peid = $data['project_teamslist'];
		if ( $peid == null )
		{
			$query = "	DELETE
						FROM #__joomleague_project_team
						WHERE project_id = '" . $data['id'] . "'";
			$this->_db->setQuery( $query );
			if ( !$this->_db->query() )
			{
				$this->setError( $this->_db->getErrorMsg() );
				$result = false;
			}
		}
		else
		{
			JArrayHelper::toInteger( $peid );
			$peids = implode( ',', $peid );
			$query = "	DELETE
						FROM #__joomleague_project_team
						WHERE project_id = '" . $data['id'] . "' AND team_id NOT IN  (" . $peids . ")";
			$this->_db->setQuery( $query );
			if ( !$this->_db->query() )
			{
				$this->setError( $this->_db->getErrorMsg() );
				$result = false;
			}

			$query = "	UPDATE  #__joomleague_match
						SET projectteam1_id = NULL 
						WHERE projectteam1_id in (select id from #__joomleague_project_team 
												where project_id = '" . $data['id'] . "' 
												AND team_id NOT IN  (" . $peids . "))";
			$this->_db->setQuery( $query );
			if ( !$this->_db->query() )
			{
				$this->setError( $this->_db->getErrorMsg() );
				$result = false;
			}
			$query = "	UPDATE  #__joomleague_match
						SET projectteam2_id = NULL 
						WHERE projectteam2_id in (select id from #__joomleague_project_team 
												where project_id = '" . $data['id'] . "' 
												AND team_id NOT IN  (" . $peids . "))";
			$this->_db->setQuery( $query );
			if ( !$this->_db->query() )
			{
				$this->setError( $this->_db->getErrorMsg() );
				$result = false;
			}
				
		}

		$ordering = "1";
		for ( $x = 0; $x < count( $data['project_teamslist'] ); $x++ )
		{
			$query = "	INSERT IGNORE
						INTO #__joomleague_project_team
						(project_id, team_id, ordering)
						VALUES ( '" . $data['id'] . "', '".$data['project_teamslist'][$x] . "', '".$ordering++."')";
						
			$this->_db->setQuery( $query );
			if ( !$this->_db->query() )
			{
				$this->setError( $this->_db->getErrorMsg() );
				$result = false;
			}
		}
		return $result;
	}

	/**
	 * Method to update checked project teams
	 *
	 * @access	public
	 * @return	boolean	True on success
	 *
	 */
	function storeshort( $cid, $data )
	{
		$result = true;
		for ( $x = 0; $x < count( $cid ); $x++ )
		{
				
			$tblProjectteam = JTable::getInstance('Projectteam','Table');
			$tblProjectteam->id = $cid[$x];
			$tblProjectteam->division_id =			$data['division_id' . $cid[$x]];
			$tblProjectteam->start_points =			$data['start_points' .$cid[$x]];
			$tblProjectteam->points_finally =		$data['points_finally' .$cid[$x]];
			$tblProjectteam->neg_points_finally =	$data['neg_points_finally' . $cid[$x]];
			$tblProjectteam->matches_finally =		$data['matches_finally' . $cid[$x]];
			$tblProjectteam->won_finally = 			$data['won_finally' . $cid[$x]];
			$tblProjectteam->draws_finally = 		$data['draws_finally' . $cid[$x]];
			$tblProjectteam->lost_finally = 		$data['lost_finally' . $cid[$x]];
			$tblProjectteam->homegoals_finally =	$data['homegoals_finally' .$cid[$x]];
			$tblProjectteam->guestgoals_finally =	$data['guestgoals_finally' . $cid[$x]];
			$tblProjectteam->diffgoals_finally =	$data['diffgoals_finally' . $cid[$x]];
				
			if (!$tblProjectteam->check())
			{
				$this->setError($tblProjectteam->getError());
				$result = false;
			}
			if (!$tblProjectteam->store())
			{
				$this->setError($tblProjectteam->getError());
				$result = false;
			}
		}
		return $result;
	}

	/**
	 * Method to return the teams array (id, name)
	 *
	 * @access  public
	 * @return  array
	 * @since 0.1
	 */
	function getTeams()
	{
		$query = '	SELECT	id AS value,
							name AS text,
							info
					FROM #__joomleague_team
					ORDER BY text ASC ';

		$this->_db->setQuery( $query );
		if ( !$result = $this->_db->loadObjectList() )
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		else
		{
			return $result;
		}
	}

	public function changeTeamId($arrOldTeamIds, $arrNewTeamIds, &$app)
	{
		$result = true;
		for ($t=0; $t < sizeof($arrOldTeamIds); $t++ )
		{
			$project_team_id 		= $arrOldTeamIds[$t];
			$team_id_new			= $arrNewTeamIds[$project_team_id];
			
			$tblProjectTeam 	= JTable::getInstance('projectteam', 'Table');
			$tblProjectTeam->load($project_team_id);
			
			$tblOldTeam 		= JTable::getInstance('team', 'Table');
			$tblOldTeam->load($tblProjectTeam->team_id);
			$old_team_name 		= $tblOldTeam->name;
	
			$tblNewTeam 		= JTable::getInstance('team', 'Table');
			$tblNewTeam->load($team_id_new);
			$new_team_name 		= $tblNewTeam->name;
			
			$app->enqueueMessage(JText::sprintf('COM_JOOMLEAGUE_ADMIN_PROJECTTEAM_MODEL_ASSIGNED_OLD_TEAMNAME', $old_team_name, $new_team_name),'Notice');

			$tblProjectTeam->id 		= $project_team_id;
			$tblProjectTeam->team_id 	= $team_id_new;

			if (!$tblProjectTeam->store())
			{
				$this->setError($tblProjectTeam->getError());
				$result = false;
				break;
			}
		}
		return result;
	}





	/**
	 * Method to return a Teams array (id,name)
	 *
	 * @access	public
	 * @return	array seasons
	 * @since	1.5.0a
	 */
	function getAllTeams($pid)
	{
		$db = JFactory::getDbo();
		if ( $pid )
		{
			// jetzt brauchen wir noch das land der liga !
			$querycountry = "SELECT l.country
							from #__joomleague_league as l
							inner join #__joomleague_project as p
							on p.league_id = l.id
							where p.id = '$pid'
							";

			$db->setQuery( $querycountry );
			$country = $db->loadResult();

			$query="SELECT t.id as value, t.name as text
					FROM #__joomleague_team as t
					INNER JOIN #__joomleague_club as c
					ON c.id = t.club_id
					WHERE c.country = '$country'  
					ORDER BY t.name ASC 
					";

		}
		else
		{
			$query='SELECT id as value, name as text 
					FROM #__joomleague_team 
					ORDER BY name ASC ';
		}

		$db->setQuery($query);
		if (!$result=$db->loadObjectList())
		{
			$this->setError($db->getErrorMsg());
			return false;
		}
		foreach ($result as $teams){
			$teams->name = $teams->text;
		}
		return $result;
	}



	/**
	 * Method to return the project teams array (id, name)
	 *
	 * @param $project_id
	 * @access  public
	 * @return  array
	 * @since 0.1
	 */
	function getProjectTeams($project_id=0)
	{
		$query = '	SELECT	t.id AS value,
							t.name AS text,
							t.notes, pt.info
					FROM #__joomleague_team AS t
					LEFT JOIN #__joomleague_project_team AS pt ON pt.team_id = t.id
					WHERE pt.project_id = ' . $project_id . '
					ORDER BY text ASC ';

		$this->_db->setQuery( $query );
		if ( !$result = $this->_db->loadObjectList() )
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		else
		{
			return $result;
		}
	}

	/**
	 * copy teams to other projects
	 * 
	 * @param int $dest destination project id
	 * @param array $ptids teams to transfer
	 */
	function copy($dest, $ptids)
	{
		if (!$dest)
		{
			$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_Destination_project_required'));
			return false;
		}
		
		if (!is_array($ptids) || !count($ptids))
		{
			$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_PROJECTTEAMS_no_teams_to_copy'));
			return false;
		}
		
		// first copy the teams
		$query = ' INSERT INTO #__joomleague_project_team (team_id, project_id, info, picture, standard_playground, extended)' 
		       . ' SELECT team_id, '.$dest.', info, picture, standard_playground, extended '
		       . ' FROM #__joomleague_project_team '
		       . ' WHERE id IN (' . implode(',', $ptids).')';
		$this->_db->setQuery($query);
		$res = $this->_db->query();
		
		if (!$res) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		// now copy the players
		$query = ' INSERT INTO #__joomleague_team_player (projectteam_id, person_id, jerseynumber, picture, extended, published) ' 
		       . ' SELECT dest.id AS projectteam_id, tp.person_id, tp.jerseynumber, tp.picture, tp.extended,tp.published '
		       . ' FROM #__joomleague_team_player AS tp '
		       . ' INNER JOIN #__joomleague_project_team AS pt ON pt.id = tp.projectteam_id '
		       . ' INNER JOIN #__joomleague_project_team AS dest ON pt.team_id = dest.team_id AND dest.project_id = '.$dest 
		       . ' WHERE pt.id IN (' . implode(',', $ptids).')';
		$this->_db->setQuery($query);
		$res = $this->_db->query();
				
		// and finally the staff
		$query = ' INSERT INTO #__joomleague_team_staff (projectteam_id, person_id, picture, extended, published) '
				       . ' SELECT dest.id AS projectteam_id, tp.person_id, tp.picture, tp.extended,tp.published '
				       . ' FROM #__joomleague_team_staff AS tp '
				       . ' INNER JOIN #__joomleague_project_team AS pt ON pt.id = tp.projectteam_id '
				       . ' INNER JOIN #__joomleague_project_team AS dest ON pt.team_id = dest.team_id AND dest.project_id = '.$dest 
		. ' WHERE pt.id IN (' . implode(',', $ptids).')';
		$this->_db->setQuery($query);
		$res = $this->_db->query();
		
		if (!$res) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		
		
		return true;
	}

	/**
	 * return count of projectteams
	 *
	 * @param int project_id
	 * @return int
	 */
	function getProjectTeamsCount($project_id)
	{
		$query='SELECT count(*) AS count
				FROM #__joomleague_project_team AS pt
				JOIN #__joomleague_project AS p on p.id = pt.project_id
				WHERE p.id='.$project_id;
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
	
	
}
?>
