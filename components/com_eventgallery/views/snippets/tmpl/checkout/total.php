<?php // no direct access

/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');
?>

<div class="cart-summary">
    <div class="subtotal">
        <div class="subtotal-headline"><?php echo JText::_('COM_EVENTGALLERY_CART_SUBTOTAL') ?></div>
		<span class="subtotal">
            <?php echo $this->lineitemcontainer->getSubTotal(); ?>
		</span>
    </div>
    <?php IF ($this->lineitemcontainer->getSurcharge() != NULL): ?>

        <div class="surcharge">
            <div class="surcharge-headline"><?php echo $this->lineitemcontainer->getSurcharge()->getDisplayName(); ?></div>
		<span class="surcharge">
            <?php echo $this->lineitemcontainer->getSurcharge()->getPrice(); ?>
		</span>
        </div>
    <?php ENDIF ?>
    <?php IF ($this->lineitemcontainer->getShippingMethod() != NULL): ?>
        <div class="surcharge">
            <div class="surcharge-headline"><?php echo $this->lineitemcontainer->getShippingMethod()->getDisplayName(); ?>
                <?php IF ($this->edit == true) :?>
                    <a href="<?php echo JRoute::_(
                        "index.php?option=com_eventgallery&view=checkout&task=change"
                    ) ?>">(<?php echo JText::_('COM_EVENTGALLERY_CART_CHECKOUT_REVIEW_FORM_CHANGE') ?>)</a>
                <?php ENDIF ?>
            </div>
		<span class="surcharge">
            <?php echo $this->lineitemcontainer->getShippingMethod()->getPrice(); ?>
		</span>
        </div>
    <?php ENDIF ?>
    <?php IF ($this->lineitemcontainer->getPaymentMethod() != NULL): ?>
        <div class="surcharge">
            <div class="surcharge-headline"><?php echo $this->lineitemcontainer->getPaymentMethod()->getDisplayName(); ?>
                <?php IF ($this->edit == true) :?>
                    <a href="<?php echo JRoute::_(
                        "index.php?option=com_eventgallery&view=checkout&task=change"
                    ) ?>">(<?php echo JText::_('COM_EVENTGALLERY_CART_CHECKOUT_REVIEW_FORM_CHANGE') ?>)</a>
                <?php ENDIF ?>
            </div>
		<span class="surcharge">
            <?php echo $this->lineitemcontainer->getPaymentMethod()->getPrice(); ?>
		</span>
        </div>
    <?php ENDIF ?>
    <div class="total ">
        <div class="total-headline"><?php echo JText::_('COM_EVENTGALLERY_CART_TOTAL') ?></div>
		<span class="total">
            <?php echo $this->lineitemcontainer->getTotal(); ?>
		</span>
		<span class="vat">
			<?php echo JText::sprintf('COM_EVENTGALLERY_CART_VAT_HINT_WITH_PLACEHOLDER', $this->lineitemcontainer->getTax()) ?>
		</span>
    </div>
</div>