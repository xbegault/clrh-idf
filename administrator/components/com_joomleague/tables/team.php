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
jimport('joomla.filter.input');

/**
* Season Table class
*
* @package		Joomleague
* @since 0.1
*/
class TableTeam extends JLTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	public function __construct(& $db) {
		parent::__construct('#__joomleague_team', 'id', $db);
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 * @since 1.0
	 */
	public function check()
	{
		if (empty($this->name)) {
			$this->setError(JText::_('NAME REQUIRED'));
			return false;
		}
		
		// add default middle size name
		if (empty($this->middle_name)) {
			$parts = explode(" ", $this->name);
			$this->middle_name = substr($parts[0], 0, 20);
		}
	
		// add default short size name
		if (empty($this->short_name)) {
			$parts = explode(" ", $this->name);
			$this->short_name = substr($parts[0], 0, 2);
		}
	
		// setting alias
		if ( empty( $this->alias ) )
		{
			$this->alias = JFilterOutput::stringURLSafe( $this->name );
		}
		else {
			$this->alias = JFilterOutput::stringURLSafe( $this->alias ); // make sure the user didn't modify it to something illegal...
		}
		
		return true;
	}
	
	/**
	 * Redefined asset name, as we support action control
	 */
	protected function _getAssetName() {
		$k = $this->_tbl_key;
		return 'com_joomleague.team.'.(int) $this->$k;
	}
}
?>
