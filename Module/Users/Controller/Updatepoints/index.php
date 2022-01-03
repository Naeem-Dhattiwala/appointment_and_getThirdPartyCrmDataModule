<?php

/**
 *
 * @author        Viha Digital Commerce Team <naeem@vdcstore.com>.
 * @copyright     Copyright(c) 2021 Viha Digital Commerce
 * @link          https://www.vihadigitalcommerce.com/
 * @date          16/Sep/2021
 */

namespace Iram\Users\Controller\Updatepoints;

use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;

class Index extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
    */
    protected $storeManager;
    /**
     * @var \Magento\Customer\Model\CustomerFactory
    */
    protected $customerFactory;

    protected $resultPageFactory;

    private $jsonResultFactory;

    protected $messageManager;

    protected $request;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\CustomerFactory    $customerFactory
    */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        JsonFactory $jsonResultFactory,
        Request $request,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->storeManager   = $storeManager;
        $this->messageManager = $messageManager;
        $this->customerFactory  = $customerFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->request = $request;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $websiteId  = $this->storeManager->getWebsite()->getWebsiteId();
        $bodyParam = $this->request->getBodyParams();
        $outputData = [];
        $customerNo = '';
        $customerLoyaltyPoints = '';
        $Email = [];
        if ($bodyParam):
            //print_r($bodyParam['data']);
            $customerNo  = $bodyParam['data']['customer_no'];
            $customerLoyaltyPoints = (int)$bodyParam['data']['no_of_points'];
            if ($customerLoyaltyPoints && $customerNo) {
                $customer = $this->customerFactory->create()->getCollection()
                            ->addAttributeToFilter('customer_no',$customerNo)
                            ->addAttributeToSelect('Email');
                if ($customer->getData()) {
                    foreach ($customer as $customerEmail) {
                        $Email = $customerEmail->getEmail();
                    }
                    $customerdata = $this->customerFactory->create()->setWebsiteId($websiteId)->loadByEmail($Email);
                    $customerdata->setCustomer_loyalty_points($customerLoyaltyPoints);
                    $customerdata->save();
                    $outputData['status']  = 200;
                    $outputData['message'] = "Customer Points are Updated sucessfully in associated website !";
                } else {
                    $outputData['status']  = 404;
                    $outputData['message'] = "Customer Number ".$customerNo." are not Exists in associated website !";
                }
            }
            else {
                $outputData['status']  = 404;
                $outputData['message'] = "Compulsory Required Customer Number and Number of Points !";
            }
            $value['output'][] = [
                'Message' => $outputData['message'],
                'Status' => $outputData['status']
            ];
            /*$json = json_encode($value);
            print_r($json);*/
            $response = $this->resultFactory
                ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
            ->setData($value);
            return $response;
        endif;
    }
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }
 
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}