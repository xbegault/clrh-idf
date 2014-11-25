<?php 

/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');


JHtml::_('behavior.tooltip');
$document = JFactory::getDocument();
$version =  new JVersion();
if ($version->isCompatible('3.0')) {
    JHtml::_('formbehavior.chosen', 'select');    
} else {
    $css=JURI::base().'components/com_eventgallery/media/css/legacy.css';
    $document->addStyleSheet($css);
}

?>

<form action="<?php echo JRoute::_('index.php?option=com_eventgallery&view=watermarks'); ?>"
      method="post" name="adminForm" id="adminForm">

<?php if (!empty( $this->sidebar)) : ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
<?php else : ?>
    <div id="j-main-container">
<?php endif;?>
        <div id="filter-bar" class="btn-toolbar">
            <div class="btn-group pull-right hidden-phone">
                <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                <?php echo $this->pagination->getLimitBox(); ?>
            </div>
        </div>
        <div class="clearfix"> </div>
        <p>
            <?php echo JText::_('COM_EVENTGALLERY_WATERMARK_CACHE_REFRESH_HINT'); ?>
        </p>
        <table class="table">
            <thead>
                <tr>
                    <th width="20">
                        <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                    </th>
                    <th class="nowrap" width="1%">
                        
                    </th>
                    <th width="1%">
                        <?php echo JText::_( 'COM_EVENTGALLERY_WATERMARK_ORDER' ); ?> 
                        <?php echo JHTML::_('grid.order',  $this->items, 'filesave.png', 'watermarks.saveorder' ); ?>   
                    </th>   
                    <th>
                        <?php echo JText::_( 'COM_EVENTGALLERY_WATERMARK_NAME' ); ?>
                    </th>

                </tr>           
            </thead>


            <tbody>
            <?php $n=count($this->items); foreach ($this->items as $i => $item) :
            /**
             * @var EventgalleryLibraryWatermark $item;
             */
            ?>

                <tr class="row<?php echo $i % 2; ?>">
                    <td class="center">
                        <?php echo JHtml::_('grid.id', $i, $item->getId()); ?>
                    </td>
                    <td>
                    	<div class="btn-group">
                            <?php IF ($item->isPublished()): ?>
                                <a title="<?php echo JText::_('COM_EVENTGALLERY_BUTTON_PUBLISHED_DESC'); ?>" style="color: green" class="btn btn-micro active jgrid" href="javascript:void(0);" 
                                    onclick="return listItemTask('cb<?php echo $i;?>','watermarks.unpublish')">
                                    <span class="state"><i class="icon-publish"></i></span>
                                </a>
                            <?php ELSE:?>
                                <a title="<?php echo JText::_('COM_EVENTGALLERY_BUTTON_UNPUBLISHED_DESC'); ?>" style="color: red" class="btn btn-micro jgrid" href="javascript:void(0);" 
                                    onclick="return listItemTask('cb<?php echo $i;?>','watermarks.publish')">
                                    <span class="state"><i class="icon-unpublish"></i></span>
                                </a>
                            <?php ENDIF ?>                            
                            <a title="<?php echo JText::_('COM_EVENTGALLERY_BUTTON_EDIT_DESC'); ?>" class="btn btn-micro" href="<?php echo
                                JRoute::_('index.php?option=com_eventgallery&task=watermark.edit&id='.$item->getId()); ?>">
                            <i class="icon-edit"></i></a>
                        </div>
                    </td>
                    <td class="order nowrap">
                        <div class="input-prepend">
                            <span class="add-on"><?php echo $this->pagination->orderUpIcon( $i, true, 'watermarks.orderup', 'JLIB_HTML_MOVE_UP', true); ?></span>
                            <span class="add-on"><?php echo $this->pagination->orderDownIcon( $i, $n, true, 'watermarks.orderdown', 'JLIB_HTML_MOVE_UP', true ); ?></span>
                            <input class="width-40 text-area-order" type="text" name="order[]" size="3"  value="<?php echo $item->getOrdering(); ?>" />
                        </div>
                    </td>
                    <td>                  
                            <?php echo $this->escape($item->getName()) ?><br>
                            <small><?php echo $this->escape($item->getDescription()) ?></small>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="pagination pagination-toolbar">
            <?php echo $this->pagination->getPagesLinks(); ?>
        </div>
    </div>
    <?php echo JHtml::_('form.token'); ?>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="limitstart" value="<?php echo $this->pagination->limitstart; ?>" />


</form>