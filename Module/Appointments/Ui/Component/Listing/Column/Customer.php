<?php

/**
 *
 * @author        Viha Digital Commerce Team <naeem@vdcstore.com>.
 * @copyright     Copyright(c) 2021 Viha Digital Commerce
 * @link          https://www.vihadigitalcommerce.com/
 * @date          09/Sep/2021
 */

namespace Iram\Appointments\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Customer\Model\CustomerFactory;

class Customer extends Column
{
    protected $customerFactory;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     * @param UserFactory $userFactory
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = [],
        CustomerFactory $customerFactory
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->customerFactory = $customerFactory;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if ($item['customer_id'] != '') {
                    $Collection = $this->customerFactory->create()->getCollection()->addFieldToFilter('entity_id',$item['customer_id']);
                    foreach ($Collection as $key => $value) {
                        $cname  = $value->getFirstname() . ' ' . $value->getLastname(); 
                    }
                    $item['customer_id'] = $cname;
                }
            }
        }
        return $dataSource;
    }

    /**
     * @param $customerId
     * @return string
     */
    private function getCustomerName()
    {
        $customer = $this->customerFactory->create()->getCollection()->addFieldToFilter('entity_id',$customerId);
        return $customer;
    }
}