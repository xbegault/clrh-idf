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


// The class name must always be the same as the filename (in camel case)

class JFormFieldlocalizabletext extends JFormField
{

    //The field class must know its own type through the variable $type.
    protected $type = 'localizabletext';


    public function getInput()
    {
        $name = (string)$this->element['name'];
        $inputtype=(string)$this->element['inputtype'];
        $class = $this->element['class'] ? ' class=" lc_'.$name.' ' . (string) $this->element['class'] . '"' : ' class="lc_'.$name.'" ';
        $required = $this->required ? ' required="required" aria-required="true"' : '';

        $langs = JFactory::getLanguage()->getKnownLanguages();

        $result = "";

        $lt = json_decode($this->value);
        if ($lt == null) {
            $lt = new stdClass();
        }
        foreach($langs as $tag=>$lang) {
            $result .= '<div class="input-prepend" style="display:block; margin-bottom:10px; clear:both;">';
            $result .= '<span class="add-on">'.$tag .'</span>';
            $value = isset($lt->$tag)===true?$lt->$tag:'';
            if ($inputtype == 'textarea'){
                $result .= '<textarea data-tag="'.$tag.'" type="text" '.$class.'>'.$value.'</textarea>';
            } else {
                $result .= '<input data-tag="'.$tag.'" type="text" value="'.$value.'" '.$class.'>';
            }
            $result .= '</div>';
        }

        $hiddenField =  '<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . $required . '/>';

        // Initialize JavaScript field attributes.
        $script = '<script type="text/javascript">';

        // the script searches all the lc text fields and creates a json string for the hidden input field.
        $script .= '
                $$(".lc_'.$name.'").addEvent("blur", function() {

                    var data = {}
                    $$(".lc_'.$name.'").each(function(item){
                        data[item.get("data-tag")] = item.value;
                    });
                    $("'.$this->id.'").value = JSON.encode(data);
                });
        ';

        $script .= '</script>';

        return $result.$hiddenField.$script;
    }
}