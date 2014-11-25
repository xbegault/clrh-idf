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

<div class="eventgallery-checkout eventgallery-change-page">
    <h1><?php echo JText::_('COM_EVENTGALLERY_CART_CHECKOUT_CHANGE_HEADLINE') ?></h1>
    <?php echo JText::_('COM_EVENTGALLERY_CART_CHECKOUT_CHANGE_TEXT') ?>&nbsp;
    <!--<a class="" href="<?php echo JRoute::_("index.php?option=com_eventgallery&view=cart") ?>"><?php echo JText::_('COM_EVENTGALLERY_CART')?> <i class="eventgallery-icon-arrow-right"></i></a>-->
    <form action="<?php echo JRoute::_("index.php?option=com_eventgallery&view=checkout&task=saveChanges") ?>"
          method="post" class="form-validate form-horizontal checkout-form">
        <fieldset>
            <?php echo $this->loadTemplate('payment'); ?>
            <?php echo $this->loadTemplate('shipping'); ?>
        </fieldset>
        <?php echo $this->loadTemplate('address'); ?>
        <fieldset>
            <div class="form-actions">
                <input name="saveChanges" type="submit" class="validate btn"
                       value="<?php echo JText::_('COM_EVENTGALLERY_CART_CHECKOUT_CHANGE_FORM_SAVE') ?>"/>
                <input name="continue" type="submit" class="validate btn btn-primary"
                       value="<?php echo JText::_('COM_EVENTGALLERY_CART_CHECKOUT_CHANGE_FORM_CONTINUE') ?>"/>
            </div>
        </fieldset>
        <?php echo JHtml::_('form.token'); ?>
    </form>

</div>

<?php echo $this->loadSnippet('footer_disclaimer'); ?>
