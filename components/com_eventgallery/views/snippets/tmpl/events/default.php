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


<?php IF (count($this->entries)>0): ?>
<script type="text/javascript">

    var eventgalleryEventsList;
    var eventgalleryLazyloader;

    window.addEvent("domready", function () {

        
            var options = {
                rowHeightPercentage: 100,
                imagesetContainer: $$('.event-thumbnails')[0],
                imageset: $$('.event-thumbnails .event-thumbnail'),
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
                    $$('.event-thumbnails .event-thumbnail img').setStyle('opacity', 0);
                },
                resizeComplete: function () {
                    eventgalleryLazyloader.initialize();
                    window.fireEvent('scroll');
                }
            };

            // initialize the imagelist
            eventgalleryEventsList = new EventgalleryEventsList(options);
        

    });
</script>
<?php ENDIF ?>
<div id="events">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
        <div class="page-header">
            <h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
        </div>
    <?php endif; ?>

    <p class="greetings"><?php echo $this->params->get('greetings'); ?></p>


    <div>
		<?php foreach($this->entries as $entry) :?>
			<?php $this->set('entry',$entry)?>
			<?php echo $this->loadSnippet('events/default_event'); ?>
		<?php endforeach?>

		<div style="clear:both"></div>
	</div>
	
	<form method="post" name="adminForm">

		<div class="pagination">
		<div class="counter pull-right"><?php echo $this->pageNav->getPagesCounter(); ?></div>
		<div class="float_left"><?php echo $this->pageNav->getPagesLinks(); ?></div>
		<div class="clear"></div>
		</div>
		
	</form>
	
</div>