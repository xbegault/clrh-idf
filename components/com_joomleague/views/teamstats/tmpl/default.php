<?php defined( '_JEXEC' ) or die( 'Restricted access' ); 

// Set display style dependent on what stat parts should be shown
$show_general = 'none';
if ( $this->config['show_general_stats'] == 1 )
{
	$show_general = 'inline';
}

$show_goals = 'none';
if ( $this->config['show_goals_stats'] == 1 )
{
	$show_goals = 'inline';
}

$show_attendance = 'none';
if ( $this->config['show_attendance_stats'] == 1 )
{
	$show_attendance = 'inline';
}

$show_flash = '';
if ( $this->config['show_goals_stats_flash'] == 0 )
{
	$show_flash = 'display:none;';
}

//$show_att_ranking	= '';
//if ( $this->config['show_attendance_ranking'] == 0 )
//{
//	$show_att_ranking = 'display:none;';
//}

//$show_events = '';
//if ( $this->config['show_events_stats'] == 0 )
//{
//	$show_events  = 'display:none;';
//}

// Make sure that in case extensions are written for mentioned (common) views,
// that they are loaded i.s.o. of the template of this view
$templatesToLoad = array('projectheading', 'backbutton', 'footer');
JoomleagueHelper::addTemplatePaths($templatesToLoad, $this);
?>
<div class="joomleague">
	<?php 
	if ($this->config['show_sectionheader'])
	{
		echo $this->loadTemplate('sectionheader'); 
	}

	echo $this->loadTemplate('projectheading'); 

	if ($this->config['show_general_stats'])
	{
		echo $this->loadTemplate('stats'); 
	}

	if ($this->config['show_attendance_stats'])
	{
		echo $this->loadTemplate('attendance_stats'); 
	}	

	echo '<div style="width:100%; float:left">';
	if ( $this->config['show_goals_stats_flash'] )
	{
		$document= JFactory::getDocument();
		$version = urlencode(JoomleagueHelper::getVersion());
		$document->addScript( JUri::base(true).'/components/com_joomleague/assets/js/json2.js?v='.$version);
		$document->addScript( JUri::base(true).'/components/com_joomleague/assets/js/swfobject.js?v='.$version);
		echo $this->loadTemplate('flashchart'); 
	}
	echo '</div>';

	echo "<div>";
		echo $this->loadTemplate('backbutton');
		echo $this->loadTemplate('footer');
	echo "</div>";
	?>
</div>
