<?php

/**
 *
 * @author        Viha Digital Commerce Team <naeem@vdcstore.com>.
 * @copyright     Copyright(c) 2021 Viha Digital Commerce
 * @link          https://www.vihadigitalcommerce.com/
 * @date          09/Sep/2021
 */

namespace Iram\Appointments\Controller\Index;

class Validation extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;

    protected $jsonHelper;

    protected $jsonFactory;

    protected $_schedule;

    protected $helperData;

    protected $appointment;

    protected $option;

    protected $_attribute;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Amasty\Storelocator\Model\Options $option
     * @param \Amasty\Storelocator\Model\Attribute  $attribute
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Amasty\Storelocator\Model\Schedule  $schedule,
        \Iram\Appointments\Helper\Data $helperData,
        \Iram\Appointments\Model\AppointmentFactory $appointment,
        \Amasty\Storelocator\Model\Attribute $attribute,
        \Amasty\Storelocator\Model\Options $option,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->jsonFactory = $jsonFactory;
        $this->_schedule = $schedule;
        $this->logger = $logger;
        $this->helperData = $helperData;
        $this->appointment = $appointment;
        $this->_attribute = $attribute;
        $this->option = $option;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $schedule = $this->getRequest()->getParams('schedule');
        $dayName = $this->getRequest()->getParams('dayName');
        $date = $this->getRequest()->getParams('appointment_date');
        $branch = $this->getRequest()->getParams('branch');
        $tmp = [];
        $output = [];
        $newdate = date('Y-m-d',strtotime($date['appointment_date']));
        $countBrachEmployee = '';
        $hide_time = '';
        $Store_id = $branch['branch'];
        try {
            $result = $this->_schedule->getCollection()
                  ->addFieldToFilter('id' ,$schedule['schedule'])
                  ->addFieldToSelect('schedule');
            foreach ($result as $key => $value) {
                $data = implode(',', $value->getData());
                $json = json_decode($data);
                $day = $dayName['dayName'];
                $json2 = $json->$day;
                $day_status = $day.'_'.'status';
                $status = $json2->$day_status;
                $from = ($json2->from->hours).':'.($json2->from->minutes);
                $break_from = ($json2->break_from->hours).':'.($json2->break_from->minutes);
                $break_to = ($json2->break_to->hours).':'.($json2->break_to->minutes);
                $to = ($json2->to->hours).':'.($json2->to->minutes);
            }

            $Collection = $this->_attribute->getCollection()->addFieldToFilter('attribute_code','employee');
            $Collection->clear();
            $Collection->getSelect()->joinLeft(
                ['am_store_attr' => $Collection->getTable('amasty_amlocator_store_attribute')],
                'main_table.attribute_id  = am_store_attr.attribute_id ',
            )->where("am_store_attr.store_id = $Store_id")->distinct(true);
            foreach ($Collection as $index => $value) {
                $Emp_id = explode(",", $value->getValue());
                $countBrachEmployee = count($Emp_id);
            }
            $Collection = $this->appointment->create()->getCollection()->addFieldToFilter('appointment_branch',$Store_id)->addFieldToFilter('appointment_date',$newdate);
            foreach ($Collection as $key => $value) {
                if(!empty($value->getEmployee())){
                    $tmp[$value['appointment_time']][] = $value['appointment_date'];
                }
            }
            foreach($tmp as $time => $dates)
            {
                $output[] = array(
                    'appointment_time' => $time,
                    'appointment_date' => $dates
                );
                if (count($dates) >= $countBrachEmployee) {
                    $hide_time .= $time.',';
                }
            }
            $Slot_duration_Attribute = $this->_attribute->getCollection()->addFieldToFilter('attribute_code','slot_duration');
            $Slot_duration_Attribute->clear();
            $Slot_duration_Attribute->getSelect()->joinLeft(
                ['am_store_attr' => $Slot_duration_Attribute->getTable('amasty_amlocator_store_attribute')],
                'main_table.attribute_id  = am_store_attr.attribute_id ',
            )->where("am_store_attr.store_id = $Store_id")->distinct(true);
            foreach ($Slot_duration_Attribute as $index => $Slot_duration_AttributeValue) {
                $Slot_duration = $this->option->getCollection()->addFieldToFilter('value_id', $Slot_duration_AttributeValue->getValue());
                foreach ($Slot_duration as $key => $Slot_duration_Value) {
                    $Slot_interval = explode(',', substr($Slot_duration_Value->getOptions_serialized(), 1, -1));
                    $Slot_interval_time = substr($Slot_interval[0], 1, -1);
                }
            }
            $response = $this->resultFactory
                ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
            ->setData([
                    'slot_duration' => $Slot_interval_time,
                    'status' => $status,
                    'from' => $from,
                    'break_from'  => $break_from,
                    'break_to' => $break_to,
                    'to'  => $to,
                    'hide_time' => $hide_time
            ]);
            return $response;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}