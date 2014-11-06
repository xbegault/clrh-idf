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

require_once( JLG_PATH_SITE . DS . 'models' . DS . 'project.php' );

class JoomleagueModelMatchReport extends JoomleagueModelProject
{

	var $matchid=0;
	var $match=null;

	/**
	 * caching for players events. Used in stats calculations
	 * @var unknown_type
	 */
	var $_playersevents=null;

	/**
	 * caching for players basic stats. Used in stats calculations
	 * @var unknown_type
	 */
	var $_playersbasicstats=null;

	/**
	 * caching for staff basic stats. Used in stats calculations
	 * @var unknown_type
	 */
	var $_staffsbasicstats=null;

	function __construct()
	{
		$this->matchid=JRequest::getInt('mid',0);
		parent::__construct();
	}

	// Functions (some specific for Matchreport) below to be replaced to project.php when recoded to general functions
	function &getMatch()
	{
		if (is_null($this->match))
		{
			$query='SELECT m.*,DATE_FORMAT(m.time_present,"%H:%i") time_present, r.project_id, p.timezone 
					FROM #__joomleague_match AS m 
					INNER JOIN #__joomleague_round AS r on r.id=m.round_id 
					INNER JOIN #__joomleague_project AS p on r.project_id=p.id 
					WHERE m.id='. $this->_db->Quote($this->matchid)
			      ;
			$this->_db->setQuery($query,0,1);
			$this->match=$this->_db->loadObject();
			if ($this->match)
			{
				JoomleagueHelper::convertMatchDateToTimezone($this->match);
			}
		}
		return $this->match;
	}

	function &getProject()
	{
		if (empty($this->_project))
		{
			$match=&$this->getMatch();
			$this->setProjectID($match->project_id);
			parent::getProject();
		}
		return $this->_project;
	}

	function getClubinfo($clubid)
	{
		$this->club =& $this->getTable('Club','Table');
		$this->club->load($clubid);

		return $this->club;
	}

	function getRound()
	{
		$match=$this->getMatch();

		$round =& $this->getTable('Round','Table');
		$round->load($match->round_id);

		//if no match title set then set the default one
		if(is_null($round->name) || empty($round->name))
		{
			$round->name=JText::sprintf('COM_JOOMLEAGUE_RESULTS_GAMEDAY_NB',$round->id);
		}

		return $round;
	}

