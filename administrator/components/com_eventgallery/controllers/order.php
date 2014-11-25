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

jimport( 'joomla.application.component.controllerform' );

class EventgalleryControllerOrder extends JControllerForm
{
    protected $view_list = 'orders';

    /**
     * Method to save a record.
     *
     * @param   string  $key     The name of the primary key of the URL variable.
     * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
     *
     * @return  boolean  True if successful, false otherwise.
     *
     * @since   12.2
     */
    public function save($key = null, $urlVar = null)
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app   = JFactory::getApplication();
        $lang  = JFactory::getLanguage();
        /**
         * @var EventgalleryModelOrder $model
         * @var JTable $table
         */
        $model = $this->getModel();
        $table = $model->getTable();
        $data  = $app->input->post->get('jform', array(), 'array');
        $checkin = property_exists($table, 'checked_out');
        $context = "$this->option.edit.$this->context";
        $task = $this->getTask();

        // Determine the name of the primary key for the data.
        if (empty($key))
        {
            $key = $table->getKeyName();
        }

        // To avoid data collisions the urlVar may be different from the primary key.
        if (empty($urlVar))
        {
            $urlVar = $key;
        }

        $recordId = $app->input->getString($urlVar);

        if (!$this->checkEditId($context, $recordId))
        {
            // Somehow the person just went to the form and tried to save it. We don't allow that.
            $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $recordId));
            $this->setMessage($this->getError(), 'error');

            $this->setRedirect(
                JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend(), false
                )
            );

            return false;
        }

        // Populate the row id from the session.
        $data[$key] = $recordId;

        // The save2copy task needs to be handled slightly differently.
        if ($task == 'save2copy')
        {
            // Check-in the original row.
            if ($checkin && $model->checkin($data[$key]) === false)
            {
                // Check-in failed. Go back to the item and display a notice.
                $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
                $this->setMessage($this->getError(), 'error');

                $this->setRedirect(
                    JRoute::_(
                        'index.php?option=' . $this->option . '&view=' . $this->view_item
                        . $this->getRedirectToItemAppend($recordId, $urlVar), false
                    )
                );

                return false;
            }

            // Reset the ID and then treat the request as for Apply.
            $data[$key] = 0;
            $task = 'apply';
        }

        // Access check.
        if (!$this->allowSave($data, $key))
        {
            $this->setError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
            $this->setMessage($this->getError(), 'error');

            $this->setRedirect(
                JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend(), false
                )
            );

            return false;
        }

        // Validate the posted data.
        // Sometimes the form needs some posted data, such as for plugins and modules.
        $form = $model->getForm($data, false);

        if (!$form)
        {
            $app->enqueueMessage($model->getError(), 'error');

            return false;
        }

        // Test whether the data is valid.
        $validData = $model->validate($form, $data);

        // Check for validation errors.
        if ($validData === false)
        {
            // Get the validation messages.
            $errors = $model->getErrors();

            // Push up to three validation messages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
            {
                if ($errors[$i] instanceof Exception)
                {
                    /**
                     * @var Exception $exception
                     */
                    $exception = $errors[$i];
                    $app->enqueueMessage($exception->getMessage(), 'warning');
                }
                else
                {
                    $app->enqueueMessage($errors[$i], 'warning');
                }
            }

            // Save the data in the session.
            $app->setUserState($context . '.data', $data);

            // Redirect back to the edit screen.
            $this->setRedirect(
                JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar), false
                )
            );

            return false;
        }

        if (!isset($validData['metadata']['tags']))
        {
            $validData['metadata']['tags'] = null;
        }

        // Attempt to save the data.
        if (!$model->save($validData))
        {
            // Save the data in the session.
            $app->setUserState($context . '.data', $validData);

            // Redirect back to the edit screen.
            $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
            $this->setMessage($this->getError(), 'error');

            $this->setRedirect(
                JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar), false
                )
            );

            return false;
        }

        // Save succeeded, so check-in the record.
        if ($checkin && $model->checkin($validData[$key]) === false)
        {
            // Save the data in the session.
            $app->setUserState($context . '.data', $validData);

            // Check-in failed, so go back to the record and display a notice.
            $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
            $this->setMessage($this->getError(), 'error');

            $this->setRedirect(
                JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar), false
                )
            );

            return false;
        }

        $this->setMessage(
            JText::_(
                ($lang->hasKey($this->text_prefix . ($recordId == 0 && $app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS')
                    ? $this->text_prefix
                    : 'JLIB_APPLICATION') . ($recordId == 0 && $app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS'
            )
        );

        // Redirect the user and adjust session state based on the chosen task.
        switch ($task)
        {
            case 'apply':
                // Set the record data in the session.
                $recordId = $model->getState($this->context . '.id');
                $this->holdEditId($context, $recordId);
                $app->setUserState($context . '.data', null);
                $model->checkout($recordId);

                // Redirect back to the edit screen.
                $this->setRedirect(
                    JRoute::_(
                        'index.php?option=' . $this->option . '&view=' . $this->view_item
                        . $this->getRedirectToItemAppend($recordId, $urlVar), false
                    )
                );
                break;

            case 'save2new':
                // Clear the record id and data from the session.
                $this->releaseEditId($context, $recordId);
                $app->setUserState($context . '.data', null);

                // Redirect back to the edit screen.
                $this->setRedirect(
                    JRoute::_(
                        'index.php?option=' . $this->option . '&view=' . $this->view_item
                        . $this->getRedirectToItemAppend(null, $urlVar), false
                    )
                );
                break;

            default:
                // Clear the record id and data from the session.
                $this->releaseEditId($context, $recordId);
                $app->setUserState($context . '.data', null);

                // Redirect to the list screen.
                $this->setRedirect(
                    JRoute::_(
                        'index.php?option=' . $this->option . '&view=' . $this->view_list
                        . $this->getRedirectToListAppend(), false
                    )
                );
                break;
        }

        // Invoke the postSave method to allow for the child class to access the model.
        $this->postSaveHook($model, $validData);

        return true;
    }

    public function edit($key = null, $urlVar = null)
    {
        $app   = JFactory::getApplication();
        /**
         * @var EventgalleryModelOrder $model
         * @var JTable $table
         */
        $model = $this->getModel();
        $table = $model->getTable();
        $cid   = $app->input->post->get('cid', array(), 'array');
        $context = "$this->option.edit.$this->context";

        // Determine the name of the primary key for the data.
        if (empty($key))
        {
            $key = $table->getKeyName();
        }

        // To avoid data collisions the urlVar may be different from the primary key.
        if (empty($urlVar))
        {
            $urlVar = $key;
        }

        // Get the previous record id (if any) and the current record id.
        $recordId =  (count($cid) ? $cid[0] : $app->input->getString($urlVar));
        $checkin = property_exists($table, 'checked_out');

        // Access check.
        if (!$this->allowEdit(array($key => $recordId), $key))
        {
            $this->setError(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
            $this->setMessage($this->getError(), 'error');

            $this->setRedirect(
                JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_list
                    . $this->getRedirectToListAppend(), false
                )
            );

            return false;
        }

        // Attempt to check-out the new record for editing and redirect.
        if ($checkin && !$model->checkout($recordId))
        {
            // Check-out failed, display a notice but allow the user to see the record.
            $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()));
            $this->setMessage($this->getError(), 'error');

            $this->setRedirect(
                JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar), false
                )
            );

            return false;
        }
        else
        {
            // Check-out succeeded, push the new record id into the session.
            $this->holdEditId($context, $recordId);
            $app->setUserState($context . '.data', null);

            $this->setRedirect(
                JRoute::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($recordId, $urlVar), false
                )
            );

            return true;
        }
    }

    public function resendmail($key = null, $urlVar = null) {

        JRequest::checkToken();
        $app = JFactory::getApplication();

        // Load the front end language
        $language = JFactory::getLanguage();
        $language->load('com_eventgallery' , JPATH_SITE.DIRECTORY_SEPARATOR.'components/com_eventgallery', $language->getTag(), true);
        $language->load('com_eventgallery' , JPATH_SITE.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'overrides', $language->getTag(), true, false);


        $orderid   = $app->input->post->get('id');

        /**
         * @var EventgalleryLibraryManagerOrder $orderMgr
         */
        $orderMgr = EventgalleryLibraryManagerOrder::getInstance();
        $order = $orderMgr->getOrderById($orderid);

        $config = JFactory::getConfig();
        $params = JComponentHelper::getParams('com_eventgallery');

        $sitename = $config->get('sitename');

        $this->setPath('view', JPATH_SITE.'/components/com_eventgallery/views/');

        $viewName = 'Mail';
        $viewPrefix = 'EventgalleryView';

        $db = JFactory::getDBO();
        $query = "SELECT template FROM #__template_styles WHERE client_id = 0 AND home = 1";
        $db->setQuery($query);
        $defaultemplate = $db->loadResult();

        $view = $this->getView($viewName, 'html', $viewPrefix, array(
            'site_template' => $defaultemplate,
            'layout'=>'confirm',
            'base_path' => JPATH_SITE.'/components/com_eventgallery',
            'template_path' => Array(
                    JPATH_SITE.'/components/com_eventgallery/views/'.strtolower($viewName).'/tmpl/',
                    JPATH_SITE.'/templates/'.$defaultemplate.'/html/com_eventgallery/'.strtolower($viewName).'/'
                )
            ));
        $view->set('order', $order);
        $view->set('params', $params);
        //$view->set
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
            $app->enqueueMessage($mailer->ErrorInfo, 'error');
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
            $this->setMessage(JText::_('COM_EVENTGALLERY_ORDER_RESEND_MAIL_FAILED'));
        } else {
            $this->setMessage(JText::_('COM_EVENTGALLERY_ORDER_RESEND_MAIL_SUCCESS'));
        }

        $this->setRedirect(
            JRoute::_(
                'index.php?option=' . $this->option . '&view=' . $this->view_item
                . $this->getRedirectToItemAppend($orderid, 'id'), false
            )
        );

        return true;
    }

}