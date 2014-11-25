<?php
/**
 * @package     Sven.Bluege
 * @subpackage  com_eventgallery
 *
 * @copyright   Copyright (C) 2005 - 2013 Sven Bluege All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

class CheckoutController extends JControllerLegacy
{
    public function display($cachable = false, $urlparams = array())
    {
        parent::display(false, $urlparams);
    }

    /**
     * @param EventgalleryLibraryOrder $order
     *
     * @return mixed|string
     */
    protected  function _sendOrderConfirmationMail($order) {

        $config = JFactory::getConfig();
        $params = JComponentHelper::getParams('com_eventgallery');

        $sitename = $config->get('sitename');

        $view = $this->getView('Mail', 'html', 'EventgalleryView', array('layout'=>'confirm'));
        $view->set('order', $order);
        $view->set('params', $params);
        $body = $view->loadTemplate();

        $mailer = JFactory::getMailer();



        $config = JFactory::getConfig();


        $subject = JText::sprintf('COM_EVENTGALLERY_CART_CHECKOUT_ORDER_MAIL_CONFIRMATION_SUBJECT', $order->getBillingAddress()->getFirstName().' '.$order->getBillingAddress()->getLastName(), $order->getLineItemsTotalCount(), $order->getLineItemsCount());

        $mailer->setSubject(
            "$sitename - " . $subject
        );

        $mailer->isHTML(true);
        $mailer->Encoding = 'base64';
        $mailer->setBody($body);

        // Customer Mail
        $sender = array(
            $config->get( 'mailfrom' ),
            $config->get( 'fromname' ) );

        $mailer->setSender($sender);
        $mailer->addRecipient($order->getEMail(), $order->getBillingAddress()->getFirstName().' '.$order->getBillingAddress()->getLastName());

        $send = $mailer->Send();

        if ($send !== true) {
            return $mailer->ErrorInfo;
        }

        // Admin Mail

        $mailer->ClearAllRecipients();

        $sender = array(
            $order->getEMail(),
            $order->getBillingAddress()->getFirstName().' '.$order->getBillingAddress()->getLastName());

        $mailer->setSender($sender);

        $userids = JAccess::getUsersByGroup($params->get('admin_usergroup'));

        foreach ($userids as $userid) {
            $user = JUser::getInstance($userid);
            if ($user->sendEmail==1) {
                $mailadresses = JMailHelper::cleanAddress($user->email);
                $mailer->addRecipient($mailadresses);
            }
        }

        $send = $mailer->Send();

        if ($send !== true) {
            return $mailer->ErrorInfo;
        }

        return $send;


    }

    /**
     * just sets the layout for the confirm page
     *
     * @param bool  $cachable
     * @param array $urlparams
     */
    public function confirm($cachable = false, $urlparams = array())
    {
        JRequest::setVar('layout', 'confirm');
        $this->display($cachable, $urlparams);
    }

    /**
     * Just sets the layout for the change page
     *
     * @param bool  $cachable
     * @param array $urlparams
     */
    public function change($cachable = false, $urlparams = array())
    {
        JRequest::setVar('layout', 'change');
        $this->display($cachable, $urlparams);
    }

    public function saveChanges($cachable = false, $urlparams = array())
    {

        /* @var EventgalleryLibraryManagerCart $cartMgr */
        $cartMgr = EventgalleryLibraryManagerCart::getInstance();

        JRequest::checkToken();
        $errors = array();
        $errors = array_merge($errors, $cartMgr->updateShippingMethod());
        $errors = array_merge($errors, $cartMgr->updatePaymentMethod());
        $errors = array_merge($errors, $cartMgr->updateAddresses());
        $cartMgr->calculateCart();

        if (count($errors) > 0) {
            $msg = "";
            $app = JFactory::getApplication();

            /**
             * @var Exception $error
             */
            foreach ($errors as $error) {
                $this->setMessage($msg);
                $app->enqueueMessage($error->getMessage(), 'error');
            }

            $this->change($cachable, $urlparams);
        } else {
            $continue = JRequest::getString('continue', null);

            $msg = JText::_('COM_EVENTGALLERY_CART_CHECKOUT_CHANGES_STORED');
            if ($continue == null) {
                $this->setRedirect(
                    JRoute::_("index.php?option=com_eventgallery&view=checkout&task=change"), $msg, 'info'
                );
            } else {
                $this->setRedirect(JRoute::_("index.php?option=com_eventgallery&view=checkout"));
            }
        }
    }


    public function createOrder()
    {

        // switch to the change page.
        $continue = JRequest::getString('continue', null);

        if ($continue == null) {
            $this->setRedirect(JRoute::_("index.php?option=com_eventgallery&view=checkout&task=change"));
            return;
        }


        // Check for request forgeries.
        JRequest::checkToken();

        /* @var EventgalleryLibraryManagerCart $cartMgr */
        $cartMgr = EventgalleryLibraryManagerCart::getInstance();

        $cartMgr->calculateCart();

        $cart = $cartMgr->getCart();

        // if the cart is empty
        if ($cart->getLineItemsCount()==0) {
            $this->setRedirect(JRoute::_("index.php?option=com_eventgallery&view=cart"));
            return;
        }

        /* create order*/
        $orderMgr = new EventgalleryLibraryManagerOrder();

        #$order = $cart;
        $order = $orderMgr->createOrder($cart);

        /* send mail */
        $send = $this->_sendOrderConfirmationMail($order);

        $orderMgr->processOnOrderSubmit($order);



        if ($send !== true) {
            $msg = JText::_('COM_EVENTGALLERY_CART_CHECKOUT_ORDER_FAILED') . ' (' . $send . ')';
        } else {
            $msg = NULL; 
        }

        $this->setRedirect(JRoute::_("index.php?option=com_eventgallery&view=checkout&task=confirm"), $msg, 'info');

    }

    public function processPayment() {
       $methodid = JRequest::getString("paymentmethodid",null);
        /**
         * @var EventgalleryLibraryManagerPayment $methodMgr
         */

        $methodMgr = EventgalleryLibraryManagerPayment::getInstance();
        $method = $methodMgr->getMethod($methodid, false);
        if ($method != null) {
            $method->onIncomingExternalRequest();
        }


    }



}
