<?php

/**
 *
 * @author        Viha Digital Commerce Team <naeem@vdcstore.com>.
 * @copyright     Copyright(c) 2021 Viha Digital Commerce
 * @link          https://www.vihadigitalcommerce.com/
 * @date          17/Sep/2021
 */

namespace Iram\Users\Controller\Websiteusers;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\Controller\Result\JsonFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $request;

    private $jsonResultFactory;

    protected $customerFactory;

    protected $addressRepository;

    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        JsonFactory $jsonResultFactory,
        Request $request,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->request = $request;
        $this->customerFactory  = $customerFactory;
        $this->addressRepository = $addressRepository;
        parent::__construct($context);
    }
    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $customer   = $this->customerFactory->create()->getCollection()
                      ->addAttributeToSelect('*');
        $Username = '';
        $Customer_no = '';
        $Lead_no = '';
        $Customer_id = '';
        $Customer_name = '';
        $Customer_email = '';
        $Customer_password = '';
        $Last_updated = '';
        $telephone = '';
        $country = '';
        $city = '';
        $street = '';
        foreach ($customer as $customerdata) {
            $Username = $customerdata->getName();
            $Customer_no = $customerdata->getCustomer_no();
            $Lead_no = $customerdata->getCustomer_lead_no();
            $Customer_id = $customerdata->getId();
            $Customer_name = $customerdata->getName();
            $Customer_email = $customerdata->getEmail();
            $Customer_password = $customerdata->getPassword_hash();
            $Customer_loyalty_points = $customerdata->getCustomer_loyalty_points();
            $Last_updated  = $customerdata->getUpdated_at();
            if ($customerdata->getDefault_billing() && $customerdata->getDefault_billing() != 0) {
                $customerModel = $this->customerFactory->create()->load($customerdata->getId());
                $customerAddress = [];
                if ($customerModel->getAddresses() != null)
                {
                    foreach ($customerModel->getAddresses() as $address) {
                        $customerAddress[] = $address->toArray();
                    }
                }
                foreach ($customerAddress as $customerAddres)
                {
                    $telephone = $customerAddres['telephone'];
                    $street = $customerAddres['street'];
                    $city = $customerAddres['city'];
                    $region = $customerAddres['region'];
                    $postcode = $customerAddres['postcode'];
                    $country = $customerAddres['country_id'];
                }
                $value['data'][] = [
                    'customer_username' => $Customer_email,
                    'customer_no' => $Customer_no,
                    'customer_lead_no' => $Lead_no,
                    'customer_id' => $Customer_id,
                    'customer_email' => $Customer_email,
                    'customer_name' => $Customer_name,
                    'customer_mobile_number' => $telephone,
                    'customer_password' => $Customer_password,
                    'customer_loyalty_ponints' => $Customer_loyalty_points,
                    'customer_address' => $street.' '.$city.', '.$region.', '.$postcode.', '.$country,
                    'last_update_by' => $Last_updated
                ];
            } else {
               $value['data'][] = [
                    'customer_username' => $Customer_email,
                    'customer_no' => $Customer_no,
                    'customer_lead_no' => $Lead_no,
                    'customer_id' => $Customer_id,
                    'customer_email' => $Customer_email,
                    'customer_name' => $Customer_name,
                    'Customer_password' => $Customer_password,
                    'customer_loyalty_ponints' => $Customer_loyalty_points,
                    'last_update_by' => $Last_updated
                ]; 
            }   
        }
        $response = $this->resultFactory
                ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
            ->setData($value);
            return $response;
        //return $this->resultPageFactory->create();
    }
}