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

<?php
	$css=JURI::base().'components/com_eventgallery/media/css/eventgallery.css';
	$document->addStyleSheet($css);		

	/**
	* adjust the image path
	*/
	$_image_script_path = 'components/com_eventgallery/helpers/image.php';
	$params = JComponentHelper::getParams('com_eventgallery');

	if ($params->get('use_legacy_image_rendering','0')=='1') {
		$_image_script_path = "index.php";
	}


?>


<form method="POST" name="adminForm" id="adminForm">
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
				<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
		</div>
		<div class="clearfix"> </div>

		<input type="hidden" name="option" value="com_eventgallery" />
		<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>

		<input type="hidden" name="option" value="com_eventgallery" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="folderid" value="<?php echo $this->item->id; ?>" />

		
		<table class="table table-striped adminlist">
		<thead>
			<tr>
				
				<th width="20">			
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>			
				<th width="110">
					<?php echo JText::_( 'COM_EVENTGALLERY_EVENTS_FILENAME' ); ?>
				</th>
				<th>
					<?php echo JText::_( 'COM_EVENTGALLERY_EVENTS_ORDER' ); ?> 
					<?php echo JHTML::_('grid.order',  $this->items, 'filesave.png', 'files.saveorder' ); ?>
				</th>
				<th>
					<?php echo JText::_( 'COM_EVENTGALLERY_EVENTS_OPTIONS' ); ?>
				</th>		
					
				<th>
					<?php echo JText::_( 'COM_EVENTGALLERY_EVENTS_DESCRIPTION' ); ?>
				</th>
				<th>
					<?php echo JText::_( 'COM_EVENTGALLERY_EVENTS_COMMENTS' ); ?>
				</th>
				<th>
					<?php echo JText::_( 'COM_EVENTGALLERY_EVENTS_MODIFIED_BY' ); ?>
				</th>
				
			</tr>			
		</thead>
		<?php

		for ($i=0, $n=count( $this->items ); $i < $n; $i++)
		{
			$row = $this->items[$i];
			$checked 	= JHTML::_('grid.id',   $i, $row->id );
			// TODO: remove due to strange issues with at least on joomla installation $published =  JHTML::_('jgrid.published', $row->published, $i );

			?>
			<tr>
				<td>
					<!--<a name="<?php echo $row->id; ?>"></a>-->
					<?php echo $checked; ?>
				</td>
				<td>
					<img class="thumbnail" title="<?php echo $row->id; ?>" src="<?php echo JURI::base().("../$_image_script_path?view=resizeimage&folder=".$row->folder."&file=".$row->file."&option=com_eventgallery&width=100&height=50")?>" />
				</td>
				<td class="order">
					<div class="input-prepend">
						<span class="add-on"><?php echo $this->pagination->orderUpIcon( $i, true, 'files.orderdown', 'JLIB_HTML_MOVE_UP', true); ?></span>
						<span class="add-on"><?php echo $this->pagination->orderDownIcon( $i, $n, true, 'files.orderup', 'JLIB_HTML_MOVE_DOWN', true ); ?></span>
						<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="width-40 text-area-order" />
					</div>
					<small><?php echo $row->file?></small>
					
				</td>
				<td>
					<div class="btn-group">	
						<a title="<?php echo JText::_( 'COM_EVENTGALLERY_EVENT_IMAGE_ACTION_PUBLISH' ); ?>" 
							onClick="return listItemTask('cb<?php echo $i; ?>','<?php echo $row->published==0?"files.publish":"files.unpublish"; ?>')"
					        class="<?php echo $row->published==1? "btn btn-micro active" : "btn btn-micro";?>">
					        <i class="eg-icon-published"></i>
					    </a>

						<a title="<?php echo JText::_( 'COM_EVENTGALLERY_EVENT_IMAGE_ACTION_ALLOWCOMMENTS' ); ?>" onClick="document.location.href='<?php echo JRoute::_("index.php?option=com_eventgallery&view=files&task=".($row->allowcomments==0?"files.allowcomments":"files.disallowcomments")."&folderid=".$this->item->id."&cid[]=".$row->id."&limitstart=".JRequest::getVar('limitstart', '0', '', 'int')."#".$row->id) ?>'"
					        class="<?php echo $row->allowcomments==1? "btn btn-micro active" : "btn btn-micro";?>">
					        <i class="eg-icon-comments"></i>
					    </a>

					    <a title="<?php echo JText::_( 'COM_EVENTGALLERY_EVENT_IMAGE_ACTION_MAINIMAGE' ); ?>" onClick="document.location.href='<?php echo JRoute::_("index.php?option=com_eventgallery&view=files&task=".($row->ismainimage==0?"files.ismainimage":"files.isnotmainimage")."&folderid=".$this->item->id."&cid[]=".$row->id."&limitstart=".JRequest::getVar('limitstart', '0', '', 'int')."#".$row->id) ?>'"
					        class="<?php echo $row->ismainimage==1? "btn btn-micro active" : "btn btn-micro";?>">
					        <i class="eg-icon-mainimage"></i>
					    </a>

					    <a title="<?php echo JText::_( 'COM_EVENTGALLERY_EVENT_IMAGE_ACTION_MAINIMAGEONLY' ); ?>" onClick="document.location.href='<?php echo JRoute::_("index.php?option=com_eventgallery&view=files&task=".($row->ismainimageonly==0?"files.ismainimageonly":"files.isnotmainimageonly")."&folderid=".$this->item->id."&cid[]=".$row->id."&limitstart=".JRequest::getVar('limitstart', '0', '', 'int')."#".$row->id) ?>'"
					        class="<?php echo $row->ismainimageonly==0? "btn btn-micro active" : "btn btn-micro";?>">
					        <i class="eg-icon-mainimageonly"></i>
					    </a>


				    </div>	
				</td>
				<td>
					<span class="description" data-title="<?php echo htmlspecialchars($row->title, ENT_COMPAT, 'UTF-8'); ?>" data-caption="<?php echo htmlspecialchars($row->caption, ENT_COMPAT, 'UTF-8'); ?>" data-id="<?php echo $row->id ?>">
						<div class="title-content">
							<?php echo strlen($row->title)>0?$row->title:JText::_('COM_EVENTGALLERY_EVENT_FILE_TITLE'); ?>
						</div>
						<div class="caption-content">
							<?php echo strlen($row->caption)>0?$row->caption:JText::_('COM_EVENTGALLERY_EVENT_FILE_CAPTION'); ?>
						</div>
					</span>
				</td>
				<td class="center">
					<a href="<?php echo JRoute::_( 'index.php?option=com_eventgallery&task=comments&filter=folder='.$row->folder) ?>">
						<?php echo $row->commentCount ?>
					</a>
				</td>
				<td>
					<small>
						<?php $user = JFactory::getUser($row->userid); echo $user->name;?>, <br> 
						<?php echo JText::_( 'COM_EVENTGALLERY_EVENT_FILE_CREATED' ); ?><?php echo JHTML::Date($row->created,JText::_('DATE_FORMAT_LC4')) ?>, <br>
						<?php echo JText::_( 'COM_EVENTGALLERY_EVENT_FILE_MODIFIED' ); ?><?php echo JHTML::Date($row->modified,JText::_('DATE_FORMAT_LC4')) ?>
					</small>
				</td>
				
			</tr>
			<?php
		}
		?>
		</table>
		<input type="hidden" name="limitstart" value="<?php echo $this->pagination->limitstart; ?>" />
		<div class="pagination pagination-toolbar">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>	
	</div>
