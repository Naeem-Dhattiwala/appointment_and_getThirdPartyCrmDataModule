<?php

/**
 *
 * @author        Viha Digital Commerce Team <naeem@vdcstore.com>.
 * @copyright     Copyright(c) 2021 Viha Digital Commerce
 * @link          https://www.vihadigitalcommerce.com/
 * @date          09/Sep/2021
 */

namespace Iram\Appointments\Model\Appointment\Attribute\Source;

class Employee extends \Magento\Backend\Block\Template\Context implements \Magento\Framework\Option\ArrayInterface 
{
    protected $_attribute;

    protected $request;

    protected $option;

    protected $appointmentFactory;

    protected $helper;

     /**
     * Constructor
     * @param \Amasty\Storelocator\Model\Attribute  $attribute
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Amasty\Storelocator\Model\Options $option
     * @param \Iram\Appointments\Model\AppointmentFactory $appointmentFactory
     * @param \Iram\Appointments\Helper\Data $helper
     */

     public function __construct(
        \Amasty\Storelocator\Model\Attribute $attribute,
        \Magento\Framework\App\Request\Http $request,
        \Iram\Appointments\Model\AppointmentFactory $appointmentFactory,
        \Amasty\Storelocator\Model\Options $option,
        \Iram\Appointments\Helper\Data $helper
    ) {
        $this->appointmentFactory = $appointmentFactory;
        $this->_attribute = $attribute;
        $this->request = $request;
        $this->option = $option;
        $this->helper = $helper;
    }
    public function getEmpIdtoHide()
    {
        $Appointment_id = $this->request->getParam('appointment_id', false);
        $CurrentAppDate = "";
        $CurrentAppTime = "";
        $EmpIdtoHide = "";
        $AppointmentsCollection = $this->appointmentFactory->create()->getCollection()
                          ->addFieldToFilter('appointment_id', $Appointment_id);
        foreach ($AppointmentsCollection as $key => $AppointmentsValue) {
                $CurrentAppDate =  $AppointmentsValue->getAppointment_date();
                $CurrentAppTime = $AppointmentsValue->getAppointment_time();
        }
        $AppointmentsCollection2 = $this->appointmentFactory->create()->getCollection()
                                  ->addFieldToFilter('appointment_date', $CurrentAppDate)
                                  ->addFieldToFilter('appointment_time', $CurrentAppTime)
                                  ->addFieldToFilter('appointment_id', array('nin' => $Appointment_id));
        foreach ($AppointmentsCollection2 as $AppointmentsValue2) {
            $EmpIdtoHide .= $AppointmentsValue2->getEmployee().',';
        }
        return $EmpIdtoHide;
    }
    public function getEmpIdtoHide2()
    {
        $Appointment_id = $this->request->getParam('appointment_id', false);
        $CurrentsAppDate = "";
        $EmpIdtoHide2 = "";
        $AppCollection = $this->appointmentFactory->create()->getCollection()
                          ->addFieldToFilter('appointment_id', $Appointment_id);
        foreach ($AppCollection as $key => $AppsValue) {
            $CurrentsAppDate =  $AppsValue->getAppointment_date();
        }
        $count = "";
        $AppCollection2 = $this->appointmentFactory->create()->getCollection()
                                  ->addFieldToFilter('appointment_date', $CurrentsAppDate)
                                  ->addFieldToFilter('appointment_id', array('nin' => $Appointment_id));
        foreach ($AppCollection2 as $AppCollectionValue) {
            $count .= $AppCollectionValue->getEmployee().',';
        }
        $AppCollectionValuegrp = explode(",", $count);
        $AppCollectionValuefilter = array_filter($AppCollectionValuegrp);
        $AppCollectionData = array_count_values($AppCollectionValuefilter);
        foreach ($AppCollectionData as $key => $AppCollectionvalues) { 
            if($AppCollectionvalues >= $this->helper->getSlotsPerEmployee()){
                $EmpIdtoHide2 .= $key.',';
            }
        }
        return $EmpIdtoHide2;
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
            $Store_id = $Appointmentvalue->getAppointment_branch();
        }
        $Collection = $this->_attribute->getCollection()->addFieldToFilter('attribute_code','employee');
        $Collection->clear();
        $Collection->getSelect()->joinLeft(
            ['am_store_attr' => $Collection->getTable('amasty_amlocator_store_attribute')],
            'main_table.attribute_id  = am_store_attr.attribute_id ',
        )->where("am_store_attr.store_id = $Store_id")->distinct(true);
        $EmpIdtoHidegrp = explode(",", $this->getEmpIdtoHide());
        $EmpIdtoHideefilter = array_filter($EmpIdtoHidegrp);
        $EmpIdtoHide2grp = explode(",", $this->getEmpIdtoHide2());
        $EmpIdtoHidee2filter = array_filter($EmpIdtoHide2grp);

        $EmpIdtoHidee = array_merge($EmpIdtoHideefilter, $EmpIdtoHidee2filter);

        foreach ($Collection as $index => $value) {
            $Emp_id = explode(",", $value->getValue());
            if (!empty($EmpIdtoHidee)) {
                $Employee = $this->option->getCollection()
                        ->addFieldToFilter('value_id', array('in' => $Emp_id))
                        ->addFieldToFilter('value_id', array('nin' => $EmpIdtoHidee));
            } else {
                $Employee = $this->option->getCollection()
                        ->addFieldToFilter('value_id', array('in' => $Emp_id));
            }
            foreach ($Employee as $key => $Employees) {
                $Emp = explode(',', substr($Employees->getOptions_serialized(), 1, -1));
                $result[] = ['value' => $Employees->getValue_id(), 'label' => substr($Emp[0], 1, -1)];
            }
        }
        return $result;
    }
}
?>