<?php

/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Supports a modal folder picker.
 *
 */
class JFormFieldModal_Folder extends  JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Modal_Folder';

	protected function getOptions()
	{
		// Initialize variables.

		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$query->select('folder As value, concat(description,\' - \',folder) As text');
		$query->from('#__eventgallery_folder AS a');
		$query->order('a.folder');

		// Get the options.
		$db->setQuery($query);

		$options = $db->loadObjectList();

		// Check for a database error.
		if ($db->getErrorNum()) {
			JError::raiseWarning(500, $db->getErrorMsg());
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}