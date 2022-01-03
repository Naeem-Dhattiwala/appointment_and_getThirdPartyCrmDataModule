<?php

/**
 *
 * @author        Viha Digital Commerce Team <naeem@vdcstore.com>.
 * @copyright     Copyright(c) 2021 Viha Digital Commerce
 * @link          https://www.vihadigitalcommerce.com/
 * @date          09/Sep/2021
 */

namespace Iram\Appointments\Block\Index;

class Index extends \Magento\Framework\View\Element\Template
{
    protected $_customerSession;

    protected $appointmentFactory;

    protected $location;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Iram\Appointments\Model\AppointmentFactory $appointmentFactory
     * @param \Amasty\Storelocator\Model\Location $location
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\SessionFactory $session,
        \Iram\Appointments\Model\AppointmentFactory $appointmentFactory,
        \Amasty\Storelocator\Model\Location $location,
        array $data = []
    ) {
        $this->_customerSession = $session;
        $this->appointmentFactory = $appointmentFactory;
         $this->_location = $location;
        parent::__construct($context, $data);
    }
    public function getCustomerId(){
        $customerSession = $this->_customerSession->create();
        return $customerSession->getCustomer()->getId();
    }
    public function getAppointments(){
        $Collection = $this->appointmentFactory->create()->getCollection()
                      ->addFieldToFilter('customer_id',$this->getCustomerId());
        return $Collection;
    }
    public function getBrand($id){
        $Collection  = $this->_location->getCollection()->addFieldToFilter('id',$id);
        foreach ($Collection as $key => $value) {
           $name = $value->getName();
        }
        return $name;
    }
}

