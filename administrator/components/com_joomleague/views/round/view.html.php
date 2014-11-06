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

jimport('joomla.application.component.view');

/**
 * HTML View class for the Joomleague component
 *
 * @author	Marco Vaninetti <martizva@tiscali.it>
 * @package	JoomLeague
 * @since	0.1
 */

class JoomleagueViewRound extends JLGView
{
	function display($tpl=null)
	{
		if ($this->getLayout() == 'form')
		{
			$this->_displayForm($tpl);
			return;
		}
		parent::display($tpl);
	}

	function _displayForm($tpl)
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDbo();
		$uri = JFactory::getURI();
		$user = JFactory::getUser();
		$model = $this->getModel();
		$lists=array();

		//get the matchday
		$round = $this->get('data');
		$isNew=($round->id < 1);

		// fail if checked out not by 'me'
		if ($model->isCheckedOut($user->get('id')))
		{
			$msg=JText::sprintf('DESCBEINGEDITTED',JText::_('The matchday'),$round->name);
			$mainframe->redirect('index.php?option='.$option,$msg);
		}

		// Edit or Create?
		if (!$isNew)
		{
			$model->checkout($user->get('id'));
		}
		else
		{
			// initialise new record
			$round->order=0;
		}

		$projectws = $this->get('Data','project');
		$this->assignRef('projectws',$projectws);
		 #$this->assignRef('lists',$lists);
		$this->assignRef('matchday',$round);

		$this->assignRef('form'      	, $this->get('form'));	
		//$extended = $this->getExtended($round->extended, 'round');
		//$this->assignRef( 'extended', $extended );		
		$this->addToolbar();		
		parent::display($tpl);
	}
	/**
	* Add the page title and toolbar.
	*
	* @since	1.6
	*/
	protected function addToolbar()
	{ 
		// Set toolbar items for the page
		$edit = JRequest::getVar('edit', true);
		$text = !$edit ? JText::_('COM_JOOMLEAGUE_GLOBAL_NEW') : JText::_('COM_JOOMLEAGUE_GLOBAL_EDIT');
		JToolBarHelper::title(JText::_('COM_JOOMLEAGUE_ADMIN_ROUND_TITLE'). ': ' . $this->matchday->name,'clubs','Matchdays');

		JLToolBarHelper::save('round.save');
		JLToolBarHelper::apply('round.apply');
		if (!$edit)
		{
			JLToolBarHelper::cancel('round.cancel');
		}
		else
		{
			// for existing items the button is renamed `close`
			JLToolBarHelper::cancel('round.cancel', 'COM_JOOMLEAGUE_GLOBAL_CLOSE');
		}
		JToolBarHelper::help('screen.joomleague', true);	
	}
}
?>