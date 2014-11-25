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


class TableFolder extends JTable
{
    public $id = null;
    public $folder = null;
    public $picasakey = null;
    public $foldertags = null;
    public $date = null;
    public $description = null;
    public $published = null;
    public $text = null;
    public $hits = null;
    public $userid = null;
    public $ordering = null;
    public $password = null;
    public $cartable = null;
    public $imagetypesetid = null;
    public $watermarkid = null;
    public $modified = null;
    public $created = null;
    public $usergroupids = null;
    public $attribs = null;

    /**
     * Constructor
     * @param JDatabaseDriver $db
     */
	function TableFolder($db) {
		parent::__construct('#__eventgallery_folder', 'id', $db);
	}	
	

}
