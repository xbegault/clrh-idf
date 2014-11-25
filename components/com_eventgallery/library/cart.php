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

/**
 * @property mixed cart
 */
class EventgalleryLibraryCart extends EventgalleryLibraryLineitemcontainer
{
    protected $_lineitemstatus = EventgalleryLibraryLineitem::TYPE_ORDER;
    /**
     * @var string
     */
    protected $_lineitemcontainer_table = "Cart";

    public function __construct($user_id)
    {
        $this->_user_id = $user_id;
        $this->_loadLineItemContainer();
        parent::__construct();
    }

    protected function _loadLineItemContainer()
    {

        $this->_lineitemcontainer = NULL;
        $this->_lineitems = NULL;

        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('c.*');
        $query->from('#__eventgallery_cart as c');
        $query->where('c.statusid is null');
        $query->where('c.userid = ' . $db->quote($this->_user_id));
        $db->setQuery($query);

        $this->_lineitemcontainer = $db->loadObject();

        if ($this->_lineitemcontainer == NULL) {

            $uuid = uniqid("", true);
            $uuid = base_convert($uuid,16,10);

            /**
             * @var TableCart $data
             */

            $query = $db->getQuery(true);
            $query->insert("#__eventgallery_cart");
            $query->columns("id");
            $query->values($db->quote($uuid));
            $db->setQuery($query);
            $db->execute();

            $data = JTable::getInstance('cart', 'Table');
            $data->userid = $this->_user_id;
            $data->id=$uuid;

            $this->_lineitemcontainer = $this->store((array)$data, 'Cart');

        }

        $this->_loadLineItems();
        $this->_loadServiceLineItems();
    }

    /**
     * @param $lineitemid
     *
     * @return EventgalleryLibraryImagelineitem|null
     */
    function cloneLineItem($lineitemid)
    {
        /**
         * @var EventgalleryLibraryImagelineitem $lineitem
         */
        $lineitem = $this->getLineItem($lineitemid);

        // do not clone a not existing line item.
        if ($lineitem == NULL) {
            return null;
        }

        /**
         * @var EventgalleryLibraryFactoryImagelineitem $imageLineItemFactory
         */
        $imageLineItemFactory = EventgalleryLibraryFactoryImagelineitem::getInstance();
        $newLineitem = $imageLineItemFactory->copyLineItem($this->getId(), $lineitem);

        $newLineitem->setQuantity(1);

        $this->_updateLineItemContainer();

        return $newLineitem;
    }

    /**
     * adds an image to the cart and checks if this action is actually allowed
     */

    function addItem($foldername, $filename, $count = 1, $typeid = NULL)
    {

        if ($filename == NULL || $foldername == NULL) {
            throw new Exception("can't add item with invalid file or folder name");
        }

        /**
         * @var EventgalleryLibraryManagerFile $fileMgr
         */
        $fileMgr = EventgalleryLibraryManagerFile::getInstance();
        $file = $fileMgr->getFile($foldername, $filename);


        /* security check BEGIN */
        if (!$file->isPublished()) {
            throw new Exception("the item you try to add is not published.");
        }

        if (!$file->getFolder()->isCartable()) {
            throw new Exception("the item you try to add is not cartable.");
        }

        if (!$file->getFolder()->isVisible()) {
            throw new Exception("the item you try to add is not visible for you. You might want to login first.");
        }

        if (!$file->getFolder()->isAccessible()) {
            throw new Exception("the item you try to add is not accessible. You might need to enter a password to unlock the folder first.");
        }

        /* check of the folder allows the type id. take the first type if not specific type was given. */

        /*@var EventgalleryLibraryImagetype */
        $imageType = NULL;

        if ($typeid == NULL) {
            $imageType = $file->getFolder()->getImageTypeSet()->getDefaultImageType();
        } else {
            $imageType = $file->getFolder()->getImageTypeSet()->getImageType($typeid);
        }

        if ($imageType == NULL) {
            throw new Exception("the image type you specified for the new item is invalid. Reason for this can be that there is not image type set, no image type set image type assignments or the image type set does not contain the image type");
        }

        /* security check END */

        /**
         * @var EventgalleryLibraryFactoryImagelineitem $imageLineItemFactory
         */
        $imageLineItemFactory = EventgalleryLibraryFactoryImagelineitem::getInstance();


        $lineitem = $this->getLineItemByFileAndType($foldername, $filename, $typeid);

        if ($lineitem == null) {
            $lineitem = $imageLineItemFactory->createLineitem($this->getId(), $foldername, $filename, $typeid, $count);
        } else {
            $lineitem->setQuantity($lineitem->getQuantity()+$count);
        }

        $this->_updateLineItemContainer();

        return $lineitem;

    }

    /**
     * tries to find a line item in the database
     *
     * @param $foldername
     * @param $filename
     * @param $imagetypeid
     *
     * @return null|EventgalleryLibraryImagelineitem
     */
    public function getLineItemByFileAndType($foldername, $filename, $imagetypeid)
    {
        foreach($this->getLineItems() as $lineitem) {
            /**
             * @var EventgalleryLibraryImagelineitem $lineitem
             */
            if ($lineitem->getFolderName() == $foldername && $lineitem->getFileName()==$filename && $lineitem->getImageType()->getId()==$imagetypeid) {
                return $lineitem;
            }
        }
        return null;
    }

    /**
     * @param int $statusid
     */
    public function setStatus($statusid)
    {
        $this->_lineitemcontainer->statusid = $statusid;
        $this->_storeLineItemContainer();
    }

    /**
     * @return int
     */
    public function getStatus() {
        return $this->_lineitemcontainer->statusid;
    }

}
