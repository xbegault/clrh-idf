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
 * @var EventgalleryLibraryManagerShipping $shippingMgr
 */

$shippingMgr = EventgalleryLibraryManagerShipping::getInstance();

$methods = $shippingMgr->getMethods(true);
$currentMethod
    = $this->cart->getShippingMethod() == NULL ? $shippingMgr->getDefaultMethod()
    : $this->cart->getShippingMethod();


?>

<div class="control-group">
    <?php echo JText::_('COM_EVENTGALLERY_CART_CHECKOUT_FORM_SHIPPINGMETHOD_LABEL') ?>
    <div class="controls">


        <select class="" name="shippingid">
            <?php FOREACH ($methods as $method): ?>


                <?php
                /**
                 * @var EventgalleryLibraryMethodsShipping $method
                 */
                $selected = "";

                if ($method->getId() == $currentMethod->getId()) {
                    $selected = 'selected = "selected"';
                }

                $disabled = "";

                if ($method->isEligible($this->cart)==false ) {
                    $disabled = 'disabled="disabled"';
                    $selected = "";
                }

                ?>
                <option <?php echo $selected; ?> <?php echo $disabled; ?>
                    value="<?php echo $method->getId(); ?>"><?php echo $method->getDisplayName(); ?>
                    (<?php echo $method->getPrice(); ?>)
                </option>
            <?php ENDFOREACH ?>
        </select>
    </div>
</div>

