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
    var eventgalleryTilesCollection;

    window.addEvent("domready", function () {

        
            var options = {
                imagesetContainer: $$('.event-thumbnails')[0],
                imageset: $$('.event-thumbnail'),
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
                    var tilesOptions = {
                        tilesSelector: '.eventgallery-tiles .eventgallery-tile',
                        tilesContainerSelector: '.eventgallery-tiles'
                    };
                    eventgalleryTilesCollection = new EventgalleryTilesCollection(tilesOptions);
                    eventgalleryTilesCollection.calculate();
                    // we need to recalculate the whole thing because it might happen that a font loads
                    // and the size of a tile changes. 
                    window.addEvent('load', function(){
                        eventgalleryTilesCollection.calculate();
                    });

                },
                resizeStart: function () {
                    $$('.event-thumbnails .event-thumbnail img').setStyle('opacity', 0);
                },
                resizeComplete: function () {
                    eventgalleryLazyloader.initialize();
                    eventgalleryTilesCollection.calculate();
                    window.fireEvent('scroll');
                }
            };

            // initialize the imagelist
            eventgalleryEventsList = new EventgalleryEventsTiles(options);
        
        

    });


</script>
<?php ENDIF ?>
<div class="eventgallery-tiles-list">
    <?php if ($this->params->get('show_page_heading', 1)) : ?>
        <div class="page-header">
            <h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
        </div>
    <?php endif; ?>

    <p class="greetings"><?php echo $this->params->get('greetings'); ?></p>


    <div class="eventgallery-tiles">
		<?php foreach($this->entries as $entry) :?>
			<?php $this->set('entry',$entry)?>
			<?php echo $this->loadSnippet('events/tiles_event'); ?>
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