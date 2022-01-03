<?php
declare(strict_types=1);

namespace Iram\Invoices\Model\ResourceModel\Invoice;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'invoice_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Iram\Invoices\Model\Invoice::class,
            \Iram\Invoices\Model\ResourceModel\Invoice::class
        );
    }
}

