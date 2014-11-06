<?php
/**
 * @copyright	Copyright(C) 2005-2014 joomleague.at. All rights reserved.
 * @license		GNU/GPL,see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License,and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.filesystem.file');

/**
 * Joomleague Component Event Controller
 *
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueControllerEventtype extends JoomleagueController
{
	protected $view_list = 'eventtypes';
	
	public function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('add','display');
		$this->registerTask('edit','display');
		$this->registerTask('apply','save');
	}

	public function display($cachable = false, $urlparams = false)
	{
		switch($this->getTask())
		{
			case 'add'	 :
			{
				JRequest::setVar('hidemainmenu',0);
				JRequest::setVar('layout','form');
				JRequest::setVar('view','eventtype');
				JRequest::setVar('edit',false);
				// Checkout the eventtype
				$model=$this->getModel('eventtype');
				$model->checkout();
			} break;
			case 'edit'	:
			{
				JRequest::setVar('hidemainmenu',0);
				JRequest::setVar('layout','form');
				JRequest::setVar('view','eventtype');
				JRequest::setVar('edit',true);
				// Checkout the eventtye
				$model=$this->getModel('eventtype');
				$model->checkout();
			} break;
		}
		parent::display();
	}

	public function save()
	{
		// Check for request forgeries
		JSession::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(0),'post','array');
		$post['id']=(int) $cid[0];
		$model=$this->getModel('eventtype');
		if($model->store($post))
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_EVENT_CTRL_SAVED');
		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_EVENT_CTRL_ERROR_SAVE').$model->getError();
		}
		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		if($this->getTask() == 'save')
		{
			$link='index.php?option=com_joomleague&view=eventtypes';
		}
		else
		{
			$link='index.php?option=com_joomleague&task=eventtype.edit&cid[]='.$post['id'];
		}
		$this->setRedirect($link,$msg);
	}

	public function remove()
	{
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		if(count($cid) < 1){JError::raiseError(500,JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_DELETE'));}
		$model=$this->getModel('eventtype');
		if(!$model->delete($cid))
		{
			$this->setRedirect('index.php?option=com_joomleague&view=eventtypes&task=eventtype.display',$model->getError(),'error');
			return;
		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_EVENT_CTRL_DELETED');
		}
		$this->setRedirect('index.php?option=com_joomleague&view=eventtypes&task=eventtype.display');
	}

	public function export()
	{
		JSession::checkToken() or die('COM_JOOMLEAGUE_GLOBAL_INVALID_TOKEN');
		$post=JRequest::get('post');
		$cid=JRequest::getVar('cid',array(),'post','array');
		JArrayHelper::toInteger($cid);
		if (count($cid) < 1){JError::raiseError(500,JText::_('COM_JOOMLEAGUE_GLOBAL_SELECT_TO_EXPORT'));}
		$model = $this->getModel("eventtype");
		$model->export($cid, "eventtype", "EventType");
	}

	public function cancel()
	{
		// Checkin the event
		$model=$this->getModel('eventtype');
		$model->checkin();
		$this->setRedirect('index.php?option=com_joomleague&view=eventtypes&task=eventtype.display');
	}

	public function import()
	{
		JRequest::setVar('view','import');
		JRequest::setVar('table','eventtype');
		parent::display();
	}
	
	/**
	 * Proxy for getModel
	 *
	 * @param	string	$name	The model name. Optional.
	 * @param	string	$prefix	The class prefix. Optional.
	 *
	 * @return	object	The model.
	 * @since	1.6
	 */
	public function getModel($name = 'Eventtype', $prefix = 'JoomleagueModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}
?>