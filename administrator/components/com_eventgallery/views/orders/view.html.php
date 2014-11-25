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

jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.pagination');

class EventgalleryViewOrders extends EventgalleryLibraryCommonView
{

    protected $items;
    protected $pagination;
    protected $state;
    /**
     * Display the view
     */
    public function display($tpl = null)
    {
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
                JError::raiseError(500, implode("\n", $errors));
                return false;
        }

        $this->addToolbar();
        EventgalleryHelpersEventgallery::addSubmenu('orders');      
        $this->sidebar = JHtmlSidebar::render();

        return parent::display($tpl);
    }

     protected function addToolbar() {
        JToolBarHelper::title(   JText::_( 'COM_EVENTGALLERY_ORDERS' ), 'generic.png' );
        JToolBarHelper::deleteList('Remove all selected Events?','orders.delete','Remove');

         // Build the active state filter options.
         $options = array();

         /**
          * @var EventgalleryLibraryManagerOrderstatus $orderstatusMgr
          * @var EventgalleryLibraryOrderstatus $orderstatus
          */
         $orderstatusMgr  = EventgalleryLibraryManagerOrderstatus::getInstance();

         /**
          * ORDER
          */
         $orderstatuses = $orderstatusMgr->getOrderStatuses(EventgalleryLibraryOrderstatus::TYPE_ORDER);

         foreach($orderstatuses as $orderstatus) {

             $options[] = JHtml::_('select.option', $orderstatus->getId(), $orderstatus->getDisplayName());
         }

         $options[] = JHtml::_('select.option', '*', 'JALL');

         JHtmlSidebar::addFilter(
             JText::_('COM_EVENTGALLERY_ORDER_ORDERSTATUS'),
             'filter_orderstatus',
             JHtml::_('select.options', $options, 'value', 'text', $this->state->get('filter.orderstatus'), true)
         );

         /**
          * PAYMENT
          */
         $orderstatuses = $orderstatusMgr->getOrderStatuses(EventgalleryLibraryOrderstatus::TYPE_PAYMENT);
         $options = array();
         foreach($orderstatuses as $orderstatus) {

             $options[] = JHtml::_('select.option', $orderstatus->getId(), $orderstatus->getDisplayName());
         }


         $options[] = JHtml::_('select.option', '*', 'JALL');

         JHtmlSidebar::addFilter(
             JText::_('COM_EVENTGALLERY_ORDER_PAYMENTSTATUS'),
             'filter_paymentstatus',
             JHtml::_('select.options', $options, 'value', 'text', $this->state->get('filter.paymentstatus'), true)
         );

         /**
          * SHIPPING
          */
         $orderstatuses = $orderstatusMgr->getOrderStatuses(EventgalleryLibraryOrderstatus::TYPE_SHIPPING);
         $options = array();
         foreach($orderstatuses as $orderstatus) {

             $options[] = JHtml::_('select.option', $orderstatus->getId(), $orderstatus->getDisplayName());
         }


         $options[] = JHtml::_('select.option', '*', 'JALL');

         JHtmlSidebar::addFilter(
             JText::_('COM_EVENTGALLERY_ORDER_SHIPPINGSTATUS'),
             'filter_shippingstatus',
             JHtml::_('select.options', $options, 'value', 'text', $this->state->get('filter.shippingstatus'), true)
         );

    }

    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     *
     * @since   3.0
     */
    protected function getSortFields()
    {
        return array(
            'documentno' => JText::_('JGRID_HEADING_ID'),
            'orderstatusid' => JText::_('COM_EVENTGALLERY_ORDER_ORDERSTATUS'),
            'paymentstatusid' => JText::_('COM_EVENTGALLERY_ORDER_PAYMENTSTATUS'),
            'shippingstatusid' => JText::_('COM_EVENTGALLERY_ORDER_SHIPPINGSTATUS'),
            'total' => JText::_('COM_EVENTGALLERY_ORDER_TOTAL')
        );
    }


}
