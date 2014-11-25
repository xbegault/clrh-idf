<?php // no direct access
/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access'); ?>
<?php IF ($this->folder->isCartable()  && $this->params->get('use_cart', '1')==1): ?>
<style type="text/css">
    .imagetypeselection-container {
        margin-top: 20px;
        margin-bottom: 20px;
    }
</style>

<div class="imagetypeselection-container">
    <button class="btn btn-primary imagetypeselection-show"><?php echo JText::_('COM_EVENTGALLERY_PRODUCT_BUY_IMAGES') ?></button>

    <div class="well imagetypeselection" style="display:none">
        <?php include dirname(__FILE__).'/imagesetinformation.php'; ?>
        <div class="btn-group pull-right">
            <?PHP if ($this->params->get('use_sticy_imagetype_selection', 0) == 0):?>
            <a title="<?php echo JText::_('COM_EVENTGALLERY_PRODUCT_BUY_IMAGES_CLOSE_DESCRIPTION') ?>" class="btn btn-default imagetypeselection-hide"><?php echo JText::_('COM_EVENTGALLERY_PRODUCT_BUY_IMAGES_CLOSE') ?></a>       
            <?PHP ENDIF ?>
            <a title="<?php echo JText::_('COM_EVENTGALLERY_CART_BUTTON_CART_DESCRIPTION') ?>" class="btn btn-default" href="<?php echo JRoute::_('index.php?option=com_eventgallery&view=cart'); ?>"><?php echo JText::_('COM_EVENTGALLERY_CART_BUTTON_CART_LABEL') ?></a>
            <a title="<?php echo JText::_('COM_EVENTGALLERY_PRODUCT_BUY_IMAGES_ADD_ALL_DESCRIPTION') ?>"class="btn btn-primary eventgallery-add-all"><?php echo JText::_('COM_EVENTGALLERY_PRODUCT_BUY_IMAGES_ADD_ALL') ?></a>
        </div>
        <div class="help">
            <?php echo JText::_('COM_EVENTGALLERY_PRODUCT_BUY_IMAGES_HELP_SELECTION');?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>


<script type="text/javascript">


    window.addEvent("domready", function () {
    
        var imagetypeselection = $$('.imagetypeselection')[0];
        var imagetypeselectionShowButton = $$('.imagetypeselection-show')[0];        

        function closeImageTypeSelection(e) {
            if (e) {
                e.preventDefault();
            }
            imagetypeselection.hide()
            imagetypeselectionShowButton.show();
            $$('.eventgallery-add2cart').hide();
        }

        function openImageTypeSelection(e) {
            if (e) {
                e.preventDefault();
            }
            imagetypeselection.show();
            imagetypeselectionShowButton.hide();

            $$('.eventgallery-add2cart').show();
        }

        $$('.imagetypeselection-hide').addEvent('click', closeImageTypeSelection);
        $$('.imagetypeselection-show').addEvent('click', openImageTypeSelection); 
    
        <?php if ($this->params->get('use_sticy_imagetype_selection', 0) == 0):?>
            $$('.imagetypeselection-show').show();
            $$('.eventgallery-add2cart').hide();
        <?php ELSE: ?>
            $$('.imagetypeselection-show').hide();
            $$('.eventgallery-add2cart').show();
            openImageTypeSelection();
        <?PHP ENDIF ?>

       
    
    });

</script>
<?php ENDIF ?>