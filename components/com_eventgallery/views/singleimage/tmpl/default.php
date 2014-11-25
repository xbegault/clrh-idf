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
?>

<?php echo $this->loadSnippet('social'); ?>

<script type="text/javascript">


    // for auto resize
    var eventgalleryImageList;
    var eventgalleryLazyloader;

    window.addEvent("domready", function () {

        $$('.singleimage').each(function(imagesetContainer){

            var options = {
                rowHeight: 100,
                rowHeightJitter: 0,
                firstImageRowHeight: 1,
                cropLastImage: false,
                imagesetContainer: imagesetContainer,
                imageset: $$('#bigimagelink'),
                initComplete: function () {
                    eventgalleryLazyloader = new LazyLoadEventgallery({
                        range: 100,
                        elements: 'img.lazyme',
                        image: 'components/com_eventgallery/media/images/blank.gif',
                        onScroll: function () {
                            //console.log('scrolling');
                        },
                        onLoad: function (img) {
                            //console.log('image loaded');
                            setTimeout(function () {
                                img.setStyle('opacity', 0).fade(1);
                            }, 500);
                        },
                        onComplete: function () {
                            //console.log('all images loaded');
                        }

                    });

                },
                resizeStart: function () {
                },
                resizeComplete: function () {
                    eventgalleryLazyloader.initialize();
                    window.fireEvent('scroll');

                }
            };

            // initialize the imagelist
            eventgalleryImageList = new EventgalleryImagelist(options);
        });

    });


    <?php IF ($this->file->isCommentingAllowed() && $this->use_comments==1): ?>
    window.addEvent('domready', function () {


        var initialVisible = <?php echo (JRequest::getString('success', 'true')=='false'?'true':'false'); ?>;
        $('commentform').setStyle('visibility','visible');
        if (initialVisible) {
            $('commentform').setStyle('display','block');
        } else {
            $('commentform').setStyle('display','none');
        }

        $('toggle_comment').addEvent('click', function (e) {
            e.stop();
            $('commentform').toggle();
        });

    });
    <?php ENDIF ?>


    var myKeyboardEvents = new Keyboard({
        eventType: 'keydown',
        events: {
            'left': function (e) {
                if (EventGalleryMediabox && EventGalleryMediabox.isActive()
                    && eventGalleryMediaBoxImages && eventGalleryMediaBoxImages[0][2] == 'cart') {
                    return;
                }
                if ($('prev_image')) {
                    document.location.href = $('prev_image').get('href');
                }
            },
            'right': function (e) {
                if (EventGalleryMediabox && EventGalleryMediabox.isActive()
                    && eventGalleryMediaBoxImages && eventGalleryMediaBoxImages[0][2] == 'cart') {
                    return;
                }
                if ($('next_image')) {
                    document.location.href = $('next_image').get('href');
                }
            }
        }
    });

    myKeyboardEvents.activate();


</script>

<?php  echo  $this->loadSnippet("cart"); ?>

