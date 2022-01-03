<?php


/**
 *
 * @author        Viha Digital Commerce Team <naeem@vdcstore.com>.
 * @copyright     Copyright(c) 2021 Viha Digital Commerce
 * @link          https://www.vihadigitalcommerce.com/
 * @date          09/Sep/2021
 */

namespace Iram\Appointments\Model\Appointment;

use Iram\Appointments\Model\ResourceModel\Appointment\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    protected $loadedData;
    protected $dataPersistor;
    protected $collection;
    protected $customerfactory;


    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param \Magento\Customer\Model\CustomerFactory $customerfactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Customer\Model\CustomerFactory $customerfactory,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
         $this->customerfactory = $customerfactory;
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        $cname = '';
        foreach ($items as $model) {
            $Customerdata = $this->customerfactory->create()->getCollection()
                            ->addFieldToFilter('entity_id',$model->getCustomer_id());
            foreach ($Customerdata as $value) {
                $cname = $value->getFirstname(). ' ' .$value->getLastname();
            }
            $this->loadedData[$model->getId()] = $model->getData();
            $cus['customer_id'] = $cname;
            $fullData = $this->loadedData;
            $this->loadedData[$model->getId()] = array_merge($fullData[$model->getId()], $cus);

        }
        $data = $this->dataPersistor->get('iram_appointments_appointment');
        
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('iram_appointments_appointment');
        }
        
        return $this->loadedData;
    }
}

