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
class JFormFieldImagetypes extends JFormField
{

    //The field class must know its own type through the variable $type.
    protected $type = 'imagetypes';


    public function getInput()
    {
        /**
         * @var EventgalleryLibraryManagerImagetype $imagetypeMgr
         */
        $imagetypeMgr = EventgalleryLibraryManagerImagetype::getInstance();

        $imagetypes = $imagetypeMgr->getImageTypes(false);

        $id = $this->form->getField('id')->value;

        $imagetypeset = null;
        if ($id!=0) {
            $imagetypeset = new EventgalleryLibraryImagetypeset($id);
        }

        /**
         * @var EventgalleryLibraryImagetype $imagetype
         */

        $return  = '<select multiple name="'.$this->name.'" id="'.$this->id.'">';
        if ($imagetypeset != null) {
            foreach($imagetypeset->getImageTypes() as $imagetype) {
                $return .= '<option selected="selected" value="'.$imagetype->getId().'">'.$imagetype->getName().'</option>';
            }
        }

        foreach($imagetypes as $imagetype) {


            if ($imagetypeset != null && $imagetypeset->getImageType($imagetype->getId())!=null){
                continue;
            }
            $return .= '<option value="'.$imagetype->getId().'">'.$imagetype->getName().'</option>';
        }
        $return .= "</select>";        

        return $return;

    }
}