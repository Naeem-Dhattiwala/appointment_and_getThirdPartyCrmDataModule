<?php

/**
 *
 * @author        Viha Digital Commerce Team <naeem@vdcstore.com>.
 * @copyright     Copyright(c) 2021 Viha Digital Commerce
 * @link          https://www.vihadigitalcommerce.com/
 * @date          09/Sep/2021
 */

namespace Iram\Appointments\Model\Appointment\Attribute\Source;

class Customer extends \Magento\Backend\Block\Template\Context implements \Magento\Framework\Option\ArrayInterface 
{
    protected $_attribute;

    protected $request;

    protected $customerFactory;

    protected $appointmentFactory;

     /**
     * Constructor
     * @param \Amasty\Storelocator\Model\Attribute  $attribute
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Amasty\Storelocator\Model\Options $option
     * @param \Iram\Appointments\Model\AppointmentFactory $appointmentFactory
     */

     public function __construct(
        \Amasty\Storelocator\Model\Attribute $attribute,
        \Magento\Framework\App\Request\Http $request,
        \Iram\Appointments\Model\AppointmentFactory $appointmentFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->appointmentFactory = $appointmentFactory;
        $this->_attribute = $attribute;
        $this->request = $request;
        $this->customerFactory = $customerFactory;
    }

    /**
     * Retrieve options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        $Appointment_id = $this->request->getParam('appointment_id', false);
        $Appointmentdata = $this->appointmentFactory->create()->getCollection()
                          ->addFieldToFilter('appointment_id',$Appointment_id);
        foreach ($Appointmentdata as $key => $Appointmentvalue) {
            $Customer_id = $Appointmentvalue->getCustomer_id();
        }
        $Collection = $this->customerFactory->create()->getCollection()->addFieldToFilter('entity_id',$Customer_id);
        foreach ($Collection as $key => $Value) {
            $result[] = ['value' => $Value->getId(), 'label' => ($Value->getFirstname().' '.$Value->getLastname())];
        }
        return $result;
    }
}
?>