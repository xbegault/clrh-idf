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

jimport( 'joomla.application.component.view' );

/**
 * HTML View class for the Joomleague component
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.0a
 */
class JoomleagueViewJLXMLImports extends JLGView
{
	/**
	 * The list of available timezone groups to use.
	 *
	 * @var    array
	 *
	 * @since  11.1
	 */
	protected static $zones = array('Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific');
	
	function display($tpl=null)
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();

		if ($this->getLayout()=='form')
		{
			$this->_displayForm($tpl);
			return;
		}

		if ($this->getLayout()=='info')
		{
			$this->_displayInfo($tpl);
			return;
		}

		if ($this->getLayout()=='selectpage')
		{
			$this->_displaySelectpage($tpl);
			return;
		}

		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_TITLE_1_3'),'generic.png');
		JToolBarHelper::help('screen.joomleague',true);

		$uri = JFactory::getURI();
		$config = JComponentHelper::getParams('com_media');
		$post=JRequest::get('post');
		$files=JRequest::get('files');

		$this->request_url = $uri->toString();
		$this->config = $config;

		parent::display($tpl);
	}

	private function _displayForm($tpl)
	{
		$mtime			= microtime();
		$mtime 			= explode(" ",$mtime);
		$mtime			= $mtime[1] + $mtime[0];
		$starttime		= $mtime;
		$option			= 'com_joomleague';
		$mainframe		= JFactory::getApplication();
		$document		= JFactory::getDocument();
		$db				= JFactory::getDbo();
		$uri			= JFactory::getURI();
		$model			= JModelLegacy::getInstance('jlxmlimport', 'joomleaguemodel');
		$data			= $model->getData();
		$uploadArray	= $mainframe->getUserState($option.'uploadArray',array());
		$tzValue  		= isset($data['project']->timezone) ? $data['project']->timezone: null;
		$zones = DateTimeZone::listIdentifiers();				
		$options = array();
		$options[]	= JHTML::_('select.option', '', '- '.JText::_( 'SELECT_TIMEZONE' ).' -');
		foreach ($zones as $zone) {
			if (strpos($zone,"/")===false && strpos($zone,"UTC")===false)  continue;
			if (strpos($zone,"Etc")===0) continue;
			$options[]	= JHTML::_('select.option', $zone, $zone);
		}
		$lists['timezone']= JHtml::_('select.genericlist', $options, 'timezone', ' class="inputbox"', 'value', 'text', $tzValue);
		// build the html select booleanlist for published
		$publishedValue  		= isset($data['project']->published) ? $data['project']->published: null;
		$lists['published']=JHtml::_('select.booleanlist','published',' ',$publishedValue);
		
		$countries=new Countries();
		$this->assignRef('uploadArray',$uploadArray);
		$this->assignRef('starttime',$starttime);
		$this->assignRef('countries',$countries->getCountries());
		$this->assignRef('request_url',$uri->toString());
		$this->assignRef('xml', $data);
		$this->assignRef('leagues',$model->getLeagueList());
		$this->assignRef('seasons',$model->getSeasonList());
		$this->assignRef('sportstypes',$model->getSportsTypeList());
		$this->assignRef('admins',$model->getUserList(true));
		$this->assignRef('editors',$model->getUserList(false));
		$this->assignRef('templates',$model->getTemplateList());
		$this->assignRef('teams',$model->getTeamList());
		$this->assignRef('clubs',$model->getClubList());
		$this->assignRef('events',$model->getEventList());
		$this->assignRef('positions',$model->getPositionList());
		$this->assignRef('parentpositions',$model->getParentPositionList());
		$this->assignRef('playgrounds',$model->getPlaygroundList());
		$this->assignRef('persons',$model->getPersonList());
		$this->assignRef('statistics',$model->getStatisticList());
		$this->assignRef('OldCountries',$model->getCountryByOldid());
		$this->assignRef('import_version',$model->import_version);
		$this->assignRef('lists',$lists);
		
		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_TITLE_2_3'),'generic.png');
		//                       task    image  mouseover_img           alt_text_for_image              check_that_standard_list_item_is_checked
		JLToolBarHelper::custom('jlxmlimport.insert','upload','upload',Jtext::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_START_BUTTON'), false); // --> bij clicken op import wordt de insert view geactiveerd
		JToolBarHelper::back();
		JToolBarHelper::help('screen.joomleague',true);

		parent::display($tpl);
	}

	private function _displayInfo($tpl)
	{
		$mtime 		= microtime();
		$mtime		= explode(" ",$mtime);
		$mtime		= $mtime[1] + $mtime[0];
		$starttime	= $mtime;
		$model 		= JModelLegacy::getInstance('jlxmlimport', 'JoomleagueModel');
		$post		= JRequest::get('post');
		
		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_TITLE_3_3'),'generic.png');
		//JToolBarHelper::back();
		JToolBarHelper::help('screen.joomleague',true);

		$this->assignRef('starttime',$starttime);
		$this->assignRef('importData',$model->importData($post));
		$this->assignRef('postData',$post);

		parent::display($tpl);
	}

	private function _displaySelectpage($tpl)
	{
		$option = JRequest::getCmd('option');
		$mainframe 	= JFactory::getApplication();
		$document 	= JFactory::getDocument();
		$db 		= JFactory::getDbo();
		$uri 		= JFactory::getURI();
		$model 		= JModelLegacy::getInstance('JLXMLImport', 'JoomleagueModel');
		$lists 		= array();

		$this->assignRef('request_url',$uri->toString());
		$this->assignRef('selectType',$mainframe->getUserState($option.'selectType'));
		$this->assignRef('recordID',$mainframe->getUserState($option.'recordID'));

		switch ($this->selectType)
		{
			case '10':   { // Select new Club
						$this->assignRef('clubs',$model->getNewClubListSelect());
						$clublist=array();
						$clublist[]=JHtml::_('select.option',0,JText::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_SELECT_CLUB'));
						$clublist=array_merge($clublist,$this->clubs);
						$lists['clubs']=JHtml::_(	'select.genericlist',$clublist,'clubID','class="inputbox select-club" onchange="javascript:insertNewClub(\''.$this->recordID.'\')" ','value','text', 0);
						unset($clubteamlist);
						}
						break;
			case '9':   { // Select Club & Team
						$this->assignRef('clubsteams',$model->getClubAndTeamListSelect());
						$clubteamlist=array();
						$clubteamlist[]=JHtml::_('select.option',0,JText::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_SELECT_CLUB_AND_TEAM'));
						$clubteamlist=array_merge($clubteamlist,$this->clubsteams);
						$lists['clubsteams']=JHtml::_(	'select.genericlist',$clubteamlist,'teamID','class="inputbox select-team" onchange="javascript:insertClubAndTeam(\''.$this->recordID.'\')" ','value','text', 0);
						unset($clubteamlist);
						}
						break;
			case '8':	{ // Select Statistics
						$this->assignRef('statistics',$model->getStatisticListSelect());
						$statisticlist=array();
						$statisticlist[]=JHtml::_('select.option',0,JText::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_SELECT_STATISTIC'));
						$statisticlist=array_merge($statisticlist,$this->statistics);
						$lists['statistics']=JHtml::_('select.genericlist',$statisticlist,'statisticID','class="inputbox select-statistic" onchange="javascript:insertStatistic(\''.$this->recordID.'\')" ');
						unset($statisticlist);
						}
						break;

			case '7':	{ // Select ParentPosition
						$this->assignRef('parentpositions',$model->getParentPositionListSelect());
						$parentpositionlist=array();
						$parentpositionlist[]=JHtml::_('select.option',0,JText::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_SELECT_PARENT_POSITION'));
						$parentpositionlist=array_merge($parentpositionlist,$this->parentpositions);
						$lists['parentpositions']=JHtml::_('select.genericlist',$parentpositionlist,'parentPositionID','class="inputbox select-parentposition" onchange="javascript:insertParentPosition(\''.$this->recordID.'\')" ');
						unset($parentpositionlist);
						}
						break;

			case '6':	{ // Select Position
						$this->assignRef('positions',$model->getPositionListSelect());
						$positionlist=array();
						$positionlist[]=JHtml::_('select.option',0,JText::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_SELECT_POSITION'));
						$positionlist=array_merge($positionlist,$this->positions);
						$lists['positions']=JHtml::_('select.genericlist',$positionlist,'positionID','class="inputbox select-position" onchange="javascript:insertPosition(\''.$this->recordID.'\')" ');
						unset($positionlist);
						}
						break;

			case '5':	{ // Select Event
						$this->assignRef('events',$model->getEventListSelect());
						$eventlist=array();
						$eventlist[]=JHtml::_('select.option',0,JText::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_SELECT_EVENT'));
						$eventlist=array_merge($eventlist,$this->events);
						$lists['events']=JHtml::_('select.genericlist',$eventlist,'eventID','class="inputbox select-event" onchange="javascript:insertEvent(\''.$this->recordID.'\')" ');
						unset($eventlist);
						}
						break;

			case '4':	{ // Select Playground
						$this->assignRef('playgrounds',$model->getPlaygroundListSelect());
						$playgroundlist=array();
						$playgroundlist[]=JHtml::_('select.option',0,JText::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_SELECT_PLAYGROUND'));
						$playgroundlist=array_merge($playgroundlist,$this->playgrounds);
						$lists['playgrounds']=JHtml::_('select.genericlist',$playgroundlist,'playgroundID','class="inputbox select-playground" onchange="javascript:insertPlayground(\''.$this->recordID.'\')" ');
						unset($playgroundlist);
						}
						break;

			case '3':	{ // Select Person
						$this->assignRef('persons',$model->getPersonListSelect());
						$personlist=array();
						$personlist[]=JHtml::_('select.option',0,JText::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_SELECT_PERSON'));
						$personlist=array_merge($personlist,$this->persons);
						$lists['persons']=JHtml::_('select.genericlist',$personlist,'personID','class="inputbox select-person" onchange="javascript:insertPerson(\''.$this->recordID.'\')" ');
						unset($personlist);
						}
						break;

			case '2':	{ // Select Club
						$this->assignRef('clubs',$model->getClubListSelect());
						$clublist=array();
						$clublist[]=JHtml::_('select.option',0,JText::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_SELECT_CLUB'));
						$clublist=array_merge($clublist,$this->clubs);
						$lists['clubs']=JHtml::_('select.genericlist',$clublist,'clubID','class="inputbox select-club" onchange="javascript:insertClub(\''.$this->recordID.'\')" ');
						unset($clublist);
						}
						break;

			case '1':
			default:	{ // Select Team
						$this->assignRef('teams',$model->getTeamListSelect());
						$this->assignRef('clubs',$model->getClubListSelect());
						$teamlist=array();
						$teamlist[]=JHtml::_('select.option',0,JText::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_SELECT_TEAM'));
						$teamlist=array_merge($teamlist,$this->teams);
						$lists['teams']=JHtml::_('select.genericlist',$teamlist,'teamID','class="inputbox select-team" onchange="javascript:insertTeam(\''.$this->recordID.'\')" ','value','text',0);
						unset($teamlist);
						}
						break;
		}

		$this->assignRef('lists',$lists);
		// Set page title
		$pageTitle=JText::_('COM_JOOMLEAGUE_ADMIN_XML_IMPORT_ASSIGN_TITLE');
		$document->setTitle($pageTitle);

		parent::display($tpl);
	}

}
?>
