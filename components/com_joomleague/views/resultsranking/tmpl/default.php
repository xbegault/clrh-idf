<?php defined('_JEXEC') or die('Restricted access');

// Make sure that in case extensions are written for mentioned (common) views,
// that they are loaded i.s.o. of the template of this view
$templatesToLoad = array('projectheading', 'backbutton', 'footer', 'results', 'ranking');
JoomleagueHelper::addTemplatePaths($templatesToLoad, $this);
?>
<div class="joomleague">
	<a name="jl_top" id="jl_top"></a>
	<?php 
	if ($this->config['show_sectionheader'])
	{
		echo $this->loadTemplate('sectionheader');
	}
		
	echo $this->loadTemplate('projectheading');

	if ($this->config['show_matchday_dropdown'])
	{
		echo $this->loadTemplate('selectround');
	}

	$results = '';
	if ($this->config['show_sectionheader'])
	{
		$results .= $this->loadTemplate('sectionheaderres');
	}
	$results .= $this->loadTemplate('results');
		
	if ($this->params->get('what_to_show_first', 0) == 0)
	{
		echo $results;
	}

	if ($this->config['show_ranking']==1)
	{
		if ($this->config['show_sectionheader'])
		{
			echo $this->loadTemplate('sectionheaderrank');
		}

		if ($this->config['use_tabbed_view']==1)
		{
			$i = 1;
			echo JHtml::_('tabs.start','tabs_ranking', array('useCookie'=>1));
			echo JHtml::_('tabs.panel', JText::_('COM_JOOMLEAGUE_RANKING_FULL_RANKING'), 'panel'.($i++));
			echo $this->loadTemplate('ranking');

			echo JHtml::_('tabs.panel', JText::_('COM_JOOMLEAGUE_RANKING_HOME_RANKING'), 'panel'.($i++));
			$this->assignRef('currentRanking',    	$this->homeRanking);
			echo $this->loadTemplate('ranking');

			echo JHtml::_('tabs.panel', JText::_('COM_JOOMLEAGUE_RANKING_AWAY_RANKING'), 'panel'.($i++));
			$this->assignRef('currentRanking',    	$this->awayRanking);
			echo $this->loadTemplate('ranking');
			echo JHtml::_('tabs.end');
		}
		else
		{
			echo $this->loadTemplate('ranking');
		}

		if ($this->config['show_colorlegend'])
		{
			echo $this->loadTemplate('colorlegend');
		}
		
		if ($this->config['show_explanation']==1)
		{
			echo $this->loadTemplate('explanation');
		}
	}

	if ($this->params->get('what_to_show_first', 0) == 1)
	{
		echo '<br />'.$results;
	}
		
	if ($this->config['show_pagnav']==1)
	{
		echo $this->loadTemplate('pagnav');
	}

	echo "<div>";
		echo $this->loadTemplate('backbutton');
		echo $this->loadTemplate('footer');
	echo "</div>";
	?>
</div>
