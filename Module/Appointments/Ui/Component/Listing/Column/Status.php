<?php

/**
 *
 * @author        Viha Digital Commerce Team <naeem@vdcstore.com>.
 * @copyright     Copyright(c) 2021 Viha Digital Commerce
 * @link          https://www.vihadigitalcommerce.com/
 * @date          09/Sep/2021
 */

namespace Iram\Appointments\Ui\Component\Listing\Column;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Pending')],
            ['value' => 2, 'label' => __('Accepted')],
            ['value' => 3, 'label' => __('Rejected')],
            ['value' => 4, 'label' => __('Done')],
            ['value' => 5, 'label' => __('Missed')]
        ];
    }
}