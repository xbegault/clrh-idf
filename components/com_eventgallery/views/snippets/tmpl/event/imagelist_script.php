<?php
/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<script type="text/javascript">
    var eventgalleryImageList;
    var eventgalleryLazyloader;


    if (typeof eventgalleryImageList == 'undefined') {
        eventgalleryImageList = null;        
        

        window.addEvent("domready", function () {

            $$('.eventgallery-thumbnails').each(function(imagesetContainer){

            var options = {
                rowHeight: <?php echo $this->params->get('event_image_list_thumbnail_height',150); ?>,
                rowHeightJitter: <?php echo $this->params->get('event_image_list_thumbnail_jitter',50); ?>,
                firstImageRowHeight: <?php echo $this->params->get('event_image_list_thumbnail_first_item_height',2); ?>,                
                imagesetContainer: imagesetContainer,
                imageset: imagesetContainer.getElements('.thumbnail'),
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
                    
                    options.imagesetContainer.getElements('.thumbnail img').setStyle('opacity', 0);


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
        
    }
</script>
