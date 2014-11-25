<?php

/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access'); 
$document = JFactory::getDocument();
$version =  new JVersion();
if ($version->isCompatible('3.0')) {

} else {
    $css=JURI::base().'components/com_eventgallery/media/css/legacy.css';
    $document->addStyleSheet($css);
}
?>

<p>
    <?php echo JText::_('COM_EVENTGALLERY_SYNC_START_DESC'); ?>
</p>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<input type="submit" class="btn btn-primary" value="<?php echo JText::_('COM_EVENTGALLERY_SYNC_START');?>" />
<input type="hidden" name="option" value="com_eventgallery" />
<input type="hidden" name="task" value="sync.start" />
<?php echo JHtml::_('form.token'); ?>
</form>