	function getMatchPlayerPositions()
	{
		$query='	SELECT	pos.id, pos.name, 
							ppos.position_id AS position_id, ppos.id as pposid
					FROM #__joomleague_position AS pos
					INNER JOIN #__joomleague_project_position AS ppos ON pos.id=ppos.position_id
					INNER JOIN #__joomleague_match_player AS mp ON ppos.id=mp.project_position_id
					WHERE mp.match_id='.(int)$this->matchid.'
					GROUP BY pos.id
					ORDER BY pos.ordering ASC ';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	function getMatchStaffPositions()
	{
		$query='	SELECT	pos.id, pos.name, 
							ppos.position_id AS position_id, ppos.id as pposid
					FROM #__joomleague_position AS pos
					INNER JOIN #__joomleague_project_position AS ppos ON pos.id=ppos.position_id
					INNER JOIN #__joomleague_match_staff AS mp ON ppos.id=mp.project_position_id
					WHERE mp.match_id='.(int)$this->matchid.'
					GROUP BY pos.id
					ORDER BY pos.ordering ASC ';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	function getMatchRefereePositions()
	{
		$query='	SELECT	pos.id, pos.name, 
							ppos.position_id AS position_id, ppos.id as pposid
					FROM #__joomleague_position AS pos
					INNER JOIN #__joomleague_project_position AS ppos ON pos.id=ppos.position_id
					INNER JOIN #__joomleague_match_referee AS mp ON ppos.id=mp.project_position_id
					WHERE mp.match_id='.(int)$this->matchid.'
					GROUP BY pos.id
					ORDER BY pos.ordering ASC ';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	function getMatchPlayers()
	{
		$query=' SELECT	pt.id,'
		      .' tp.person_id,'
		      .' p.firstname,'
		      .' p.nickname,'
		      .' p.lastname,'
		      .' tp.jerseynumber,'
		      .' ppos.position_id,'
		      .' ppos.id AS pposid,'
		      .' pt.team_id,'
		      .' pt.id as ptid,'
		      .' mp.teamplayer_id,'
		      .' tp.picture,'
			  .' p.picture AS ppic,'
		      .' CASE WHEN CHAR_LENGTH(t.alias) THEN CONCAT_WS(\':\',t.id,t.alias) ELSE t.id END AS team_slug,'
		      .' CASE WHEN CHAR_LENGTH(p.alias) THEN CONCAT_WS(\':\',p.id,p.alias) ELSE p.id END AS person_slug '
		      .' FROM #__joomleague_match_player AS mp '
		      .' INNER JOIN	#__joomleague_team_player AS tp ON tp.id=mp.teamplayer_id '
		      .' INNER JOIN	#__joomleague_project_team AS pt ON pt.id=tp.projectteam_id '
		      .' INNER JOIN	#__joomleague_team AS t ON t.id=pt.team_id '
		      .' INNER JOIN	#__joomleague_person AS p ON tp.person_id=p.id '
		      .' LEFT JOIN #__joomleague_project_position AS ppos ON ppos.id=mp.project_position_id '
		      .' LEFT JOIN #__joomleague_position AS pos ON ppos.position_id=pos.id '
		      .' WHERE mp.match_id='.(int)$this->matchid
		      .' AND mp.came_in=0 '
		      .' AND p.published = 1 '
			  .' GROUP BY tp.id '
		      .' ORDER BY mp.ordering, tp.jerseynumber, p.lastname ';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	function getMatchStaff()
	{
		$query=' SELECT	p.id,'
		      .' p.id AS person_id,'
		      .' ms.team_staff_id,'
		      .' p.firstname,'
		      .' p.nickname,'
		      .' p.lastname,'
		      .' ppos.position_id,'
		      .' ppos.id AS pposid,'
		      .' pt.team_id,'
		      .' pt.id as ptid,'
		      .' tp.picture,'
			  .' CASE WHEN CHAR_LENGTH(t.alias) THEN CONCAT_WS(\':\',t.id,t.alias) ELSE t.id END AS team_slug,'
			  .' CASE WHEN CHAR_LENGTH(p.alias) THEN CONCAT_WS(\':\',p.id,p.alias) ELSE p.id END AS person_slug '
		      .' FROM #__joomleague_match_staff AS ms '
		      .' INNER JOIN	#__joomleague_team_staff AS tp ON tp.id=ms.team_staff_id '
		      .' INNER JOIN	#__joomleague_project_team AS pt ON pt.id=tp.projectteam_id '
		      .' INNER JOIN	#__joomleague_person AS p ON tp.person_id=p.id '
		      .' INNER JOIN	#__joomleague_team AS t ON t.id=pt.team_id '
		      .' LEFT JOIN #__joomleague_project_position AS ppos ON ppos.id=ms.project_position_id '
		      .' LEFT JOIN #__joomleague_position AS pos ON ppos.position_id=pos.id '
		      .' WHERE ms.match_id='.(int)$this->matchid
		      .'  AND p.published = 1';
		       $this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	function getMatchReferees()
	{
		$query=' SELECT	p.id,'
		      .' p.firstname,'
		      .' p.nickname,'
		      .' p.lastname,'
		      .' ppos.position_id,'
		      .' ppos.id AS pposid,'
		      .' pos.name AS position_name ,'
		      .' CASE WHEN CHAR_LENGTH(p.alias) THEN CONCAT_WS(\':\',p.id,p.alias) ELSE p.id END AS person_slug '
		      .' FROM #__joomleague_match_referee AS mr '
		      .' LEFT JOIN #__joomleague_project_referee AS pref ON mr.project_referee_id=pref.id '
		      .' INNER JOIN #__joomleague_person AS p ON pref.person_id=p.id '
		      .' LEFT JOIN #__joomleague_project_position AS ppos ON ppos.id=mr.project_position_id '
		      .' LEFT JOIN #__joomleague_position AS pos ON ppos.position_id=pos.id '
		      .' WHERE mr.match_id='.(int)$this->matchid
		      .' AND p.published = 1';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	function getSubstitutes()
	{
		$query=' SELECT	mp.in_out_time,
						mp.teamplayer_id,
						pt.team_id,
						pt.id AS ptid,
						tp.person_id,
						tp.jerseynumber,
						tp2.person_id AS out_person_id,
						mp.in_for,
						p2.id AS out_ptid,
						p.firstname,
						p.nickname,
						p.lastname,
						pos.name AS in_position,
						pos2.name AS out_position,
						p2.firstname AS out_firstname,
						p2.nickname AS out_nickname,
						p2.lastname AS out_lastname,
						ppos.id AS pposid1,
						ppos2.id AS pposid2,
						CASE WHEN CHAR_LENGTH(t.alias) THEN CONCAT_WS(\':\',t.id,t.alias) ELSE t.id END AS team_slug,
						CASE WHEN CHAR_LENGTH(p.alias) THEN CONCAT_WS(\':\',p.id,p.alias) ELSE p.id END AS person_slug
					FROM #__joomleague_match_player AS mp
						LEFT JOIN #__joomleague_team_player AS tp ON mp.teamplayer_id=tp.id
						LEFT JOIN #__joomleague_project_team AS pt ON tp.projectteam_id=pt.id
						LEFT JOIN #__joomleague_person AS p ON tp.person_id=p.id
						  AND p.published = 1
						LEFT JOIN #__joomleague_team_player AS tp2 ON mp.in_for=tp2.id
						LEFT JOIN #__joomleague_person AS p2 ON tp2.person_id=p2.id
						  AND p2.published = 1
						LEFT JOIN #__joomleague_project_position AS ppos ON ppos.id=mp.project_position_id
						LEFT JOIN #__joomleague_position AS pos ON ppos.position_id=pos.id
						LEFT JOIN #__joomleague_match_player AS mp2 ON mp.match_id=mp2.match_id and mp.in_for=mp2.teamplayer_id
						LEFT JOIN #__joomleague_project_position AS ppos2 ON ppos2.id=mp2.project_position_id
						LEFT JOIN #__joomleague_position AS pos2 ON ppos2.position_id=pos2.id
						INNER JOIN #__joomleague_team AS t ON t.id=pt.team_id
					WHERE mp.match_id = '.(int)$this->matchid.' 
					  AND mp.came_in > 0
					GROUP BY mp.in_out_time, mp.teamplayer_id, pt.team_id
					ORDER by (mp.in_out_time+0)';
		$this->_db->setQuery($query);
		//echo($this->_db->getQuery());
		$result=$this->_db->loadObjectList();
		return $result;
	}

	function getEventTypes()
	{
		$query='	SELECT	et.id,
							et.name,
							et.icon
					FROM #__joomleague_eventtype AS et
					INNER JOIN #__joomleague_position_eventtype AS pet ON pet.eventtype_id=et.id					
					LEFT JOIN #__joomleague_match_event AS me ON et.id=me.event_type_id
					WHERE me.match_id='.(int)$this->matchid.'
					GROUP BY et.id
					ORDER BY pet.ordering ';
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	function getPlayground($pgid)
	{
		$this->playground =& $this->getTable('Playground','Table');
		$this->playground->load($pgid);

		return $this->playground;
	}

	/**
	 * get match statistics as an array (projectteam_id => teamplayer_id => statistic_id)
	 * @return array
	 */
	function getMatchStats()
	{
		$match=&$this->getMatch();
		$query=' SELECT * FROM #__joomleague_match_statistic '
		      .' WHERE match_id='. $this->_db->Quote($match->id);
		$this->_db->setQuery($query);
		$res=$this->_db->loadObjectList();

		$stats = array(	$match->projectteam1_id => array(),
						$match->projectteam2_id => array());
		if(count($stats)>0 && count($res)>0) {
			foreach ($res as $stat)
			{
				@$stats[$stat->projectteam_id][$stat->teamplayer_id][$stat->statistic_id]=$stat->value;
			}
		}
		return $stats;
	}

	/**
	 * get match statistics as array(teamplayer_id => array(statistic_id => value))
	 * @return array
	 */
	function getPlayersStats()
	{
		if (!($this->_playersbasicstats))
		{
			$match=&$this->getMatch();

			$query=' SELECT * FROM #__joomleague_match_statistic '
			      .' WHERE match_id='. $this->_db->Quote($match->id);
			$this->_db->setQuery($query);
			$res=$this->_db->loadObjectList();

			$stats=array();
			if (count($res))
			{
				foreach ($res as $stat)
				{
					@$stats[$stat->teamplayer_id][$stat->statistic_id]=$stat->value;
				}
			}
			$this->_playersbasicstats=$stats;
		}

		return $this->_playersbasicstats;
	}

	/**
	 * get match statistics as array(teamplayer_id => array(event_type_id => value))
	 * @return array
	 */
	function getPlayersEvents()
	{
		if (!($this->_playersevents))
		{
			$match=&$this->getMatch();

			$query=' SELECT * FROM #__joomleague_match_event '
			      .' WHERE match_id='. $this->_db->Quote($match->id);
			$this->_db->setQuery($query);
			$res=$this->_db->loadObjectList();

			$events=array();
			if (count($res))
			{
				foreach ($res as $event)
				{
					@$events[$event->teamplayer_id][$event->event_type_id] += $event->event_sum;
				}
			}
			$this->_playersevents=$events;
		}

		return $this->_playersevents;
	}

	/**
	 * get match statistics as an array (team_staff_id => statistic_id)
	 * @return array
	 */
	function getMatchStaffStats()
	{
		if (!($this->_staffsbasicstats))
		{
			$match=&$this->getMatch();

			$query=' SELECT * FROM #__joomleague_match_staff_statistic '
			      .' WHERE match_id='. $this->_db->Quote($match->id);
			$this->_db->setQuery($query);
			$res=$this->_db->loadObjectList();

			$stats=array();
			if (count($res))
			{
				foreach ($res as $stat)
				{
					@$stats[$stat->team_staff_id][$stat->statistic_id]=$stat->value;
				}
			}
			$this->_staffsbasicstats=$stats;
		}
		return $this->_staffsbasicstats;
	}

	function getMatchText($match_id)
	{
		$query="SELECT	m.*,
						t1.name t1name,
						t2.name t2name
				FROM #__joomleague_match AS m
				INNER JOIN #__joomleague_project_team AS pt1 ON m.projectteam1_id=pt1.id
				INNER JOIN #__joomleague_project_team AS pt2 ON m.projectteam2_id=pt2.id
				INNER JOIN #__joomleague_team AS t1 ON pt1.team_id=t1.id
				INNER JOIN #__joomleague_team AS t2 ON pt2.team_id=t2.id
				WHERE m.id=".$match_id."
				AND m.published=1
				ORDER BY m.match_date,t1.short_name";
		$this->_db->setQuery($query);
		return $this->_db->loadObject();
	}

}
?>
