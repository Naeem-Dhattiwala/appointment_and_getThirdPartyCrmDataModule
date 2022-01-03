<?php

/**
 *
 * @author        Viha Digital Commerce Team <naeem@vdcstore.com>.
 * @copyright     Copyright(c) 2021 Viha Digital Commerce
 * @link          https://www.vihadigitalcommerce.com/
 * @date          09/Sep/2021
 */

namespace Iram\Appointments\Block\Index;

class Add extends \Magento\Framework\View\Element\Template
{
    protected $_location;

    protected $_schedule;

    protected $_customerSession;

    protected $helperData;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Amasty\Storelocator\Model\Location  $location
     * @param \Amasty\Storelocator\Model\Schedule  $schedule
     * @param \Magento\Customer\Model\Session $session
     * @param \Iram\Appointments\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Amasty\Storelocator\Model\Location $location,
        \Amasty\Storelocator\Model\Schedule  $schedule,
        \Magento\Customer\Model\SessionFactory $session,
        \Iram\Appointments\Helper\Data $helperData,
        array $data = []
    ) {
        $this->_location = $location;
        $this->_schedule = $schedule;
        $this->_customerSession = $session;
        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }
    public function getBrand(){
        return $this->_location->getCollection()->addFieldToFilter('status',1);
    }
    public function getAppointmentTime(){
        return $this->_schedule->getCollection();
    }
    public function getCustomerId(){
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $om->get('Magento\Customer\Model\Session');
        return $customerSession->getCustomer()->getId();
    }
    public function getSlotDration(){
       return $this->helperData->getslotDuration();
    }
}

