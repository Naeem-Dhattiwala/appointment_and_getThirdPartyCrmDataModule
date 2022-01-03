<?php
declare(strict_types=1);

namespace Iram\Invoices\Model\ResourceModel\InvoiceDetails;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'invoicedetails_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Iram\Invoices\Model\InvoiceDetails::class,
            \Iram\Invoices\Model\ResourceModel\InvoiceDetails::class
        );
    }
}

