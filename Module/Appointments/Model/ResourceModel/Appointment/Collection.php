<?php

/**
 *
 * @author        Viha Digital Commerce Team <naeem@vdcstore.com>.
 * @copyright     Copyright(c) 2021 Viha Digital Commerce
 * @link          https://www.vihadigitalcommerce.com/
 * @date          09/Sep/2021
 */

namespace Iram\Appointments\Model\ResourceModel\Appointment;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'appointment_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Iram\Appointments\Model\Appointment::class,
            \Iram\Appointments\Model\ResourceModel\Appointment::class
        );
    }
}
