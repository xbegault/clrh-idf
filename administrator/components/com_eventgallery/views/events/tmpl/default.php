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
    JHtml::_('bootstrap.tooltip');
    JHtml::_('behavior.multiselect');
    JHtml::_('formbehavior.chosen', 'select');
 
} else {
    JHtml::_('behavior.tooltip');
    $css=JURI::base().'components/com_eventgallery/media/css/legacy.css';
    $document->addStyleSheet($css);
}

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$sortFields = $this->getSortFields();

if ($version->isCompatible('3.0')) {
    $saveOrder	= $listOrder == 'ordering';
    if ($saveOrder)
    {
        $saveOrderingUrl = 'index.php?option=com_eventgallery&task=events.saveOrderAjax&tmpl=component';
        JHtml::_('sortablelist.sortable', 'eventsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
    }
}
?>

<script type="text/javascript">
    Joomla.orderTable = function()
    {
        table = document.getElementById("sortTable");
        direction = document.getElementById("directionTable");
        order = table.options[table.selectedIndex].value;
        if (order != '<?php echo $listOrder; ?>')
        {
            dirn = 'desc';
        }
        else
        {
            dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    }
</script>
<form method="post" id="adminForm" name="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
		<div id="filter-bar" class="btn-toolbar">
            <div class="filter-search btn-group pull-left">
                <label for="filter_search" class="element-invisible"><?php echo JText::_('COM_EVENTGALLERY_EVENT_SEARCH_LABEL');?></label>
                <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_EVENTGALLERY_EVENT_SEARCH_PLACEHOLDER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('COM_EVENTGALLERY_ORDERS_SEARCH_DESC'); ?>" />
            </div>
            <div class="btn-group pull-left">
                <button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                <button class="btn hasTooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                <?php echo $this->pagination->getLimitBox(); ?>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
                <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
                    <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
                    <option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('COM_EVENTGALLERY_ORDER_ASCENDING');?></option>
                    <option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('COM_EVENTGALLERY_ORDER_DESCENDING');?></option>
                </select>
            </div>
            <div class="btn-group pull-right">
                <label for="sortTable" class="element-invisible"><?php echo JText::_('COM_EVENTGALLERY_SORT_BY');?></label>
                <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                    <option value=""><?php echo JText::_('COM_EVENTGALLERY_SORT_BY');?></option>
                    <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
                </select>
            </div>
        </div>
		<div class="clearfix"> </div>

	<table class="adminlist table table-striped" id="eventsList">
		<thead>
			<tr>
                <?php if ($version->isCompatible('3.0')):?>
                <th width="1%" class="nowrap center hidden-phone">
                    <?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
                </th>
                <?php ENDIF; ?>
                <th width="20">
                    <!--<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ); ?>);" />-->
                    <input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                </th>
                <th class="nowrap" width="1%">
					
				</th>
				<th>
					<?php echo JText::_( 'COM_EVENTGALLERY_EVENTS_FOLDERNAME' ); ?>
				</th>

				<th>
					<?php echo JText::_( 'COM_EVENTGALLERY_EVENTS_ORDER' ); ?> 
					<?php echo JHTML::_('grid.order',  $this->items, 'filesave.png', 'events.saveorder' ); ?>	
				</th>				
				<th>
					<?php echo JText::_( 'COM_EVENTGALLERY_EVENTS_DESCRIPTION' ); ?>
				</th>
                <th class="nowrap">
                    <?php echo JText::_( 'COM_EVENTGALLERY_EVENTS_EVENT_DATE' ); ?>
                </th>
                <th>
					&nbsp;
				</th>
				<th class="nowrap">
					<?php echo JText::_( 'COM_EVENTGALLERY_EVENTS_COMMENTS' ); ?>
				</th>
				<th class="nowrap">
					<?php echo JText::_( 'COM_EVENTGALLERY_EVENTS_MODIFIED_BY' ); ?>
				</th>
				
			</tr>			
		</thead>
		<?php
		
		for ($i=0, $n=count( $this->items ); $i < $n; $i++)
		{
			$row = $this->items[$i];		
			$checked 	= JHTML::_('grid.id',   $i, $row->id );
			$editLink 	= JRoute::_( 'index.php?option=com_eventgallery&task=event.edit&id='. $row->id );
			$uploadLink = JRoute::_( 'index.php?option=com_eventgallery&task=upload.upload&folderid='. $row->id );
			$filesLink  = JRoute::_( 'index.php?option=com_eventgallery&view=files&folderid='. $row->id);


			?>
			<tr class="">
                <?php if ($version->isCompatible('3.0')):?>
                <td>
                    <?php
                    $iconClass = '';
                    if (!$saveOrder)
                    {
                        $iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
                    }
                    ?>
                    <span class="sortable-handler<?php echo $iconClass ?>">
						<i class="icon-menu"></i>
					</span>
                    <?php if ($saveOrder) : ?>
                        <!--<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $row->ordering;?>" class="width-20 text-area-order " />-->
                    <?php endif; ?>

                </td>
                <?php ENDIF; ?>
                <td>
                    <?php echo $checked; ?>
                </td>
				<td>
					<div class="btn-group">

                        
                        <?php IF ($row->published==1): ?>
                            <a title="<?php echo JText::_('COM_EVENTGALLERY_BUTTON_PUBLISHED_DESC'); ?>" style="color: green" class="btn btn-micro active jgrid" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','events.unpublish')">
                                <span class="state"><i class="icon-publish"></i></span>
                            </a>
                        <?php ELSE:?>
                            <a title="<?php echo JText::_('COM_EVENTGALLERY_BUTTON_UNPUBLISHED_DESC'); ?>" style="color: red" class="btn btn-micro jgrid" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','events.publish')">
                                <span class="state"><i class="icon-unpublish"></i></span>
                            </a>
                        <?php ENDIF ?>


                        <?php IF ($row->cartable==1): ?>
                            <a title="<?php echo JText::_('COM_EVENTGALLERY_BUTTON_CARTABLE_DESC'); ?>" style="color: green" class="btn btn-micro active jgrid" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','events.notcartable')">
                                <span class="state"><i class="icon-cart"></i></span>
                            </a>
                        <?php ELSE:?>
                            <a title="<?php echo JText::_('COM_EVENTGALLERY_BUTTON_UNCARTABLE_DESC'); ?>" style="color: red" class="btn btn-micro jgrid" href="javascript:void(0);" onclick="return listItemTask('cb<?php echo $i;?>','events.cartable')">
                                <span class="state"><i class="icon-cart"></i></span>
                            </a>
                        <?php ENDIF ?>

                        <?php IF (EventgalleryLibraryFolderLocal::canHandle($row->folder)): ?>
						<?php /*the following mix of jgrid and btn is for being compatible with joomla 2.5 and 3.0*/ ?>
                            <a title="<?php echo JText::_('COM_EVENTGALLERY_BUTTON_UPLOAD_DESC'); ?>" href="<?php echo $uploadLink; ?>" id="upload_<?php echo $row->id?>" class="btn btn-micro jgrid">
                                <span class="state "><i class="icon-upload"></i>	<span class="text"></span></span>
                            </a>
                            <a title="<?php echo JText::_('COM_EVENTGALLERY_BUTTON_FILES_DESC'); ?>" href="<?php echo $filesLink; ?>" id="files_<?php echo $row->id?>" class="btn btn-micro jgrid">
                                <span class="state"><i class="icon-folder-2"></i>	<span class="text"></span></span>
                            </a>
                        <?php ENDIF ?>
                        <a title="<?php echo JText::_('COM_EVENTGALLERY_BUTTON_EDIT_DESC'); ?>" href="<?php echo $editLink; ?>" id="files_<?php echo $row->id?>" class="btn btn-micro jgrid">
                            <span class="state"><i class="icon-edit"></i>	<span class="text"></span></span>
                        </a>
                        
					</div>				
				</td>
				<td>
					<?php echo $row->folder;
                    /**
                     * @var EventgalleryLibraryManagerFolder $folderMgr
                     */
                    $folderMgr = EventgalleryLibraryManagerFolder::getInstance();
                    $folder = $folderMgr->getFolder($row->folder);

                    ?><br/>
                    <small>
                        <?php echo $folder->getFileCount();?> <?php echo JText::_('COM_EVENTGALLERY_EVENTS_FILECOUNT_FILES'); ?>, 
                        <?php echo $folder->getHits();?> <?php echo JText::_('COM_EVENTGALLERY_EVENTS_HITS'); ?> 
                    </small></br>


                    <?php
                        if ( null != $folder->getWatermark() ) {
                            echo '<small><strong>'.JText::_( 'COM_EVENTGALLERY_EVENTS_WATERMARK' ).'</strong>';
                            echo '<br/>'.$folder->getWatermark()->getName().'</small><br>';
                        }
                        if (null != $folder->getImageTypeSet()) {
                            echo '<small><strong>'.JText::_( 'COM_EVENTGALLERY_EVENTS_IMAGETYPESET' ).'</strong>';
                            echo '<br/>'.$folder->getImageTypeSet()->getName().'</small><br>';
                        }
                    ?>
				</td>

				<td class="order nowrap">
					<div class="input-prepend">
						<span class="add-on"><?php echo $this->pagination->orderUpIcon( $i, true, 'events.orderdown', 'JLIB_HTML_MOVE_UP', true); ?></span>
						<span class="add-on"><?php echo $this->pagination->orderDownIcon( $i, $n, true, 'events.orderup', 'JLIB_HTML_MOVE_UP', true ); ?></span>
						<input class="width-40 text-area-order" type="text" name="order[]" size="3"  value="<?php echo $row->ordering; ?>" />
					</div>
				</td>

                <td>
					<?php echo $row->description; ?>
				</td>
                <td class="nowrap">
                    <?php echo JHTML::Date($row->date, JText::_('DATE_FORMAT_LC3')); ?><br>
                </td>
                <td>
                    <small>
                        <?php IF (strlen($row->category_title)>0): ?>
                            <strong><?php echo JText::_( 'COM_EVENTGALLERY_EVENTS_CATEGORY' ); ?></strong><br>
                            <?php echo $row->category_title; ?><br>
                        <?php ENDIF ?>
                        <?php IF (strlen($row->foldertags)>0): ?>
                            <strong><?php echo JText::_( 'COM_EVENTGALLERY_EVENTS_TAGS' ); ?></strong><br>
                            <?php echo $row->foldertags; ?><br>
                        <?php ENDIF ?>
                        <?php IF (strlen($row->picasakey)>0): ?>
                            <strong> <?php echo JText::_( 'COM_EVENTGALLERY_EVENTS_PICASA_KEY' ); ?></strong><br>
                            <?php echo $row->picasakey; ?><br>
                        <?php ENDIF ?>
                        <?php IF (strlen($row->password)>0): ?>
                            <strong><?php echo JText::_( 'COM_EVENTGALLERY_EVENTS_PASSWORD' ); ?></strong><br>
                            <?php echo $row->password; ?><br>
                        <?php ENDIF ?>
                        <?php IF (strlen($row->usergroupids)>0 && $row->usergroupids!='1'): ?>
                            <strong><?php echo JText::_( 'COM_EVENTGALLERY_EVENTS_USERGROUPS' ); ?></strong><br>
                            <?php
                                $usergroupids = explode(',',$row->usergroupids);
                                $groups = array();
                                foreach($usergroupids as $usergroupid) {
                                    $groups[] = EventgalleryHelpersUsergroups::getUserGroupName($usergroupid);
                                }
                                echo implode(',', $groups);
                            ?><br>
                        <?php ENDIF ?>
                    </small>
				</td>
				<td class="center">
					<a href="<?php echo JRoute::_( 'index.php?option=com_eventgallery&view=comments&filter=folder='.$row->folder) ?>">
						<?php echo $row->commentCount ?>
					</a>
				</td>
				<td><small>
					<?php $user = JFactory::getUser($row->userid); echo $user->name;?><br /> 
                    <?php echo JText::_( 'COM_EVENTGALLERY_EVENT_CREATED' ); ?> <?php echo JHTML::Date($row->created,JText::_('DATE_FORMAT_LC4')) ?><br>
                    <?php echo JText::_( 'COM_EVENTGALLERY_EVENT_MODIFIED' ); ?> <?php echo JHTML::Date($row->modified,JText::_('DATE_FORMAT_LC4')) ?><br>
                    </small>
				</td>
				
			</tr>
			<?php
			
		}
		?>
		</table>
		<div class="pagination pagination-toolbar">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	</div>

    <?php //Load the batch processing form. ?>
    <?php if ($version->isCompatible('3.0')) {echo $this->loadTemplate('batch');} ?>

	<?php echo JHtml::_('form.token'); ?>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="limitstart" value="<?php echo $this->pagination->limitstart; ?>" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<input type="hidden" name="option" value="com_eventgallery" />	
	
</form>
