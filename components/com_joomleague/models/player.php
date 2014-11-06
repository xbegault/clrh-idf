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
require_once('person.php');

class JoomleagueModelPlayer extends JoomleagueModelPerson
{
	/**
	 * data array for player history
	 * @var array
	 */
	var $_playerhistory =null;
	var $_teamplayers = null;

	function __construct()
	{
		parent::__construct();
		$this->projectid=JRequest::getInt('p',0);
		$this->personid=JRequest::getInt('pid',0);
		$this->teamplayerid=JRequest::getInt('pt',0);
	}

	// Get all teamplayers of the project where the person played in
	//  (in case the player was transferred team of the project within the season)
	function getTeamPlayers()
	{
		if (is_null($this->_teamplayers))
		{
			$query='	SELECT	tp.*,
								pt.project_id,
								pt.team_id,
								pos.name AS position_name,
								ppos.position_id,
								rinjuryfrom.round_date_first injury_date,
								rinjuryto.round_date_last injury_end,
								rinjuryfrom.name rinjury_from,
								rinjuryto.name rinjury_to,

								rsuspfrom.round_date_first suspension_date,
								rsuspto.round_date_last suspension_end,
								rsuspfrom.name rsusp_from,
								rsuspto.name rsusp_to,

								rawayfrom.round_date_first away_date,
								rawayto.round_date_last away_end,
								rawayfrom.name raway_from,
								rawayto.name raway_to

						FROM #__joomleague_team_player AS tp
						INNER JOIN #__joomleague_project_team AS pt ON pt.id=tp.projectteam_id
						INNER JOIN #__joomleague_project_position AS ppos ON ppos.id=tp.project_position_id
						INNER JOIN #__joomleague_project AS p ON p.id=pt.project_id
						LEFT JOIN #__joomleague_position AS pos ON pos.id=ppos.position_id
						LEFT JOIN #__joomleague_round AS rinjuryfrom ON tp.injury_date=rinjuryfrom.id
						LEFT JOIN #__joomleague_round AS rinjuryto ON tp.injury_end=rinjuryto.id
						LEFT JOIN #__joomleague_round AS rsuspfrom ON tp.suspension_date=rsuspfrom.id
						LEFT JOIN #__joomleague_round AS rsuspto ON tp.suspension_end=rsuspto.id
						LEFT JOIN #__joomleague_round AS rawayfrom ON tp.away_date=rawayfrom.id
						LEFT JOIN #__joomleague_round AS rawayto ON tp.away_end=rawayto.id
						WHERE pt.project_id='.$this->_db->Quote($this->projectid).'
						  AND tp.person_id='.$this->_db->Quote($this->personid).'
						  AND p.published = 1';
			$this->_db->setQuery($query);
			$this->_teamplayers = $this->_db->loadObjectList('projectteam_id');
		}
		return $this->_teamplayers;
	}

	/**
	 * return the injury,suspension,away data from a player
	 *
	 * @param int $round_id
	 * @param int $player_id
	 *
	 * @access public
	 * @since  1.5.0a
	 *
	 * @return object
	 */
	function getTeamPlayerByRound($round_id=0, $player_id=0)
	{
		if (is_null($this->_inproject))
		{		$query = '	SELECT	tp.*, tp.injury AS injury, pt.team_id,
							tp.suspension AS suspension,
							tp.away AS away,
							ppos.id As pposid,
							ppos.position_id,
							pos.id AS position_id,
							pos.name AS position_name,
							rinjuryfrom.round_date_first injury_date,
							rinjuryto.round_date_last injury_end,
							rinjuryfrom.name rinjury_from,
							rinjuryto.name rinjury_to,

							rsuspfrom.round_date_first suspension_date,
							rsuspto.round_date_last suspension_end,
							rsuspfrom.name rsusp_from,
							rsuspto.name rsusp_to,

							rawayfrom.round_date_first away_date,
							rawayto.round_date_last away_end,
							rawayfrom.name raway_from,
							rawayto.name raway_to

					FROM #__joomleague_team_player AS tp
					INNER JOIN #__joomleague_person AS pr ON tp.person_id=pr.id
					INNER JOIN #__joomleague_project_team AS pt ON pt.id=tp.projectteam_id
					INNER JOIN #__joomleague_round AS r ON r.project_id=pt.project_id
					INNER JOIN #__joomleague_project_position AS ppos ON ppos.id=tp.project_position_id
					INNER JOIN #__joomleague_position AS pos ON pos.id=ppos.position_id
					LEFT JOIN #__joomleague_round AS rinjuryfrom ON tp.injury_date=rinjuryfrom.id
					LEFT JOIN #__joomleague_round AS rinjuryto ON tp.injury_end=rinjuryto.id
					LEFT JOIN #__joomleague_round AS rsuspfrom ON tp.suspension_date=rsuspfrom.id
					LEFT JOIN #__joomleague_round AS rsuspto ON tp.suspension_end=rsuspto.id
					LEFT JOIN #__joomleague_round AS rawayfrom ON tp.away_date=rawayfrom.id
					LEFT JOIN #__joomleague_round AS rawayto ON tp.away_end=rawayto.id
					WHERE r.id='.$round_id.'
					  AND pr.id='.$player_id.'
					  AND pr.published = 1
					  AND tp.published = 1
					ORDER BY tp.id DESC
					  ';
			$this->_db->setQuery($query);
			$this->_inproject=$this->_db->loadObjectList();
		}
		return $this->_inproject;
	}

