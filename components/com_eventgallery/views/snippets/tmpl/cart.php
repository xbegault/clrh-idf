<?php // no direct access
/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access'); ?>

<?php
/**
 * @var JSite $myApp
 */
$myApp = JFactory::getApplication();
/**
 * @var JRegistry $myParams
 */
$myParams = $myApp->getParams();
$use_cart = $myParams->get('use_cart', '1') == 1;
$use_cart_inside_component = $myParams->get('use_cart_inside_component', '1') == 1;

?>

<?php IF ($use_cart): ?>

    <?php IF ($use_cart_inside_component): ?>
        <script type="text/javascript">
            /* <![CDATA[ */
            window.addEvent("domready", function () {

                var options = {
                    buttonShowType: 'inline',
                    emptyCartSelector: '.eventgallery-cart-empty',
                    cartSelector: '.eventgallery-ajaxcart-internal',
                    cartItemContainerSelector: '.eventgallery-ajaxcart-internal .cart-items-container',
                    cartItemsSelector: '.eventgallery-ajaxcart-internal .cart-items',
                    cartItemSelector: '.eventgallery-ajaxcart-internal .cart-items .cart-item',
                    cartCountSelector: '.eventgallery-ajaxcart-internal .itemscount',
                    buttonDownSelector: '.eventgallery-ajaxcart-internal .toggle-down',
                    buttonUpSelector: '.eventgallery-ajaxcart-internal .toggle-up',
                    'removeUrl': "<?php echo JRoute::_("index.php?option=com_eventgallery&view=rest&task=removefromcart&format=raw", true); ?>".replace(/&amp;/g, '&'),
                    'add2cartUrl': "<?php echo JRoute::_("index.php?option=com_eventgallery&view=rest&task=add2cart&format=raw", true); ?>".replace(/&amp;/g, '&'),
                    'removeLinkTitle': "<?php echo JText::_('COM_EVENTGALLERY_CART_ITEM_REMOVE')?>",
                    'getCartUrl': "<?php echo JRoute::_("index.php?option=com_eventgallery&view=rest&task=getCart&format=raw", true); ?>".replace(/&amp;/g, '&')
                };

               var eventgalleryCart = new EventgalleryCart(options);

            });
            /* ]]> */
        </script>

        <div class="eventgallery-ajaxcart-internal eventgallery-ajaxcart well">

            <h2><?php echo JText::_('COM_EVENTGALLERY_CART') ?></h2>

            <div class="cart-items-container">
                <div class="cart-items"></div>
            </div>

            <div class="cart-summary btn-group">
                
                
                <button title="<?php echo JText::_('COM_EVENTGALLERY_CART_ITEMS_DESCRIPTION') ?>"class="btn"><span class="itemscount">0</span> <?php echo JText::_('COM_EVENTGALLERY_CART_ITEMS') ?>
                </button>
                <button title="<?php echo JText::_('COM_EVENTGALLERY_CART_BUTTON_CART_DESCRIPTION') ?>" onclick="document.location.href='<?php echo JRoute::_(
                    "index.php?option=com_eventgallery&view=cart"
                ); ?>'" class="btn"><i class="eventgallery-icon-tocart-small"></i></button>

                <button title="<?php echo JText::_('COM_EVENTGALLERY_CART_ITEMS_TOGGLE_DOWN') ?>" class="btn toggle-down" href="#"><i class="eventgallery-icon-arrow-down"></i></button>
                <button title="<?php echo JText::_('COM_EVENTGALLERY_CART_ITEMS_TOGGLE_UP') ?>" class="btn toggle-up" href="#"><i class="eventgallery-icon-arrow-up"></i></button>
                <button class="btn" data-rel="lightbo2" data-href="#mb_cart-help">?</button>
            </div>
            <div style="display:none">
                <div id="mb_cart-help">
                    <h2><?php echo JText::_('COM_EVENTGALLERY_CART_HELP_HEADLINE') ?></h2>
                    <?php echo JText::_('COM_EVENTGALLERY_CART_HELP_TEXT') ?>
                </div>
            </div>
            <div style="clear:both"></div>

        </div>
    <?php ENDIF; ?>

<?php ELSE: ?>
    <style type="text/css">
        .eventgallery-add2cart {
            display: none !important;
        }
    </style>
<?php ENDIF; ?>