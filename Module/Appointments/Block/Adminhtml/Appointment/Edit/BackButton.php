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

class BackButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }
}

