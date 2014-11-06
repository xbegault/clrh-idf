<?php defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
require_once(JPATH_COMPONENT . DS . 'models' . DS . 'results.php');
class JoomleagueViewNextMatch extends JLGView
{
	function display($tpl = null)
	{
		// Get a reference of the page instance in joomla
		$document= JFactory::getDocument();

		$model = $this->getModel();
		$match = $model->getMatch();

		$config = $model->getTemplateConfig($this->getName());
		$tableconfig = $model->getTemplateConfig( "ranking" );

		$this->assignRef( 'project',			$model->getProject() );
		$this->assignRef( 'config',			$config );
		$this->assignRef( 'tableconfig',		$tableconfig );
		$this->assignRef( 'overallconfig',		$model->getOverallConfig() );
		if ( !isset( $this->overallconfig['seperator'] ) )
		{
			$this->overallconfig['seperator'] = ":";
		}
		$this->assignRef( 'match',			$match);

		if ($match)
		{
			$newmatchtext = "";
			if($match->new_match_id > 0)
			{
				$ret = $model->getMatchText($match->new_match_id);
				$matchDate = JoomleagueHelper::getMatchDate($ret, JText::_('COM_JOOMLEAGUE_NEXTMATCH_NEXT_MATCHDATE'));
				$matchTime = JoomleagueHelperHtml::showMatchTime($ret, $this->config, $this->overallconfig, $this->project);
				$newmatchtext = $matchDate . " " . $matchTime . ", " . $ret->t1name . " - " . $ret->t2name;
			}
			$this->assignRef( 'newmatchtext',	$newmatchtext);
			$prevmatchtext = "";
			if($match->old_match_id > 0)
			{
				$ret = $model->getMatchText($match->old_match_id);
				$matchDate = JoomleagueHelper::getMatchDate($ret, JText::_('COM_JOOMLEAGUE_NEXTMATCH_PREVIOUS_MATCHDATE'));
				$matchTime = JoomleagueHelperHtml::showMatchTime($ret, $this->config, $this->overallconfig, $this->project);
				$prevmatchtext = $matchDate . " " . $matchTime . ", " . $ret->t1name . " - " . $ret->t2name;
			}
			$this->assignRef( 'oldmatchtext',	$prevmatchtext);

			$this->assignRef( 'teams', 		$model->getMatchTeams() );

			$this->assignRef( 'referees', 	$model->getReferees() );
			$this->assignRef( 'playground',	$model->getPlayground( $this->match->playground_id ) );

			$this->assignRef( 'homeranked',	$model->getHomeRanked() );		
			$this->assignRef( 'awayranked',	$model->getAwayRanked() );
			$this->assignRef( 'chances', 	$model->getChances() );		

			$this->assignRef( 'home_highest_home_win',	$model->getHomeHighestHomeWin() );
			$this->assignRef( 'away_highest_home_win',	$model->getAwayHighestHomeWin() );
			$this->assignRef( 'home_highest_home_def',	$model->getHomeHighestHomeDef() );
			$this->assignRef( 'away_highest_home_def',	$model->getAwayHighestHomeDef() );
			$this->assignRef( 'home_highest_away_win',	$model->getHomeHighestAwayWin() );
			$this->assignRef( 'away_highest_away_win',	$model->getAwayHighestAwayWin() );
			$this->assignRef( 'home_highest_away_def',	$model->getHomeHighestAwayDef() );
			$this->assignRef( 'away_highest_away_def',	$model->getAwayHighestAwayDef() );

			$games = $model->getGames();
			$gamesteams = $model->getTeamsFromMatches( $games );
			$this->assignRef( 'games', $games );
			$this->assignRef( 'gamesteams', $gamesteams );
			
			
			$previousx = &$this->get('previousx');
			$teams = &$this->get('TeamsIndexedByPtid');
			
			$this->assignRef('previousx', $previousx);
			$this->assignRef('allteams',  $teams);
		}
		
		// Set page title
		$titleInfo = JoomleagueHelper::createTitleInfo(JText::_('COM_JOOMLEAGUE_NEXTMATCH_PAGE_TITLE'));
		if (count($this->teams) == 2)
		{
			if (!empty($this->teams[0]))
			{
				$titleInfo->team1Name = $this->teams[0]->name;
			}
			if (!empty($this->teams[1]))
			{
				$titleInfo->team2Name = $this->teams[1]->name;
			}
		}
		if (!empty($this->project))
		{
			$titleInfo->projectName = $this->project->name;
			$titleInfo->leagueName = $this->project->league_name;
			$titleInfo->seasonName = $this->project->season_name;
		}
		$division = $model->getDivision(JRequest::getInt('division',0));
		if (!empty( $division ) && $division->id != 0)
		{
			$titleInfo->divisionName = $division->name;
		}
		$this->assignRef('pagetitle', JoomleagueHelper::formatTitle($titleInfo, $this->config["page_title_format"]));
		$document->setTitle($this->pagetitle);
		
		parent::display( $tpl );
	}
}
?>