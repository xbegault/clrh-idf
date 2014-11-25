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
<?php IF ($this->folder->isCartable() && $this->params->get('use_cart',1)==1): ?><a href="#"title="<?php echo JText::_(
                        'COM_EVENTGALLERY_CART_ITEM_ADD2CART'
                    ) ?>" class="button-add2cart eventgallery-add2cart btn btn-primary"data-id="folder=<?php echo
                        $this->entry->getFolderName() . "&file=" . $this->entry->getFileName() ?>"><i class="big"></i></a><?php ENDIF
                    ?><?php IF ($this->folder->isCartable() && $this->params->get('show_cart_connector', 0) == 1):?><a
                        href="<?php echo EventgalleryHelpersCartconnector::getLink(
                            $this->entry->getFolderName(), $this->entry->getFileName()
                        ); ?>" class="button-cart-connector"
                        title="<?php echo JText::_('COM_EVENTGALLERY_CART_CONNECTOR') ?>"
                        data-folder="<?php echo $this->entry->getFolderName() ?>" data-file="<?php echo $this->entry->getFileName(); ?>"><i
                                class="big"></i></a><?php ENDIF 
                        ?>