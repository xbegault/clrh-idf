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

class EventgalleryModelOrderstatus extends JModelAdmin
{
    protected $text_prefix = 'COM_EVENTGALLERY';

    public function getTable($type = 'orderstatus', $prefix = 'Table', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param array $data An optional array of data for the form to interogate.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     * @return JForm A JForm object on success, false on failure
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Initialise variables.

        // Get the form.
        $form = $this->loadForm('com_eventgallery.orderstatus', 'orderstatus', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }



        if ( $form->getValue('id')!=null ) {
            $orderstatus = new EventgalleryLibraryOrderstatus($form->getValue('id'));
            if ($orderstatus->isSystemManaged()) {
                $form->setFieldAttribute('name', 'required', 'false');
                $form->setFieldAttribute('name', 'disabled', 'true');
            }
        }

        // the name field is required if we create a new order status
        if( !isset($data['id'])) {
            $form->setFieldAttribute('name', 'required', 'true');
        }

        return $form;
    }

    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_eventgallery.edit.orderstatus.data', array());

        if (empty($data))
        {
            $data = $this->getItem();
            // Prime some default values.
            if ($this->getState('orderstatus.id') == 0)
            {
                $app = JFactory::getApplication();
                $data->set('id', $app->input->get('id'));
            }
        } else {
            if (isset($data['id']) ) {
                $orderstatus = new EventgalleryLibraryOrderstatus($data['id']);
                $data['name'] = $orderstatus->getName();
            }
        }

		if (method_exists($this, 'preprocessData')) {
        	$this->preprocessData('com_eventgallery.orderstatus', $data);
		}
		
        return $data;
    }

     public function setDefault($pks, $value) {

        $id = $pks[0];
        $orderStatus = new EventgalleryLibraryOrderstatus($id);

        if ($orderStatus->isSystemManaged()) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EVENTGALLERY_ORDERSTATUS_EDIT_SYSTEMMANAGED_ERROR'), 'error');
            return false;
        }

        if (!$orderStatus->getType()==EventgalleryLibraryOrderstatus::TYPE_ORDER) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_EVENTGALLERY_ORDERSTATUS_SETDEFAULT_FOR_NONORDER_ERROR'), 'error');
            return false;
        }

        if ($value==1) {

            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->update('#__eventgallery_orderstatus');
            $query->set($db->quoteName('default') . ' = 0');
            $query->where('type='.$db->quote($orderStatus->getType()));

            $db->setQuery($query);
            $db->execute();

            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->update('#__eventgallery_orderstatus');
            $query->set($db->quoteName('default') . ' = 1');
            $query->where('type='.$db->quote($orderStatus->getType()));
            $query->where('id='.$db->quote($id));

            $db->setQuery($query);
            $db->execute();

        }
        return true;

    }

    public function delete(&$pks) {

        $newPks = array();

        foreach($pks as $pk) {
            $orderstatus = new EventgalleryLibraryOrderstatus($pk);
            if (!$orderstatus->isSystemManaged()) {
                $newPks[] = $pk;
            }
        }


        if (!parent::delete($newPks)) {
            return false;
        }


        foreach($newPks as $pk) {

            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->update('#__eventgallery_order');
            $query->set('orderstatusid = null');
            $query->where('orderstatusid = '.$db->quote($pk));
            $db->setQuery($query);
            $db->execute();

            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->update('#__eventgallery_order');
            $query->set('paymentstatusid = null');
            $query->where('paymentstatusid = '.$db->quote($pk));
            $db->setQuery($query);
            $db->execute();

            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->update('#__eventgallery_order');
            $query->set('shippingstatusid = null');
            $query->where('shippingstatusid = '.$db->quote($pk));
            $db->setQuery($query);
            $db->execute();
        }

        return true;
    }


}
