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

<div class="eventgallery-thumbnails eventgallery-imagelist thumbnails">
    <?php foreach ($this->entries as $entry) : /** @var EventgalleryLibraryFile $entry */ ?>        
     
	        <div class="thumbnail-container">

	            <?php $this->showContent=true; $this->entry=$entry; $this->cssClass="thumbnail"; echo $this->loadSnippet('event/inc/thumb'); ?>	            
	        </div>
        
    <?php endforeach ?>
    <div style="clear: both"></div>
</div>