<div id="singleimage">

    <?php IF ($this->params->get('show_date', 1) == 1): ?>
        <h4 class="date">
            <?php echo JHTML::date($this->folder->getDate()) ?>
        </h4>
    <?php ENDIF ?>
    <h1 class="description">
        <?php echo $this->folder->getDescription() ?>
    </h1>

    <a name="image"></a>

    <div class="btn-group">
        <a class="btn singleimage-overview" href="<?php echo JRoute::_(
            "index.php?option=com_eventgallery&view=event&folder=" . $this->folder->getFolderName() . "&limitstart="
            . $this->model->currentLimitStart
        ); ?>" title="<?php echo JText::_('COM_EVENTGALLERY_SINGLEIMAGE_NAV_OVERVIEW') ?>"><i class="eventgallery-icon-list"></i></a>

        <?php IF (  $this->model->firstFile && $this->model->firstFile != $this->file): ?>
            <a class="btn singleimage-first" href="<?php echo JRoute::_(
                "index.php?option=com_eventgallery&view=singleimage&folder=" . $this->model->firstFile->getFolderName()
                . "&file=" . $this->model->firstFile->getFileName()
            ) ?>#image" title="<?php echo JText::_('COM_EVENTGALLERY_SINGLEIMAGE_NAV_START') ?>"><i
                    class="eventgallery-icon-fast-backward"></i></a>
        <?php ENDIF ?>  

        <?php IF ($this->model->prevFile && $this->model->prevFile != $this->file): ?>
            <a class="btn singleimage-prev" id="prev_image" href="<?php echo JRoute::_(
                "index.php?option=com_eventgallery&view=singleimage&folder=" . $this->model->prevFile->getFolderName() . "&file="
                . $this->model->prevFile->getFileName()
            ) ?>#image" title="<?php echo JText::_('COM_EVENTGALLERY_SINGLEIMAGE_NAV_PREV') ?>"><i
                    class="eventgallery-icon-arrow-left"></i></a>
        <?php ENDIF ?>

        <?php IF ($this->model->nextFile && $this->model->nextFile != $this->file): ?>
            <a class="btn singleimage-next" id="next_image" href="<?php echo JRoute::_(
                "index.php?option=com_eventgallery&view=singleimage&folder=" . $this->model->nextFile->getFolderName() . "&file="
                . $this->model->nextFile->getFileName()
            ) ?>#image" title="<?php echo JText::_('COM_EVENTGALLERY_SINGLEIMAGE_NAV_NEXT') ?>"><i
                    class="eventgallery-icon-arrow-right"></i></a>
        <?php ENDIF ?>

        <?php IF ($this->model->lastFile && $this->model->lastFile != $this->file): ?>
            <a class="btn singleimage-last" href="<?php echo JRoute::_(
                "index.php?option=com_eventgallery&view=singleimage&folder=" . $this->model->lastFile->getFolderName() . "&file="
                . $this->model->lastFile->getFileName()
            ) ?>#image" title="<?php echo JText::_('COM_EVENTGALLERY_SINGLEIMAGE_NAV_END') ?>"><i
                    class="eventgallery-icon-fast-forward"></i></a>
        <?php ENDIF ?>

        <?php IF ($this->file->isCommentingAllowed() && $this->use_comments == 1): ?>
            <a class="btn singleimage-comment" href="#" id="toggle_comment"
               title="<?php echo JText::_('COM_EVENTGALLERY_SINGLEIMAGE_NAV_COMMENT') ?>"><i
                    class="eventgallery-icon-comment"></i></a>
        <?php ENDIF ?>

        <a class="btn singleimage-zoom" href="<?php echo $this->file->getImageUrl(NULL, NULL, true) ?>"
           rel="lightbo2[gallery<?php echo
           $this->params->get('use_fullscreen_lightbox', 0) == 1 ? 'fullscreen' : ''; ?>]"
           title="<?php echo JText::_('COM_EVENTGALLERY_SINGLEIMAGE_NAV_ZOOM') ?>"><i class="eventgallery-icon-zoom-in"></i></a>

        
        <?php IF ($this->folder->isCartable()  && $this->params->get('use_cart', '1')==1): ?>
            <a href="#" class="btn btn-primary imagetypeselection-show"><?php echo JText::_('COM_EVENTGALLERY_PRODUCT_BUY_IMAGES') ?></a>
        <?php ENDIF ?>

        <?php IF ($this->folder->isCartable() && $this->params->get('show_cart_connector', 0) == 1): ?>
            <a href="<?php echo EventgalleryHelpersCartconnector::getLink(
                $this->file->getFolderName(), $this->file->getFileName()
            ); ?>" class="btn button-cart-connector" title="<?php echo JText::_('COM_EVENTGALLERY_CART_CONNECTOR') ?>"
               data-folder="<?php echo $this->file->getFolderName() ?>"
               data-file="<?php echo $this->file->getFileName(); ?>"><i class="eventgallery-icon-cart-connector-small"></i></a>
        <?php ENDIF ?>

		<?php IF ($this->params->get('use_social_sharing_button', 0)==1 && $this->folder->getAttribs()->get('use_social_sharing', 1)==1):?>
			<a class="btn social-share-button social-share-button-open" rel="nofollow" href="#" data-link="<?php echo JRoute::_('index.php?option=com_eventgallery&view=singleimage&layout=share&folder='.$this->file->getFolderName().'&file='.$this->file->getFileName().'&format=raw'); ?>" class="social-share-button" title="<?php echo JText::_('COM_EVENTGALLERY_SOCIAL_SHARE')?>" ><i class="eventgallery-icon-social-share-button-small"></i></a>
		<?php ENDIF ?>

        <?php IF ($this->file->getHitCount()>0 && $this->params->get('show_singlepage_imagehits', 1) == 1): ?>
            <div class="btn singleimage-hits"><?php echo JText::_(
                    'COM_EVENTGALLERY_SINGLEIMAGE_HITS'
                ) ?> <?php echo $this->file->getHitCount() ?></div>
        <?php ENDIF ?>
    </div>


    <?php IF ($this->folder->isCartable()  && $this->params->get('use_cart', '1')==1): ?>        
        <?php echo $this->loadSnippet('imageset/imagesetselectionsingle'); ?>
    <?php ENDIF ?>

    <br>
    <?php echo $this->loadTemplate('commentform'); ?>
    <br>

    <div class="singleimage">
        <a class="thumbnail"
           id="bigimagelink"
           data-title="<?php echo rawurlencode($this->file->getLightBoxTitle($this->params->get('show_image_filename',0)==1, $this->params->get('show_exif','1')=='1')) ?>"
           href="<?php echo $this->file->getImageUrl(NULL, NULL, true) ?>"
           rel="lightbo2">
            <?php echo $this->file->getLazyThumbImgTag(100, 100); ?>
        </a>
        <?php IF ($this->file->hasTitle()): ?>
            <div class="well description"><?php echo $this->file->getTitle($this->params->get('show_image_filename',0)==1, $this->params->get('show_exif','1')=='1'); ?></div>
        <?php ELSEIF ($this->params->get('show_image_filename',0)==1): ?>
        	<div class="well description"><div class="img-id"><?php echo JText::_('COM_EVENTGALLERY_IMAGE_ID'); ?> <?php echo $this->file->getFileName() ?></div></div>
        <?php ENDIF ?>
    </div>
    
    <a name="comments"></a>
    <?php echo $this->loadTemplate('comments'); ?>

</div>

<?php echo $this->loadSnippet('footer_disclaimer'); ?>