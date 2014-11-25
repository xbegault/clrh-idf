<?php // no direct access
/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access'); ?>

<script type="text/javascript">
    /* <![CDATA[ */

    var myGallery;

    /* Method to bring the thumb rel attribute to the right size */
    var adjustImageSize = function () {
        var imageContainerSize = $('bigimageContainer').getSize();
        var sizeCalculator = new SizeCalculator();
        var width = imageContainerSize.x;

        $$('#thumbs .ajax-thumbnail').each(function (item) {
            var ratio = item.getAttribute('data-width') / item.getAttribute('data-height');
            var height = Math.round(width / ratio);
            var googleWidth = sizeCalculator.getSize(width, height, ratio);
            item.setAttribute('rel', sizeCalculator.adjustImageURL(item.getAttribute('rel'), googleWidth));
        });
    }

    /* start the eventgallery*/
    window.addEvent("domready", function () {
        adjustImageSize();
        myGallery = new JSGallery2($$('.ajax-thumbnail-container'), $('bigImage'), $('pageContainer'),
            {    'prevHandle': $('prev'),
                'nextHandle': $('next'),
                'countHandle': $('count'),
                'prev_image': '<?php echo JURI::base().'components/com_eventgallery/media/images/prev_button.png'?>',
                'next_image': '<?php echo JURI::base().'components/com_eventgallery/media/images/next_button.png'?>',
                'zoom_image': '<?php echo JURI::base().'components/com_eventgallery/media/images/zoom_button.png'?>',
                'titleTarget': 'bigImageDescription',
                'showSocialMediaButton': <?php echo ($this->params->get('use_social_sharing_button', 0)==1  && $this->folder->getAttribs()->get('use_social_sharing', 1)==1)?'true':'false'?>,
                'showCartButton': <?php echo $this->folder->isCartable()?'true':'false'; ?>,
                'showCartConnector': <?php echo $this->params->get('show_cart_connector', 0)==1&&$this->folder->isCartable()==1?'true':'false'; ?>,
				'cartConnectorLinkRel' : '<?php echo $this->params->get('cart_connector_link_rel', 'nofollow')?>',
                'lightboxRel': 'lightbo2[gallery<?php echo $this->params->get('use_fullscreen_lightbox',0)==1?'fullscreen':''; ?>]'
            });

    });

    /* Method which handles the case the window got resized */
    var resizePage = function () {

        window.clearTimeout(eventgalleryAjaxResizeTimer);

        var eventgalleryAjaxResizeTimer = (function () {
            var size = $$('.ajaxpaging .navigation').getLast().getSize();

            $$('.navigation .page').setStyle('width', size.x + 2 + "px");
            if (myGallery != undefined) {
                adjustImageSize();
                myGallery.resetThumbs();
                myGallery.gotoPage(myGallery.currentPageNumber);
            }
        }.bind(this)).delay(500);
    };

    window.addEvent('load', resizePage);
    window.addEvent('resize', resizePage);
    /* ]]> */
</script>
