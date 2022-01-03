<?php
declare(strict_types=1);

namespace Iram\Invoices\Model\ResourceModel;

class Invoice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('iram_invoices_invoice', 'invoice_id');
    }
}

