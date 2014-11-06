<?php defined( '_JEXEC' ) or die( 'Restricted access' );


jimport('joomla.html.pane');
?>

<!-- START: game stats -->
<?php
if (!empty($this->matchplayerpositions ))
{
	$hasMatchPlayerStats = false;
	$hasMatchStaffStats = false;
	foreach ( $this->matchplayerpositions as $pos )
	{
		if(isset($this->stats[$pos->position_id]) && count($this->stats[$pos->position_id])>0) {
			foreach ($this->stats[$pos->position_id] as $stat) {
				if ($stat->showInSingleMatchReports() && $stat->showInMatchReport()) {
					$hasMatchPlayerStats = true;
					break;
				}
			}
		}
	}
	foreach ( $this->matchstaffpositions as $pos )
	{
		if(isset($this->stats[$pos->position_id]) && count($this->stats[$pos->position_id])>0) {
			foreach ($this->stats[$pos->position_id] as $stat) {
				if ($stat->showInSingleMatchReports() && $stat->showInMatchReport()) {
					$hasMatchStaffStats = true;
				}
			}
		}
	}
	if($hasMatchPlayerStats || $hasMatchStaffStats) :
	?>

	<h2><?php echo JText::_('COM_JOOMLEAGUE_MATCHREPORT_STATISTICS'); ?></h2>

		<?php
		$iPanel = 1;
		echo JHtml::_('tabs.start','tabs', array('useCookie'=>1));
		echo JHtml::_('tabs.panel', $this->team1->name, 'panel'.$iPanel++);
		echo $this->loadTemplate('stats_home');
		echo JHtml::_('tabs.panel', $this->team2->name, 'panel'.$iPanel++);
		echo $this->loadTemplate('stats_away');
		echo JHtml::_('tabs.end');

	endif;
}
?>
<!-- END of game stats -->
