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
/**
 * @var JCacheControllerCallback $cache
 */
$cache = JFactory::getCache('com_eventgallery');
?>

<?php echo  $this->loadSnippet("cart"); ?>

<?php echo  $this->loadSnippet("events/" . $this->params->get('events_layout','default') ); ?>

<?php echo $this->loadSnippet('footer_disclaimer'); ?>