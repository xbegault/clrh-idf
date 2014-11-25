<?php
/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

//jimport( 'joomla.application.component.helper' );

class EventgalleryLibraryDatabaseObject extends JObject
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * @param array  $data
     * @param string $table the JTable name suffix
     *
     * @return bool|JTable
     * @throws Exception
     */
    public function store($data, $table)
    {
        if ($data == null) {
            throw new Exception("'can't store something without data for table $table");
        }
        $data = $data ? $data : JRequest::get('post');
        if (isset($data['table'])) {
            throw new Exception('Data should not contain table attribute for security reasons.');
        }

        $row = JTable::getInstance($table, 'Table');

        $date = date("Y-m-d H:i:s");

        // Bind the form fields to the table
        if (!$row->bind($data)) {
            return false;
        }

        $row->modified = $date;

        if (!$row->created) {
            $row->created = $date;
        }

        // Make sure the record is valid
        if (!$row->check()) {
            return false;
        }

        if (!$row->store()) {
            return false;
        }

        return $row;

    }


}