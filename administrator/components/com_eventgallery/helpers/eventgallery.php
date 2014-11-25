<?php
/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

$version =  new JVersion();

if (!$version->isCompatible('3.0')) {
    require_once(JPATH_BASE.'/components/com_eventgallery/helpers/legacy_layout.php');
    require_once(JPATH_BASE.'/components/com_eventgallery/helpers/legacy_base.php');
    require_once(JPATH_BASE.'/components/com_eventgallery/helpers/legacy_file.php');
    require_once(JPATH_BASE.'/components/com_eventgallery/helpers/legacy_sidebar.php');
    require_once(JPATH_BASE.'/components/com_eventgallery/helpers/legacy_helpercontent.php');
}

class EventgalleryHelper extends JHelperContent {
    public static function addSubmenu($vName = 'events') {
        EventgalleryHelpersEventgallery::addSubmenu($vName);
    }
}

class EventgalleryHelpersEventgallery extends JHelperContent
{
	
	public static function addSubmenu($vName = 'events')
	{
        $currentLayout = JRequest::getString('layout','default');
        

        JHtmlSidebar::addEntry(
            JText::_('COM_EVENTGALLERY_SUBMENU_EVENTGALLERY'),
            'index.php?option=com_eventgallery',
            $vName == 'eventgallery'
        );

        JHtmlSidebar::addEntry(
            '<hr>',
            '#',
            false);

		JHtmlSidebar::addEntry(
			JText::_('COM_EVENTGALLERY_SUBMENU_EVENTS'),
			'index.php?option=com_eventgallery&view=events',
			$vName == 'events' || $vName=='event' || $vName=='files'
		);

		JHtmlSidebar::addEntry(
			JText::_('COM_EVENTGALLERY_SUBMENU_COMMENTS'),
			'index.php?option=com_eventgallery&view=comments',
			$vName == 'comments' || $vName == 'comment');

		JHtmlSidebar::addEntry(
			JText::_('COM_EVENTGALLERY_SUBMENU_ORDERS'),
			'index.php?option=com_eventgallery&view=orders',
			$vName == 'orders' || $vName == 'order');

        JHtmlSidebar::addEntry(
            '<hr>',
            '#',
            false);

        JHtmlSidebar::addEntry(
            JText::_('COM_EVENTGALLERY_SUBMENU_CATEGORIES'),
            'index.php?option=com_categories&extension=com_eventgallery',
            $vName == 'categories');       

        JHtmlSidebar::addEntry(
            JText::_('COM_EVENTGALLERY_SUBMENU_WATERMARKS'),
            'index.php?option=com_eventgallery&view=watermarks',
            $vName == 'watermarks' || $vName == 'watermark');       

        JHtmlSidebar::addEntry(
            JText::_('COM_EVENTGALLERY_SUBMENU_IMAGETYPES'),
            'index.php?option=com_eventgallery&view=imagetypes',
            $vName == 'imagetypes' || $vName == 'imagetype');       

        JHtmlSidebar::addEntry(
			JText::_('COM_EVENTGALLERY_SUBMENU_IMAGETYPESETS'),
			'index.php?option=com_eventgallery&view=imagetypesets',
			$vName == 'imagetypesets' || $vName == 'imagetypeset');

 		JHtmlSidebar::addEntry(
			JText::_('COM_EVENTGALLERY_SUBMENU_ORDERSTATUSES'),
			'index.php?option=com_eventgallery&view=orderstatuses',
			$vName == 'orderstatuses' || $vName == 'orderstatuse');

        JHtmlSidebar::addEntry(
			JText::_('COM_EVENTGALLERY_SUBMENU_SURCHARGES'),
			'index.php?option=com_eventgallery&view=surcharges',
			$vName == 'surcharges' || $vName == 'surcharge');

		JHtmlSidebar::addEntry(
			JText::_('COM_EVENTGALLERY_SUBMENU_SHIPPINGMETHODS'),
			'index.php?option=com_eventgallery&view=shippingmethods',
			$vName == 'shippingmethods' || $vName == 'shippingmethod');

		JHtmlSidebar::addEntry(
			JText::_('COM_EVENTGALLERY_SUBMENU_PAYMENTMETHODS'),
			'index.php?option=com_eventgallery&view=paymentmethods',
			$vName == 'paymentmethods' || $vName == 'paymentmethod');

        JHtmlSidebar::addEntry(
            '<hr>',
            '#',
            false);

		JHtmlSidebar::addEntry(
			JText::_('COM_EVENTGALLERY_SUBMENU_DOCUMENTATION'),
			'index.php?option=com_eventgallery&view=documentation',
			$vName == 'documentation'
		);
	}
	
}