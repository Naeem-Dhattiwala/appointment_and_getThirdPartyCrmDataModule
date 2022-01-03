<?php

/**
 *
 * @author        Viha Digital Commerce Team <naeem@vdcstore.com>.
 * @copyright     Copyright(c) 2021 Viha Digital Commerce
 * @link          https://www.vihadigitalcommerce.com/
 * @date          09/Sep/2021
 */

namespace Iram\Invoices\Model;

class InvoiceDetails extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'iram_invoices_invoicedetails';

    protected function _construct()
    {
        $this->_init('Iram\Invoices\Model\ResourceModel\InvoiceDetails');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function loadByMultiple($fields)
    {
        $collection = $this->getCollection();
        foreach ($fields as $key => $value) {
            $collection = $collection->addFieldToFilter($key, $value);
        }
        if ($collection->getFirstItem()) {
            return $collection->getFirstItem();
        } else {
            return $this;
        }
    }
}