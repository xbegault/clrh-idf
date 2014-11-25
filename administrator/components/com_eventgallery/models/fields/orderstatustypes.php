<?php

/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

// The class name must always be the same as the filename (in camel case)
class JFormFieldOrderstatustypes extends JFormField
{

    //The field class must know its own type through the variable $type.
    protected $type = 'orderstatustypes';
    protected $currentOrderstatusId = EventgalleryLibraryOrderstatus::TYPE_ORDER;


    public function getInput()
    {
        /**
         * @var EventgalleryLibraryManagerOrderstatus $orderstatusMgr
         */
        $orderstatusMgr = EventgalleryLibraryManagerOrderstatus::getInstance();

        $statuses = $orderstatusMgr->getOrderStatuses($this->currentOrderstatusId);

        if ($this->value == null) {
            $this->value = $orderstatusMgr->getDefaultOrderStatus($this->currentOrderstatusId)->getId();
        }


        $currentorderstatus = new EventgalleryLibraryOrderstatus($this->value);

        $return = "";
        $return .= '<select name='.$this->name.' id='.$this->id.'>';
        foreach($statuses as $orderstatus) {
            /**
             * @var EventgalleryLibraryOrderstatus $orderstatus
             */

            $this->value==$orderstatus->getId()?$selected='selected="selected"':$selected ='';

            $return .= '<option '.$selected.' value="'.$orderstatus->getId().'">'.$orderstatus->getDisplayName().'</option>';
        }
        $return .= "</select>";
        $return .= "<br><small>".$currentorderstatus->getDisplayName()."</small>";
        return $return;

    }
}