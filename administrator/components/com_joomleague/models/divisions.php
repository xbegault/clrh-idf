<?php
/**
 * @copyright	Copyright (C) 2006-2014 joomleague.at. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );
require_once ( JLG_PATH_ADMIN.DS. 'models' . DS . 'list.php' );

/**
 * Joomleague Component Divisions Model
 *
 * @package		Joomleague
 * @since 0.1
 */
class JoomleagueModelDivisions extends JoomleagueModelList
{
	var $_identifier = "divisions";
	
	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query
		$where		= $this->_buildContentWhere();
		$orderby	= $this->_buildContentOrderBy();

		$query = '	SELECT	dv.*,
							dvp.name AS parent_name,
							u.name AS editor
					FROM #__joomleague_division AS dv
					LEFT JOIN #__joomleague_division AS dvp ON dvp.id = dv.parent_id
					LEFT JOIN #__users AS u ON u.id = dv.checked_out ' .
					$where . $orderby;

		return $query;
	}

	function _buildContentOrderBy()
	{
		$option = JRequest::getCmd('option');

		$mainframe	= JFactory::getApplication();
		$filter_order		= $mainframe->getUserStateFromRequest( $option . 'dv_filter_order',		'filter_order',		'dv.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option . 'dv_filter_order_Dir',	'filter_order_Dir',	'',				'word' );

		if ( $filter_order == 'dv.ordering' )
		{
			$orderby 	= ' ORDER BY dv.ordering ' . $filter_order_Dir;
		}
		else
		{
			$orderby 	= ' ORDER BY ' . $filter_order . ' '.$filter_order_Dir . ' , dv.ordering ';
		}

		return $orderby;
	}

	function _buildContentWhere()
	{
		$option = JRequest::getCmd('option');

 		$mainframe	= JFactory::getApplication();
		$project_id = $mainframe->getUserState( $option . 'project' );
		$where = array();

		$where[]	= ' dv.project_id = ' . $project_id;

		$filter_order		= $mainframe->getUserStateFromRequest( $option . 'dv_filter_order',		'filter_order',		'dv.ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option . 'dv_filter_order_Dir',	'filter_order_Dir',	'',				'word' );
		$search				= $mainframe->getUserStateFromRequest( $option . 'dv_search',			'search',			'',				'string' );
		$search				= JString::strtolower( $search );

		if ( $search )
		{
			$where[] = 'LOWER(dv.name) LIKE ' . $this->_db->Quote( '%' . $search . '%' );
		}


		$where = ( count( $where ) ? ' WHERE ' . implode( ' AND ', $where ) : '' );

		return $where;
	}
	
	/**
	* Method to return a divisions array (id, name)
	*
	* @param int $project_id
	* @access  public
	* @return  array
	* @since 0.1
	*/
	function getDivisions($project_id)
	{
		$query = '	SELECT	id AS value,
					name AS text
					FROM #__joomleague_division
					WHERE project_id=' . $project_id .
					' ORDER BY name ASC ';

		$this->_db->setQuery( $query );
		if ( !$result = $this->_db->loadObjectList("value") )
		{
			$this->setError( $this->_db->getErrorMsg() );
			return array();
		}
		else
		{
			return $result;
		}
		
	}
	
	/**
	 * return count of project divisions
	 *
	 * @param int project_id
	 * @return int
	 */
	function getProjectDivisionsCount($project_id)
	{
		$query='SELECT count(*) AS count
		FROM #__joomleague_division AS d
		JOIN #__joomleague_project AS p on p.id = d.project_id
		WHERE p.id='.$project_id;
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
	
}
?>