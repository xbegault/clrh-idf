<?php
/**
* @copyright	Copyright (C) 2005-2014 joomleague.at. All rights reserved.
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

/**
 * Joomleague Component DatabaseTool Controller
 *
 * @author	Kurt Norgaz
 * @package	JoomLeague
 * @since	1.5.0a
 */
class JoomleagueControllerDatabaseTool extends JoomleagueController
{

	public function __construct()
	{
		parent::__construct();

		$this->registerTask('repair','repair');
		$this->registerTask('optimize','optimize');
	}

	public function display($cachable = false, $urlparams = false)
	{
		parent::display();
	}

	public function optimize()
	{
		$model=$this->getModel('databasetools');
		if ($model->optimize())
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_DBTOOL_CTRL_OPTIMIZE');
		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_DBTOOL_CTRL_ERROR_OPTIMIZE').$model->getError();
		}
		$link='index.php?option=com_joomleague&view=databasetools';
		$this->setRedirect($link,$msg);
	}

	public function repair()
	{
		$model=$this->getModel('databasetools');
		if ($model->repair())
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_DBTOOL_CTRL_REPAIR');
		}
		else
		{
			$msg=JText::_('COM_JOOMLEAGUE_ADMIN_DBTOOL_CTRL_ERROR_REPAIR').$model->getError();
		}
		$link='index.php?option=com_joomleague&view=databasetools';
		$this->setRedirect($link,$msg);
	}

}
?>