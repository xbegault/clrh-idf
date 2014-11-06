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
require_once (JPATH_COMPONENT.DS.'models'.DS.'item.php');

/**
 * Joomleague Component Season Model
 *
 * @author	Julien Vonthron <julien.vonthron@gmail.com>
 * @package	JoomLeague
 * @since	0.1
 */
class JoomleagueModelSeason extends JoomleagueModelItem
{
	/**
	 * Method to remove a season
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	0.1
	 */
	function delete(&$pks=array())
	{
		if (count($pks))
		{
			$cids=implode(',',$pks);
			$query="SELECT id FROM #__joomleague_project WHERE season_id IN ($cids)";
			$this->_db->setQuery($query);
			if ($this->_db->loadResult())
			{
				$this->setError(JText::_('COM_JOOMLEAGUE_ADMIN_SEASON_MODEL_ERROR_PROJECT_EXISTS'));
				return false;
			}
			return parent::delete($pks);
		}
		return true;
	}

	/**
	 * Method to load content season data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$this->_data = parent::getItem($this->_id);
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the season data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$season						= new stdClass();
			$season->id					= 0;
			$season->name				= null;
			$season->alias				= null;
			$season->published			= 0;
			$season->checked_out		= 0;
			$season->checked_out_time	= 0;
			$season->extended			= null;
			$season->ordering			= 0;
			$season->modified			= null;
			$season->modified_by		= null;
			
			$this->_data				= $season;

			return (boolean) $this->_data;
		}

		return true;
	}
	
	/**
	 * Method to add a season if not already exists
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 **/
	function addSeason($newSeasonName)
	{
		$tblSeason = $this->getTable();
		$tblSeason->load(array('name'=>$newSeasonName));
		$tblSeason->name = $newSeasonName;
		$tblSeason->published = 1;
		$tblSeason->store();
		return $tblSeason->id;
	}
	
	
	/**
	 * Returns a Table object, always creating it
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'season', $prefix = 'table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.7
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_joomleague.'.$this->name, $this->name,
				array('load_data' => $loadData) );
		if (empty($form))
		{
			return false;
		}
		return $form;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.7
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_joomleague.edit.'.$this->name.'.data', array());
		if (empty($data))
		{
			$data = $this->getData();
		}
		return $data;
	}
}
?>