</form>

<script type="text/javascript">


window.addEvent("domready", function(){

	function processCaption(e) {


		var dataContainer = e.target;

		if (!dataContainer.hasClass('description')) {
			dataContainer = dataContainer.getParent('span.description');
		}

		
		var id = dataContainer.getAttribute('data-id');
		var titleContainer = dataContainer.getChildren('.title-content').getLast();
		var captionContainer = dataContainer.getChildren('.caption-content').getLast();

		titleContainer.setStyle('display','none');
		captionContainer.setStyle('display','none');


		// create form
		var formContainer = new Element('form', {

		});

		var form = new Element('div', {
			class: 'input-append'
		});

		var inputTitle = new Element('input', {
			name: 'title',
			value: dataContainer.getAttribute('data-title'),
			placeholder: '<?php echo JText::_('COM_EVENTGALLERY_EVENT_FILE_TITLE')?>'
		});
		var inputCaption = new Element('textarea', {
			name: 'caption',
			value: dataContainer.getAttribute('data-caption'),
			placeholder: '<?php echo JText::_('COM_EVENTGALLERY_EVENT_FILE_CAPTION')?>'
		});
		var buttonCancel = new Element('button',{
			text: '<?php echo JText::_('COM_EVENTGALLERY_COMMON_CANCEL')?>',
			class: 'btn btn-small',
			events: {
				click: function(e) {
					titleContainer.setStyle('display','block');
					captionContainer.setStyle('display','block');
					formContainer.dispose();
					e.preventDefault();

				}.bind(this)
			}
		});

		var buttonSave = new Element('button', {
			text: '<?php echo JText::_('COM_EVENTGALLERY_COMMON_SAVE')?>',
			class: 'btn btn-small',			
			events: {
				click: function(e) {

					var myRequest = new Request({
					    url: '<?php echo JRoute::_('index.php?task=files.saveCaption&option=com_eventgallery&cid=', false); ?>'+id,
					    data: 'caption='+encodeURIComponent(inputCaption.value)+"&title="+encodeURIComponent(inputTitle.value)

					}).post();

					dataContainer.setAttribute('data-title',inputTitle.value);
					dataContainer.setAttribute('data-caption',inputCaption.value);
					titleContainer.innerHTML = inputTitle.value.length>0?inputTitle.value:'<?php echo JText::_('COM_EVENTGALLERY_EVENT_FILE_TITLE')?>';
					captionContainer.innerHTML = inputCaption.value.length>0?inputCaption.value:'<?php echo JText::_('COM_EVENTGALLERY_EVENT_FILE_CAPTION')?>';
					//console.log("x"+inputTitle.value.length+"x");
					titleContainer.setStyle('display','block');
					captionContainer.setStyle('display','block');
					formContainer.dispose();
					e.preventDefault();
				}.bind(this)
			}
		});

		form.grab(inputTitle);
		form.grab(buttonCancel);
		form.grab(buttonSave);
		formContainer.grab(form);
		formContainer.grab(inputCaption);
		dataContainer.grab(formContainer);

	}

	$$('.title-content').addEvent('click', processCaption);
	$$('.caption-content').addEvent('click', processCaption);

});	

</script>