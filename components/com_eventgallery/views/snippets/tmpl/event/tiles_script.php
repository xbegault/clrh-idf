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

<script type="text/javascript">

    var eventgalleryEventsList;
    var eventgalleryLazyloader;
    var eventgalleryGridCollection;

    window.addEvent("domready", function () {

            
            var options = {
                imagesetContainer: $$('.event-thumbnails')[0],
                imageset: $$('.event-thumbnail'),
                adjustMode: 'width',
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
                    
                    var gridOptions = {
                        tilesSelector: '.eventgallery-tiles .eventgallery-tile',
                        tilesContainerSelector: '.eventgallery-tiles',
                        thumbSelector: '.event-thumbnail',
                        thumbContainerSelector: '.event-thumbnails'
                    };
                    eventgalleryGridCollection = new EventgalleryTilesCollection(gridOptions);
                    eventgalleryGridCollection.calculate();
                    // we need to recalculate the whole thing because it might happen that a font loads
                    // and the size of a tile changes. 
                    window.addEvent('load', function(){
                        eventgalleryGridCollection.calculate();
                    });

                },
                resizeStart: function () {
                    $$('.event-thumbnails .event-thumbnail img').setStyle('opacity', 0);
                },
                resizeComplete: function () {
                    eventgalleryLazyloader.initialize();
                    eventgalleryGridCollection.calculate();
                    window.fireEvent('scroll');
                }
            };

            // initialize the imagelist
            eventgalleryEventsList = new EventgalleryEventsTiles(options);
                

    });

</script>