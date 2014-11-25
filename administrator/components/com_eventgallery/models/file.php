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

jimport( 'joomla.application.component.modeladmin' );
jimport( 'joomla.filesystem.file' );

class EventgalleryModelFile extends JModelAdmin
{
    protected $text_prefix = 'COM_EVENTGALLERY';

    public function getTable($type = 'file', $prefix = 'Table', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true) {
        return null;
    }

    /**
     * Method to delete record(s)
     *
     * @access    public
     * @param array $pks
     * @return    boolean    True on success
     */

    function delete(&$pks)
    {


        $row = $this->getTable();

        if (count( $pks ))
        {
            foreach($pks as $cid) {

                $query = $this->_db->getQuery(true)
                    ->select('*')
                    ->from($this->_db->quoteName('#__eventgallery_file'))
                    ->where('id=' . $this->_db->quote($cid));

                $this->_db->setQuery( $query );
                $data = $this->_db->loadObject();

                $path=JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'eventgallery'.DIRECTORY_SEPARATOR.JFile::makeSafe($data->folder).DIRECTORY_SEPARATOR ;
                $filename=JFile::makeSafe($data->file);
                $file = $path.$filename;

                if (file_exists($file) && !is_dir($file)) {
                    if (!unlink($file)) {
                        echo $file;
                        return false;
                    }
                }

                if (!$row->delete( $cid )) {
                    $this->setError( $row->getErrorMsg() );
                    return false;
                }



            }
        }
        return true;
    }



    function setCaption($pks, $caption, $title) {

        $this->setValue($pks, 'caption', $caption);
        $this->setValue($pks, 'title', $title);

        return true;

    }

    /**
     * @param array $pks the primary keys value
     * @param string $key the name of the column you want to change
     * @param string $value the name you want to set the value to.
     * @return bool success
     */
    protected function setValue($pks, $key, $value) {
        $table = $this->getTable();
        $pks = (array) $pks;
        $result = true;

        foreach ($pks as $i => $pk)
        {
            $table->reset();

            if ($table->load($pk))
            {
                $table->$key= $value;
                $table->store();
            }
            else
            {
                $this->setError($table->getError());
                unset($pks[$i]);
                $result = false;
            }
        }



        return $result;
    }

    public function allowComments($pks, $allowcomments)
    {
        return $this->setValue($pks, "allowcomments", $allowcomments);
    }
  

    public function isMainImageOnly($pks, $ismainimageonly)
    {
        return $this->setValue($pks, "ismainimageonly", $ismainimageonly);
    }


    public function isMainImage($pks, $ismainimage)
    {
        return $this->setValue($pks, "ismainimage", $ismainimage);
    }

    /**
    * resets the ordering values for a folder
    */
    public function clearOrdering($folderid) {
        $query = $this->_db->getQuery(true);
        $query->select('folder')->from('#__eventgallery_folder')->where('id='.$this->_db->quote($folderid));
        $this->_db->setQuery( $query );
        $folder = $this->_db->loadObject();


        $query = $this->_db->getQuery(true)
                    ->update('#__eventgallery_file')
                    ->set('ordering=0')
                    ->where('folder='.$this->_db->quote($folder->folder));


        $this->_db->setQuery( $query );
        $this->_db->execute();

    }

    /**
     * A protected method to get a set of ordering conditions.
     *
     * @param   JTable  $table  A record object.
     *
     * @return  array  An array of conditions to add to add to ordering queries.
     *
     * @since   1.6
     */
    protected function getReorderConditions($table)
    {
        $condition = array();
        $condition[] = 'folder = '.$this->_db->quote($table->folder);
        return $condition;
    }


}
