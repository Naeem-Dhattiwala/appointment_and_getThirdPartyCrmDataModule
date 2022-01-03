<?php

/**
 *
 * @author        Viha Digital Commerce Team <naeem@vdcstore.com>.
 * @copyright     Copyright(c) 2021 Viha Digital Commerce
 * @link          https://www.vihadigitalcommerce.com/
 * @date          09/Sep/2021
 */

namespace Iram\Appointments\Ui\Component\Listing\Column;

class Employee implements \Magento\Framework\Option\ArrayInterface
{
    protected $option;

    /**
     * Constructor
     * @param \Amasty\Storelocator\Model\Options $option
    */
    public function __construct(
        \Amasty\Storelocator\Model\Options $option,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->option = $option;
    }
    public function toOptionArray()
    {
        $Employee = $this->option->getCollection();
        foreach ($Employee as $key => $Employees) {
            $Emp = explode(',', substr($Employees->getOptions_serialized(), 1, -1));
            $result[] = ['value' => $Employees->getValue_id(), 'label' => substr($Emp[0], 1, -1)];
        }
        return $result;
    }
}