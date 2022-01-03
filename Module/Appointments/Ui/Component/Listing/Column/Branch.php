<?php

/**
 *
 * @author        Viha Digital Commerce Team <naeem@vdcstore.com>.
 * @copyright     Copyright(c) 2021 Viha Digital Commerce
 * @link          https://www.vihadigitalcommerce.com/
 * @date          09/Sep/2021
 */

namespace Iram\Appointments\Ui\Component\Listing\Column;

class Branch implements \Magento\Framework\Option\ArrayInterface
{
    protected $location;

    /**
     * Constructor
     * @param \Amasty\Storelocator\Model\Location $location
    */
    public function __construct(
        \Amasty\Storelocator\Model\Location $location,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->location = $location;
    }
    public function toOptionArray()
    {
        $Branchs = $this->location->getCollection();
        foreach ($Branchs as $key => $Branch) {
            $result[] = ['value' => $Branch->getId(), 'label' => $Branch->getName()];
        }
        return $result;
    }
}