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

<div class="eventgallery-checkout-address">

    <fieldset class="userdata-fieldset">
        <?php foreach ($this->userdataform->getFieldset() as $field): ?>
            <div class="control-group">
                <?php if (!$field->hidden): ?>
                    <?php echo $field->label; ?>
                <?php endif; ?>
                <div class="controls">
                    <?php echo $field->input; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </fieldset>
    <hr>

    <fieldset class="billing-address-fieldset">
        <?php foreach ($this->billingform->getFieldset() as $field): ?>
            <div class="control-group">
                <?php if (!$field->hidden): ?>
                    <?php echo $field->label; ?>
                <?php endif; ?>
                <div class="controls">
                    <?php echo $field->input; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </fieldset>

    <hr>

    <fieldset class="ship-to_different-address-fieldset">
        <div class="control-group">
            <?php echo JText::_('COM_EVENTGALLERY_CART_CHECKOUT_FORM_SHIPTODIFFERENTADDRESS') ?>
            <?php
            $checkF = '';
            $checkT = '';
            if ($this->cart->getShippingAddress() == NULL
                || $this->cart->getBillingAddress() == NULL
                || $this->cart->getShippingAddress()->getId() == $this->cart->getBillingAddress()->getId()
            ) {
                $checkF = ' checked="checked" ';
            } else {
                $checkT = ' checked="checked" ';
            }
            ?>
            <div class="controls">
                <?php echo JText::_('COM_EVENTGALLERY_CART_CHECKOUT_FORM_SHIPTODIFFERENTADDRESS_FALSE') ?>
                <input autocomplete="off" type="radio" id="shiptodifferentaddress-false" name="shiptodifferentaddress"
                       value="false" <?php echo $checkF; ?>>
                <?php echo JText::_('COM_EVENTGALLERY_CART_CHECKOUT_FORM_SHIPTODIFFERENTADDRESS_TRUE') ?>
                <input autocomplete="off" type="radio" id="shiptodifferentaddress-true" class="shiptodifferentaddress"
                       name="shiptodifferentaddress" value="true" <?php echo $checkT; ?>>
            </div>
        </div>
    </fieldset>
    <hr>

    <fieldset class="shipping-address-fieldset">
        <?php foreach ($this->shippingform->getFieldset() as $field): ?>
            <div class="control-group">
                <?php if (!$field->hidden): ?>
                    <?php echo $field->label; ?>
                <?php endif; ?>
                <div class="controls">
                    <?php echo $field->input; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </fieldset>

</div>


<script type="text/javascript">
    window.addEvent("domready", function () {

        /**
        * fixes HTML5Fallback issue where the disabled property was not set in the right way
        */
        function refreshShippingAddressFields() {
            $$('.shipping-address').each(function(elem)  {
                console.log($(elem));
                elem.isRequired = !!($(elem).get("required"));
                elem.isDisabled = !!($(elem).get("disabled"));
            });
        }

        function disableRequiredForShipping() {
            $$('.shipping-address').set('disabled', 'disabled');       
            $$('.shipping-address-fieldset .is-required').removeClass('required');
            $$('.shipping-address-fieldset').hide();

            refreshShippingAddressFields();            
        }

        function enableReqiredForShipping() {
            $$('.shipping-address').removeProperty('disabled');
            $$('.shipping-address-fieldset .is-required').addClass('required');
            $$('.shipping-address-fieldset').show();
            refreshShippingAddressFields();

        }

        $('shiptodifferentaddress-false').addEvent('click', disableRequiredForShipping);
        $('shiptodifferentaddress-true').addEvent('click', enableReqiredForShipping);

        
        $$('.shipping-address-fieldset .required').addClass('is-required');
        
        if ($('shiptodifferentaddress-false').get('checked') == true) {
            disableRequiredForShipping();
        }

    });

</script>