<?php

/**
 *
 * @author        Viha Digital Commerce Team <naeem@vdcstore.com>.
 * @copyright     Copyright(c) 2021 Viha Digital Commerce
 * @link          https://www.vihadigitalcommerce.com/
 * @date          09/Sep/2021
 */

namespace Iram\Appointments\Block\Adminhtml\Appointment\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveAndContinueButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save and Continue Edit'),
            'class' => 'save',
            'data_attribute' => [
                'mage-init' => [
                    'button' => ['event' => 'saveAndContinueEdit'],
                ],
            ],
            'sort_order' => 80,
        ];
    }
}

