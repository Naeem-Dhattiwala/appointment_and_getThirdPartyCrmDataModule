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

class DeleteButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getModelId()) {
            $data = [
                'label' => __('Delete Appointment'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * Get URL for delete button
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['appointment_id' => $this->getModelId()]);
    }
}

