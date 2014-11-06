<?php
/**
 * @copyright  Copyright (C) 2005-2014 joomleague.at. All rights reserved.
 * @license	GNU/GPL,see LICENSE.php
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

class JoomleagueViewMatch extends JLGView
{
	function display($tpl=null)
	{
		$result=JRequest::getVar('result');
		echo $result;
	}

	function _displaySaveSubst($tpl=null)
	{
		$result=JRequest::getVar('result');
		echo $result;
	}

}
?>