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


class TableOrder extends JTable
{

    public $id;
    public $documentno;
    public $userid;
    public $email;
    public $phone;
    public $statusid;
    public $subtotal;
    public $subtotalcurrency;
    public $total;
    public $totalcurrency;
    public $surchargeid;
    public $paymentmethodid;
    public $shippingmethodid;
    public $billingaddressid;
    public $shippingaddressid;
    public $message;
    public $modified;
    public $created;

    public $orderstatusid;
    public $paymentstatusid;
    public $shippingstatusid;

    public $surchargetotal;
    public $surchargetotalcurrency;

    public $paymenttotal;
    public $paymenttotalcurrency;

    public $shippingtotal;
    public $shippingtotalcurrency;



    /**
     * Constructor
     * @param JDatabaseDriver $db
     */

	function __construct( &$db ) {
		parent::__construct('#__eventgallery_order', 'id', $db);
	}

    public function store($updateNulls = false) {
        $this->modified = date("Y-m-d H:i:s");
        return parent::store($updateNulls);
    }
}