	// As a player can be transferred from one team to the next within a season, it is possible that
	function getTeamPlayer_old()
	{
		if (is_null($this->_inproject))
		{
			$query='	SELECT	tp.*,
								pt.project_id,
								pt.team_id,
								pos.name AS position_name,
								ppos.position_id,
								pt.notes AS ptnotes
						FROM #__joomleague_team_player AS tp
						INNER JOIN #__joomleague_project_team AS pt ON pt.id=tp.projectteam_id
						INNER JOIN #__joomleague_project_position AS ppos ON ppos.id=tp.project_position_id
						INNER JOIN #__joomleague_project AS p ON p.id=pt.project_id
						LEFT JOIN #__joomleague_position AS pos ON pos.id=ppos.position_id
						WHERE pt.project_id='.$this->_db->Quote($this->projectid).'
						  AND tp.person_id='.$this->_db->Quote($this->personid).'
						  AND p.published = 1';
			$this->_db->setQuery($query);
			$this->_inproject=$this->_db->loadObjectList();
		}
		return $this->_inproject;
	}

	function getTeamStaff()
	{
		if (is_null($this->_inproject))
		{
			$query='	SELECT	ts.*,
								pos.name AS position_name
								pt.notes AS ptnotes
						FROM #__joomleague_team_staff AS ts
						INNER JOIN #__joomleague_project_team AS pt ON pt.id=ts.projectteam_id
						INNER JOIN #__joomleague_project_position AS ppos ON ppos.id=ts.project_position_id
						INNER JOIN #__joomleague_project AS p ON p.id=pt.project_id
						LEFT JOIN #__joomleague_position AS pos ON pos.id=ppos.position_id
						WHERE pt.project_id='.$this->_db->Quote($this->projectid).'
						  AND ts.person_id='.$this->_db->Quote($this->personid).'
						  AND p.published = 1';
			$this->_db->setQuery($query);
			$this->_inproject=$this->_db->loadObject();
		}
		return $this->_inproject;
	}

	function getPositionEventTypes($positionId=0)
	{
		$result=array();
		// TODO: pj.id is not known in the query. Is this function called anywhere? If not, just remove it...
		$query='	SELECT	pet.*,
							pj.id AS ppID,
							et.name,
							et.icon
					FROM #__joomleague_position_eventtype AS pet
					INNER JOIN #__joomleague_eventtype AS et ON et.id=pet.eventtype_id
					INNER JOIN #__joomleague_match_event AS me ON et.id=me.event_type_id
					WHERE me.project_id='.$this->projectid;
		if ($positionId > 0)
		{
			$query .= ' AND pet.position_id='.(int)$positionId;
		}
		$query .= ' ORDER BY pet.ordering';
		$this->_db->setQuery($query);
		$result=$this->_db->loadObjectList();
		if ($result)
		{
			if ($positionId) {
				return $result;
			} else {
				$posEvents=array();
				foreach ($result as $r)
				{
					//$posEvents[$r->position_id][]=$r;
					$posEvents[$r->ppID][]=$r;
				}
				return ($posEvents);
			}
		}
		return array();
	}

