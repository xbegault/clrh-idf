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
require_once (JPATH_COMPONENT.DS.'models'.DS.'list.php');

/**
 * Joomleague Component Venues Model
 *
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueModelPlaygrounds extends JoomleagueModelList
{
	var $_identifier = "playgrounds";
	
	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where=$this->_buildContentWhere();
		$orderby=$this->_buildContentOrderBy();
		$query='	SELECT v.*,c.name As club,u.name AS editor
					FROM #__joomleague_playground AS v
					LEFT JOIN #__joomleague_club AS c ON c.id=v.club_id
					LEFT JOIN #__users AS u ON u.id=v.checked_out '
					. $where
					. $orderby;
		return $query;
	}

	function _buildContentOrderBy()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$filter_order		= $mainframe->getUserStateFromRequest($option.'v_filter_order','filter_order','v.ordering','cmd');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest($option.'v_filter_order_Dir','filter_order_Dir','','word');
		if ($filter_order == 'v.ordering')
		{
			$orderby=' ORDER BY v.ordering '.$filter_order_Dir;
		}
		else
		{
			$orderby=' ORDER BY '.$filter_order.' '.$filter_order_Dir.',v.ordering ';
		}
		return $orderby;
	}

	function _buildContentWhere()
	{
		$option = JRequest::getCmd('option');
		$mainframe = JFactory::getApplication();
		$filter_order		= $mainframe->getUserStateFromRequest($option.'v_filter_order',		'filter_order',		'v.ordering',	'cmd');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest($option.'v_filter_order_Dir',	'filter_order_Dir',	'',				'word');
		$search				= $mainframe->getUserStateFromRequest($option.'v_search',			'search',			'',				'string');
		$search_mode		= $mainframe->getUserStateFromRequest($option.'v_search_mode',		'search_mode',		'',				'string');
		$search=JString::strtolower($search);
		$where=array();
		if ($search)
		{
			if($search_mode)
			{
				$where[]='LOWER(v.name) LIKE '.$this->_db->Quote($search.'%');
			}
			else
			{
				$where[]='LOWER(v.name) LIKE '.$this->_db->Quote('%'.$search.'%');
			}
		}
		$where=(count($where) ? ' WHERE '. implode(' AND ',$where) : '');
		return $where;
	}

}
?>
