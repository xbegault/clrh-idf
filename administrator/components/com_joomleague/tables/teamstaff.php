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

// Include library dependencies
jimport( 'joomla.filter.input' );

/**
 * Joomleague TeamStaff Table class
 *
 * @package	JoomLeague
 * @since	1.50a
 */
class TableTeamStaff extends JLTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	public function __construct(& $db)
	{
		parent::__construct( '#__joomleague_team_staff', 'id', $db );
	}

	public function canDelete($id)
	{
		// the staff cannot be deleted if assigned to games
		$query = ' SELECT COUNT(id) FROM #__joomleague_match_staff '
		       . ' WHERE team_staff_id = '. $this->getDbo()->Quote($id)
		       . ' GROUP BY team_staff_id ';
		$this->getDbo()->setQuery($query, 0, 1);
		$res = $this->getDbo()->loadResult();
		
		if ($res) {
			$this->setError(Jtext::sprintf('STAFF ASSIGNED TO %d GAMES', $res));
			return false;
		}
		
		// the staff cannot be deleted if has stats
		$query = ' SELECT COUNT(id) FROM #__joomleague_match_staff_statistic '
		       . ' WHERE team_staff_id = '. $this->getDbo()->Quote($id)
		       . ' GROUP BY team_staff_id ';
		$this->getDbo()->setQuery($query, 0, 1);
		$res = $this->getDbo()->loadResult();
		
		if ($res) {
			$this->setError(JText::sprintf('%d STATS ASSIGNED TO STAFF', $res));
			return false;
		}
		
		return true;
	}

	/**
	 * Redefined asset name, as we support action control
	 */
	protected function _getAssetName() {
		$k = $this->_tbl_key;
		return 'com_joomleague.team_staff.'.(int) $this->$k;
	}
}
?>