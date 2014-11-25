<?php
/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

$db = JFactory::getDbo();

$sql = $db->getQuery(true)
	->select($db->quoteName('name'))
	->from($db->quoteName('#__extensions'))
	->where($db->quoteName('type').' = '.$db->quote('package'))
	->where($db->quoteName('element').' = '.$db->quote('pkg_eventgallery_full'));
$db->setQuery($sql);
$result = $db->loadResult();

$isFull = $result!=null?true:false;


define('EVENTGALLERY_EXTENDED', $isFull);
define('EVENTGALLERY_VERSION', '3.2.1');
define('EVENTGALLERY_DATE', '05.10.2014');