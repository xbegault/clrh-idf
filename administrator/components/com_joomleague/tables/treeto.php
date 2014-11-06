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
* Treeto class
*
* @package		Joomleague
* @since 0.1
*/
class TableTreeto extends JLTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	public function __construct(& $db) {
		parent::__construct('#__joomleague_treeto', 'id', $db);
	}

	public function bind( $array, $ignore = '' )
	{
		if ( key_exists( 'params', $array ) && is_array( $array['params'] ) )
		{
			$registry = new JRegistry();
			$registry->loadArray( $array['params'] );
			$array['params'] = $registry->toString();
		}
		if ( key_exists( 'comp_params', $array ) && is_array( $array['comp_params'] ) )
		{
			$registry = new JRegistry();
			$registry->loadArray( $array['comp_params'] );
			$array['comp_params'] = $registry->toString();
		}
    	//print_r( $array );exit;
		return parent::bind( $array, $ignore );
	}
}
?>
