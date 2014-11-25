<?php

/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


defined('_JEXEC') or die;

class EventgalleryHelpersCategories
{

    public static function addCategoryPathToPathway($pathway, $rootCatId, $catId, $menuItemId) {
        // add the category path

        if ( $catId != $rootCatId) {
            $options = Array();
            $categories = JCategories::getInstance('Eventgallery', $options);
            // get the category and the path for the current folder
            /**
             * @var JCategoryNode $category
             */
            $category = $categories->get($catId);
            $path = $category->getPath();


            // search the path for
            foreach($path as $pathItem) {
                $temp = explode(':', $pathItem);
                $currentCatId = $temp[0];
                $category = $categories->get($currentCatId);
                $pathway->addItem($category->title, JRoute::_('index.php?option=com_eventgallery&view=categories&catid='.(int)$currentCatId.'&Itemid='.$menuItemId));
            }
        }
    }
}