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
?><a class="event-thumbnail <?php if (isset($this->cssClass)) {echo $this->cssClass;}?>" href="<?php echo $this->entry->getImageUrl(null, null, true); ?>"
   title="<?php echo htmlspecialchars($this->entry->getPlainTextTitle($this->params->get('show_image_filename',0)==1, $this->params->get('show_exif','1')=='1'), ENT_COMPAT, 'UTF-8') ?>"
   data-title="<?php echo rawurlencode($this->entry->getLightBoxTitle($this->params->get('show_image_filename',0)==1, $this->params->get('show_exif','1')=='1')) ?>"
   rel="lightbo2[gallery<?php echo $this->params->get('use_fullscreen_lightbox', 0) == 1 ? 'fullscreen' : ''; ?>]"><?php echo $this->entry->getLazyThumbImgTag(50, 50, false, false); ?>
   <?php echo $this->loadSnippet('event/inc/thumbs_content'); ?>
</a><?php echo $this->loadSnippet('event/inc/icon_add2cart'); ?><?php echo $this->loadSnippet('event/inc/icon_socialsharing'); ?>