	/**
	 * get person history across all projects,with team,season,position,... info
	 *
	 * @param int $person_id,linked to player_id from Person object
	 * @param int $order ordering for season and league,default is ASC ordering
	 * @param string $filter e.g. "s.name=2007/2008",default empty string
	 * @return array of objects
	 */
	function &getPlayerHistory($sportstype=0, $order='ASC')
	{
		if (empty($this->_playerhistory))
		{
			$personid=$this->personid;
			$query='	SELECT	pr.id AS pid,
								tp.person_id,
								tp.id AS tpid,
								pt.project_id,
								pr.firstname,
								pr.lastname,
								p.name AS project_name,
								s.name AS season_name,
								t.name AS team_name,
								pos.name AS position_name,
								tp.project_position_id,
								t.id AS team_id,
								pt.id AS ptid,
								pos.id AS posID,
								CASE WHEN CHAR_LENGTH(t.alias) THEN CONCAT_WS(\':\',t.id,t.alias) ELSE t.id END AS team_slug,
								CASE WHEN CHAR_LENGTH(p.alias) THEN CONCAT_WS(\':\',p.id,p.alias) ELSE p.id END AS project_slug
						FROM #__joomleague_person AS pr
						INNER JOIN #__joomleague_team_player AS tp ON tp.person_id=pr.id
						INNER JOIN #__joomleague_project_team AS pt ON pt.id=tp.projectteam_id
						INNER JOIN #__joomleague_team AS t ON t.id=pt.team_id
						INNER JOIN #__joomleague_project AS p ON p.id=pt.project_id
						INNER JOIN #__joomleague_season AS s ON s.id=p.season_id
						INNER JOIN #__joomleague_league AS l ON l.id=p.league_id
						INNER JOIN #__joomleague_project_position AS ppos ON ppos.id=tp.project_position_id
						INNER JOIN #__joomleague_position AS pos ON pos.id=ppos.position_id
						WHERE pr.id='.$this->_db->Quote($personid).'
						  AND p.published=1
						  AND tp.published=1
						  AND pr.published = 1';
			if ($sportstype > 0)
			{
				$query .= '	AND p.sports_type_id = '.$this->_db->Quote($sportstype);
			}
			$query .= '	ORDER BY s.ordering ' . $order .',l.ordering ASC,p.name ASC ';
			$this->_db->setQuery($query);
			$this->_playerhistory=$this->_db->loadObjectList();
		}
		return $this->_playerhistory;
	}

	// new insert for staff
	function &getPlayerHistoryStaff($sportstype=0, $order='ASC')
	{
		if (empty($this->_playerhistorystaff))
		{
			$personid=$this->personid;
			$query='	SELECT	pr.id AS pid,
								ts.person_id,
								pt.project_id,
								pr.firstname,
								pr.lastname,
								p.name AS project_name,
								s.name AS season_name,
								t.name AS team_name,
								pos.name AS position_name,
								ppos.position_id,
								t.id AS team_id,
								pt.id AS ptid,
								CASE WHEN CHAR_LENGTH(t.alias) THEN CONCAT_WS(\':\',t.id,t.alias) ELSE t.id END AS team_slug,
								CASE WHEN CHAR_LENGTH(p.alias) THEN CONCAT_WS(\':\',p.id,p.alias) ELSE p.id END AS project_slug
						FROM #__joomleague_person AS pr
						INNER JOIN #__joomleague_team_staff AS ts ON ts.person_id=pr.id
						INNER JOIN #__joomleague_project_team AS pt ON pt.id=ts.projectteam_id
						INNER JOIN #__joomleague_team AS t ON t.id=pt.team_id
						INNER JOIN #__joomleague_project AS p ON p.id=pt.project_id
						INNER JOIN #__joomleague_season AS s ON s.id=p.season_id
						INNER JOIN #__joomleague_league AS l ON l.id=p.league_id
						INNER JOIN #__joomleague_project_position AS ppos ON ppos.id=ts.project_position_id
						INNER JOIN #__joomleague_position AS pos ON pos.id=ppos.position_id
						WHERE pr.id='.$this->_db->Quote($personid).'
						  AND p.published=1
						  AND pr.published = 1';
			if ($sportstype > 0)
			{
				$query .= '	AND p.sports_type_id = '.$this->_db->Quote($sportstype);
			}
			$query .= '	ORDER BY s.ordering ' . $order .',l.ordering ASC,p.name ASC ';
			$this->_db->setQuery($query);
			$this->_playerhistorystaff=$this->_db->loadObjectList();
		}
		return $this->_playerhistorystaff;
	}

	function getRounds($roundcodestart,$roundcodeend)
	{
		$projectid=$this->projectid;
		$thisround=0;
		$query="	SELECT	id
					FROM #__joomleague_round
					WHERE project_id='".(int)$projectid."'
					AND roundcode>='".(int)$roundcodestart."'
					AND roundcode<='".(int)$roundcodeend."'
					ORDER BY round_date_first";
		$this->_db->setQuery($query);
		$rows=$this->_db->loadColumn();
		$rounds=array();
		if (count($rows) > 0)
		{
			$startround =& $this->getTable('Round','Table');
			$startround->load($rows[0]);
			$rounds[0]=$startround;
			$endround =& $this->getTable('Round','Table');
			$endround->load(end($rows));
			$rounds[1]=$endround;
		}
		return $rounds;
	}

	/**
	 * get all events associated to positions the player was assigned too in different projects
	 *
	 * @return array of event types row objects.
	 */
	function getAllEvents($sportstype=0)
	{
		$history=&$this->getPlayerHistory($sportstype);
		$positionhistory=array();
		foreach($history as $h)
		{
			if (!in_array($h->posID,$positionhistory) && $h->posID!=null)
			{
				$positionhistory[]=$h->posID;
			}
		}
		if (!count($positionhistory))
		{
			return array();
		}
		$query='	SELECT DISTINCT	et.*
					FROM #__joomleague_eventtype AS et
					INNER JOIN #__joomleague_position_eventtype AS pet ON pet.eventtype_id=et.id
					INNER JOIN #__joomleague_project_position AS ppos ON ppos.position_id=pet.position_id
					WHERE published=1
					  AND pet.position_id IN ('. implode(',',$positionhistory) .')
					ORDER BY pet.ordering ';
		$this->_db->setQuery($query);
		$info=$this->_db->loadObjectList();
		return $info;
	}

	function getInOutStats($project_id, $projectteam_id, $teamplayer_id)
	{
		$quotedTpId = $this->_db->Quote($teamplayer_id);
		$quotedPtId = $this->_db->Quote($projectteam_id);
		$query  = ' SELECT COUNT(m.id) AS played, SUM(ios.started) AS started, SUM(ios.sub_in) AS sub_in, SUM(ios.sub_out) AS sub_out'
				. ' FROM #__joomleague_match AS m'
				. ' INNER JOIN '
				. ' ('
				. '   SELECT mp.match_id,'
				. '          sum(mp.came_in=0 AND mp.teamplayer_id = '.$quotedTpId.') AS started,'
				. '          sum(mp.came_in=1 AND mp.teamplayer_id = '.$quotedTpId.') AS sub_in,'
				. '          sum((mp.teamplayer_id = '.$quotedTpId.' AND mp.out = 1) OR mp.in_for = '.$quotedTpId.') AS sub_out'
				. '   FROM #__joomleague_match_player AS mp'
				. '   INNER JOIN #__joomleague_team_player AS tp ON tp.id=mp.teamplayer_id'
				. '   INNER JOIN #__joomleague_match AS m ON m.id=mp.match_id'
				. '   INNER JOIN #__joomleague_round r ON r.id=m.round_id'
				. '   INNER JOIN #__joomleague_project AS p ON p.id=r.project_id'
				. '   INNER JOIN #__joomleague_project_team AS pt1 ON pt1.id=m.projectteam1_id'
				. '   INNER JOIN #__joomleague_team AS t1 ON t1.id=pt1.team_id'
				. '   INNER JOIN #__joomleague_project_team AS pt2 ON pt2.id=m.projectteam2_id'
				. '   INNER JOIN #__joomleague_team AS t2 ON t2.id=pt2.team_id'
				. '   WHERE ((mp.teamplayer_id = '.$quotedTpId.') OR (mp.in_for = '.$quotedTpId.'))'
				. '     AND p.id='.$this->_db->Quote($project_id)
				. '     AND (pt1.id='.$quotedPtId.' OR pt2.id='.$quotedPtId.')'
				. '     AND m.published = 1 AND p.published = 1'
				. '   GROUP BY mp.match_id'
				. ' ) AS ios ON ios.match_id=m.id';
		$this->_db->setQuery($query);
		$inoutstat = $this->_db->loadObject();

		return $inoutstat;
	}

	function getTimePlayed($player_id,$game_regular_time,$match_id = NULL)
	{
		$result = 0;
		// starting line up without subs in/out
		$query='SELECT count(match_id) as totalmatch
			      FROM #__joomleague_match_player
			      WHERE teamplayer_id = '.$player_id.' and came_in = 0';
		if ( $match_id )
		{
			$query .= ' and match_id = '.$match_id;
		}
		$this->_db->setQuery($query);
		$totalresult = $this->_db->loadObject();

		if ( $totalresult )
		{
			$result += $totalresult->totalmatch * $game_regular_time;
		}

		// subs in
		$query='SELECT count(match_id) as totalmatch, SUM(in_out_time) as totalin
			      FROM #__joomleague_match_player
			      WHERE teamplayer_id = '.$player_id.' and came_in = 1 and in_for IS NOT NULL';
		if ( $match_id )
		{
			$query .= ' and match_id = '.$match_id;
		}
		$this->_db->setQuery($query);
		$cameinresult = $this->_db->loadObject();

		if ( $cameinresult )
		{
			$result += ( $cameinresult->totalmatch * $game_regular_time ) - ( $cameinresult->totalin );
		}

		// subs out
		$query='SELECT count(match_id) as totalmatch, SUM(in_out_time) as totalout
			      FROM #__joomleague_match_player
			      WHERE in_for = '.$player_id.' and came_in = 1 ';
		if ( $match_id )
		{
			$query .= ' and match_id = '.$match_id;
		}
		$this->_db->setQuery($query);
		$cameoutresult = $this->_db->loadObject();

		if ( $cameoutresult )
		{
			$result += ( $cameoutresult->totalout ) - ( $cameoutresult->totalmatch * $game_regular_time );
		}

		// get all events which leads to a suspension (e.g. red card)
		$query = 'SELECT id
				    FROM #__joomleague_eventtype
				    WHERE suspension = 1';
		$this->_db->setQuery($query);
		$suspension_events = $this->_db->loadColumn();
		$suspension_events = implode(',',$suspension_events);

		// find matches where the player was suspended because of e.g. a red card
		if (!empty($suspension_events))
		{
			$query = 'SELECT *
					    FROM #__joomleague_match_event
					    WHERE teamplayer_id = '.$player_id;
			$query .= ' and event_type_id in ('.$suspension_events.')';
			if ( $match_id )
			{
			$query .= ' and match_id = '.$match_id;
			}
			$this->_db->setQuery($query);
			$cardsresult = $this->_db->loadObjectList();
			foreach ( $cardsresult as $row )
			{
			    $result -= ( $game_regular_time - $row->event_time );
			}
		}

		return $result;
	}

	/**
	 * get stats for the player position (the person can be player of multiple teams in the project due to transfer(s))
	 * @return array
	 */
	function getStats($current_round=0, $personid=0)
	{
		$stats = array();
		$players =& $this->getTeamPlayerByRound($current_round, $personid);
		if (is_array($players))
		{
			foreach ($players as $player)
			{
				// Remark: we cannot use array_merge because numerical keys will result in duplicate entries
				// so we check if a key already exists in the output array before adding it.
				$projectStats = $this->getProjectStats(0,$player->position_id);
				if (is_array($projectStats))
				{
					foreach ($projectStats as $key=>$projectStat)
					{
						if (!array_key_exists($key, $stats))
						{
							$stats[$key] = $projectStat;
						}
					}
				}
			}
		}
		return $stats;
	}

	/**
	 * get all statistics types that are in use for the player in his/her whole career
	 * @return array
	 */
	function getCareerStats($person_id, $sports_type_id)
	{
		if (empty($this->_careerStats))
		{
			$query = 'SELECT s.id,'
				. ' s.name,'
				. ' s.short,'
				. ' s.class,'
				. ' s.icon,'
				. ' s.calculated,'
				. ' ppos.id AS pposid,'
				. ' ppos.position_id AS position_id,'
				. ' s.params,'
				. ' s.baseparams'
				. ' FROM #__joomleague_person AS p'
				. ' INNER JOIN #__joomleague_team_player AS tp ON tp.person_id=p.id'
				. ' INNER JOIN #__joomleague_project_position AS ppos ON tp.project_position_id=ppos.id'
				. ' INNER JOIN #__joomleague_position AS pos ON ppos.position_id=pos.id'
				. ' INNER JOIN #__joomleague_position_statistic AS ps ON ps.position_id=pos.id'
				. ' INNER JOIN #__joomleague_statistic AS s ON ps.statistic_id=s.id'
				. ' WHERE p.id='.$this->_db->Quote($person_id);
			if ($sports_type_id > 0)
			{
				$query.= ' AND pos.sports_type_id='.$this->_db->Quote($sports_type_id);
			}
			$query.= ' GROUP BY s.id';

			$this->_db->setQuery($query);
			$this->_careerStats=$this->_db->loadObjectList();
		}
		$stats=array();
		if (count($this->_careerStats) > 0)
		{
			foreach ($this->_careerStats as $k => $row)
			{
				$stat=&JLGStatistic::getInstance($row->class);
				$stat->bind($row);
				$stat->set('position_id',$row->position_id);
				$stats[$row->id]=$stat;
			}
		}
		return $stats;
	}

	/**
	 * get player stats per game (one array entry per statistic ID)
	 * @return array
	 */
	function getPlayerStatsByGame()
	{
		$teamplayers=$this->getTeamPlayers();
		$displaystats=array();
		if (count($teamplayers))
		{
			$project =& $this->getProject();
			$project_id=$project->id;
			// Determine teamplayer id(s) of the player (plural if (s)he played in multiple teams of the project
			// and the position_id(s) where the player played
			$teamplayer_ids = array();
			$position_ids = array();
			foreach ($teamplayers as $teamplayer)
			{
				$teamplayer_ids[] = $teamplayer->id;
				if (!in_array($teamplayer->position_id, $position_ids))
				{
					$position_ids[] = $teamplayer->position_id;
				}
			}
			// For each position_id get the statistics types and merge the results (prevent duplicate statistics ids)
			// ($pos_stats is an array indexed by statistic_id)
			$pos_stats = array();
			foreach ($position_ids as $position_id)
			{
				$stats_for_position_id = $this->getProjectStats(0, $position_id);
				foreach ($stats_for_position_id as $id => $stat)
				{
					if (!array_key_exists($id, $pos_stats))
					{
						$pos_stats[$id] = $stat;
					}
				}
			}
			foreach ($pos_stats as $stat)
			{
				if(!empty($stat))
				{
					if ($stat->showInSingleMatchReports())
					{
						$stat->set('gamesstats',$stat->getPlayerStatsByGame($teamplayer_ids, $project_id));
						$displaystats[]=$stat;
					}
				}
			}
		}
		return $displaystats;
	}

	/**
	 * get player stats per project (one array entry per statistic ID)
	 * @return array
	 */
	function getPlayerStatsByProject($sportstype=0, $current_round=0, $playerid)
	{
		$teamplayer =& $this->getTeamPlayerByRound($current_round, $playerid);
		$result=array();
		if (is_array($teamplayer) && !empty($teamplayer))
		{
			// getTeamPlayer can return multiple teamplayers, because a player can be transferred from
			// one team to another inside a season, but they are all the same person so have same person_id.
			// So we get the player_id from the first array entry.
			$stats  =& $this->getCareerStats($teamplayer[0]->person_id, $sportstype);
			$history=& $this->getPlayerHistory($sportstype);
			if(count($history)>0)
			{
				foreach ($stats as $stat)
				{
					if(!empty($stat))
					{
						foreach ($history as $player)
						{
							$result[$stat->id][$player->project_id][$player->ptid]=$stat->getPlayerStatsByProject($player->person_id, $player->ptid, $player->project_id, $sportstype);
						}
						$result[$stat->id]['totals'] = $stat->getPlayerStatsByProject($player->person_id, 0, 0, $sportstype);
					}
				}
			}
		}
		return $result;
	}

	function getGames()
	{
		$teamplayers=$this->getTeamPlayers();
		$games=array();
		if (count($teamplayers))
		{
			$quoted_tpids=array();
			foreach ($teamplayers as $teamplayer)
			{
				$quoted_tpids[]=$this->_db->Quote($teamplayer->id);
			}
			$tpid_list = '('.implode(',', $quoted_tpids).')';

			$query  = ' SELECT m.*, ios.*'
					. ' FROM #__joomleague_match AS m'
					. ' INNER JOIN'
					. ' ('
					. ' 		SELECT mp.match_id, t1.id AS team1, t2.id AS team2, r.roundcode, r.project_id, tp.projectteam_id, p.timezone, '
					. ' 		sum(mp.came_in=0 AND mp.teamplayer_id IN ('.$tpid_list.')) AS started,'
					. ' 		sum(mp.came_in=1 AND mp.teamplayer_id IN ('.$tpid_list.')) AS sub_in,'
					. ' 		sum((mp.teamplayer_id IN ('.$tpid_list.') AND mp.out = 1) OR mp.in_for IN ('.$tpid_list.')) AS sub_out'
					. ' 		FROM #__joomleague_match_player AS mp'
					. ' 		INNER JOIN #__joomleague_team_player AS tp ON tp.id=mp.teamplayer_id'
					. ' 		INNER JOIN #__joomleague_match AS m ON m.id=mp.match_id'
					. ' 		INNER JOIN #__joomleague_round r ON r.id=m.round_id'
					. ' 		INNER JOIN #__joomleague_project AS p ON p.id=r.project_id'
					. ' 		INNER JOIN #__joomleague_project_team AS pt1 ON pt1.id=m.projectteam1_id'
					. ' 		INNER JOIN #__joomleague_team AS t1 ON t1.id=pt1.team_id'
					. ' 		INNER JOIN #__joomleague_project_team AS pt2 ON pt2.id=m.projectteam2_id'
					. ' 		INNER JOIN #__joomleague_team AS t2 ON t2.id=pt2.team_id'
					. ' 		WHERE ((mp.teamplayer_id IN ('.$tpid_list.')) OR (mp.in_for IN ('.$tpid_list.')))'
					. '           AND m.published = 1 AND p.published = 1'
					. ' 		GROUP BY mp.match_id, t1.id, t2.id, r.roundcode, r.project_id, tp.projectteam_id, p.timezone'
					. ' ) AS ios ON ios.match_id=m.id'
					. ' ORDER BY m.match_date';

			$this->_db->setQuery($query);
			$games =  $this->_db->loadObjectList();
			if ($games)
			{
				foreach ($games as $game)
				{
					JoomleagueHelper::convertMatchDateToTimezone($game);
				}
			}
		}
		return $games;
	}

	function getGamesEvents()
	{
		$teamplayers=$this->getTeamPlayers();
		$gameevents=array();
		if (count($teamplayers))
		{
			$quoted_tpids=array();
			foreach ($teamplayers as $teamplayer)
			{
				$quoted_tpids[]=$this->_db->Quote($teamplayer->id);
			}
			$query='SELECT	SUM(me.event_sum) as value,
					me.*,
					me.match_id
				FROM #__joomleague_match_event AS me
				WHERE me.teamplayer_id IN ('. implode(',', $quoted_tpids) .')
				GROUP BY me.match_id, me.event_type_id';
			$this->_db->setQuery($query);
			$events=$this->_db->loadObjectList();
			foreach ((array) $events as $ev)
			{
				if (isset($gameevents[$ev->match_id]))
				{
					$gameevents[$ev->match_id][$ev->event_type_id]=$ev->value;
				}
				else
				{
					$gameevents[$ev->match_id]=array($ev->event_type_id => $ev->value);
				}
			}
		}
		return $gameevents;
	}

}

?>