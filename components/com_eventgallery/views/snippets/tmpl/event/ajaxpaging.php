<?php // no direct access
/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access'); ?>

<?php echo $this->loadSnippet('imageset/imagesetselectionajax'); ?>

<?php echo $this->loadSnippet('event/ajaxpaging_script'); ?>

<div itemscope itemtype="http://schema.org/Event" class="ajaxpaging">

    <?php
    $pageCount = 0;
    $imageCount = 0;
    $imagesOnPage = 0;
    $imagesFirstPage = $this->params->get('event_ajax_list_number_of_thumbnail_on_first_page', 11);
    $imagesPerPage = $this->params->get('event_ajax_list_number_of_thumbnail_per_page', 22);

    $pagesCount = ceil((count($this->entries) - $imagesFirstPage) / $imagesPerPage) + 1;
    ?>

	<div style="display:none">
    	<?php 
    		if (isset($this->entries[0])) {
            echo '<meta itemprop="image" content="'. $this->entries[0]->getOriginalImageUrl() .'" />';
            echo '<link rel="image_src" tpe="image/jpeg" href="'. $this->entries[0]->getOriginalImageUrl() .'" />';
    		}
    	?>
      <span itemprop="startDate" content="<?php echo $this->folder->getDate(); ?>">
        <?php echo JHTML::Date($this->folder->getDate()); ?>
      </span>
    </div>

    <div class="navigation">

        <div id="pagerContainer">
            <div id="thumbs">
                <div id="pageContainer">

                    <div id="page<?php echo $pageCount++; ?>" class="page">

                        <?php foreach ($this->entries as $entry) :/** @var EventgalleryLibraryFile $entry */ ?>

                        <?php IF ($pageCount == 1 && $imageCount == 0): ?>
                            <?php IF ($this->params->get('show_date', 1) == 1): ?>
                                <h4 class="date">
                                    <?php echo JHTML::Date($this->folder->getDate()); ?>
                                </h4>
                            <?php ENDIF ?>
                            <h1 itemprop="name" class="description">
                                <?php echo $this->folder->getDescription(); ?>
                            </h1>
                            <div itemprop="description" class="text">
                            	<?php echo JHtml::_('content.prepare', $this->folder->getText(), '', 'com_eventgallery.event'); ?>
                            </div>
                        <?php ENDIF; ?>

                        <?php $this->assign('entry', $entry) ?>
                        <?php $imagesOnPage++ ?>

                        <div class="ajax-thumbnail-container thumbnail" id="image<?php echo $imageCount++; ?>">
                            <a longdesc="<?php echo $entry->getImageUrl(NULL, NULL, true); ?>"
                               class="ajax-thumbnail"
                               href="<?php echo $entry->getImageUrl(NULL, NULL, true); ?>"
                               title="<?php echo htmlspecialchars($entry->getPlainTextTitle(), ENT_COMPAT, 'UTF-8'); ?>"
                               rel="<?php echo $entry->getImageUrl(50, 50, false, false); ?>"
                               data-folder="<?php echo $entry->getFolderName(); ?>"
                               data-file="<?php echo $entry->getFileName(); ?>"
                               <?php IF ($this->params->get('show_cart_connector', 0)==1):?>
							       data-cart-connector-link="<?php echo rawurlencode(EventgalleryHelpersCartconnector::getLink($this->entry->getFolderName(), $this->entry->getFileName()));?>"
							   <?php ENDIF ?>
                               data-id="folder=<?php echo $entry->getFolderName() ?>&amp;file=<?php echo $entry->getFileName() ?>"
                               data-width="<?php echo $entry->getWidth(); ?>"
                               data-height="<?php echo $entry->getHeight(); ?>"
                               data-description="<?php if ($this->params->get('show_date', 1) == 1) {
                                   echo JHTML::Date($this->folder->getDate()) . ' - ';
                               }
                               echo htmlentities($this->folder->getDescription() . "<br> " . JText::_(
                                       'COM_EVENTGALLERY_EVENT_AJAX_IMAGE_CAPTION_IMAGE'
                                   ) . " $imageCount " . JText::_('COM_EVENTGALLERY_EVENT_AJAX_IMAGE_CAPTION_OF')
                                   . " $this->entriesCount", ENT_QUOTES, "UTF-8") ?>
										<br /><?php echo rawurlencode($entry->getTitle($this->params->get('show_image_filename',0)==1, $this->params->get('show_exif','1')=='1')); ?>"
                               data-title="<?php echo rawurlencode($entry->getLightBoxTitle($this->params->get('show_image_filename',0)==1, $this->params->get('show_exif','1')=='1')); ?>"
                           	   <?php IF ($this->params->get('use_social_sharing_button', 0)==1):?>
							    	data-social-sharing-link="<?php echo rawurlencode(JRoute::_('index.php?option=com_eventgallery&view=singleimage&layout=share&folder='.$this->entry->getFolderName().'&file='.$this->entry->getFileName()."&Itemid=".$this->currentItemid.'&format=raw') ); ?>"
							   <?php ENDIF ?>
                                >
                                <?php echo $entry->getThumbImgTag(
                                    $this->params->get('event_ajax_list_thumbnail_size', 75),
                                    $this->params->get('event_ajax_list_thumbnail_size', 75)
                                ); ?>
                            </a>
                        </div>

                        <?php IF (($imagesOnPage % $imagesPerPage == 0)
                        || ($pageCount == 1
                            && ($imagesOnPage % $imagesFirstPage == 0))): ?>
                    </div>
                    <div id="page<?php echo $pageCount++; ?>" class="page">
                        <?php $imagesOnPage = 0; ?>
                        <?php ENDIF; ?>

                        <?php endforeach ?>
                    </div>

                </div>
            </div>
            <div class="clear"></div>
        </div>

        <!--<a style="" href="#" onclick="myGallery.prevPage(); return false;" id="prev"><img src="<?php echo JURI::base().'components/com_eventgallery/media/images/prev_button.png'?>" alt="back" style="border: 0px;"/></a>
		<a style="" href="#" onclick="myGallery.nextPage(); return false;" id="next"><img src="<?php echo JURI::base().'components/com_eventgallery/media/images/next_button.png'?>" alt="next" style="border: 0px;"/></a>-->
        <div class="pagination">
            <ul id="count"></ul>
        </div>

    </div>

    <div class="image">

        <div id="bigimageContainer">
            <img src="<?php echo JURI::base() . 'components/com_eventgallery/media/images/loading_s.gif' ?>" alt=""
                 id="bigImage"/>
            <span id="bigImageDescription" class="img_overlay img_overlay_fotos overlay_3"><?php echo JText::_(
                    'COM_EVENTGALLERY_EVENT_AJAX_LOADING'
                ) ?></span>
        </div>

    </div>
    <div style="clear:both"></div>
    
</div>
