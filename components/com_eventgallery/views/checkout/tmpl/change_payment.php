<?php // no direct access

/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

/**
 * @var EventgalleryLibraryManagerPayment $paymentMgr
 */

$paymentMgr = EventgalleryLibraryManagerPayment::getInstance();
$methods = $paymentMgr->getMethods(true);
$currentMethod
    = $this->cart->getPaymentMethod() == NULL ? $paymentMgr->getDefaultMethod()
    : $this->cart->getPaymentMethod();


?>

<div class="control-group">
    <?php echo JText::_('COM_EVENTGALLERY_CART_CHECKOUT_FORM_PAYMENTMETHOD_LABEL') ?>
    <div class="controls">


        <select class="" name="paymentid">
            <?php FOREACH ($methods as $method): ?>
                <?php

                /**
                 * @var EventgalleryLibraryMethodsPayment $method
                 */
                $selected = "";

                if ($method->getId() == $currentMethod->getId()) {
                    $selected = 'selected = "selected"';
                }


                ?>
                <option <?php echo $selected; ?>
                    value="<?php echo $method->getId(); ?>"><?php echo $method->getDisplayName(); ?>
                    (<?php echo $method->getPrice(); ?>)
                </option>
            <?php ENDFOREACH ?>
        </select>
    </div>
</div>




