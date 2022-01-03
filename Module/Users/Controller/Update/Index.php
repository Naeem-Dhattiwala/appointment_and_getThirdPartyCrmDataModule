<?php

/**
 *
 * @author        Viha Digital Commerce Team <naeem@vdcstore.com>.
 * @copyright     Copyright(c) 2021 Viha Digital Commerce
 * @link          https://www.vihadigitalcommerce.com/
 * @date          16/Sep/2021
 */

namespace Iram\Users\Controller\Update;

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
        $customerNo     = [];
        $customerEmail  = [];
        $customerName   = [];
        $customerLeadNo = [];
        $customerPassword = [];
        $Cname = [];
        $customerLoyaltyPoints = [];
        $Email = [];
        if ($bodyParam):
            foreach ($bodyParam['data'] as $i => $bodyParams) {
                $customerEmail[]    = $bodyParams['customer_email'];
                $customerPassword[] = $bodyParams['customer_password'];
                $customerName[]     = $bodyParams['customer_name'];
                $customerNo[]     = $bodyParams['customer_no'];
                $Cname[] = explode(" ", $customerName[$i]);
                $customerLeadNo[] = $bodyParams['customer_lead_no'];
                $customerLoyaltyPoints[] = $bodyParams['customer_loyalty_ponints'];
                $customerdata = $this->customerFactory->create()->setWebsiteId($websiteId)->loadByEmail($customerEmail[$i]);
                
                $customerNumChk = $this->customerFactory->create()->getCollection()
                                  ->addAttributeToSelect('*')
                                  ->addFieldToFilter('customer_no',  array('in' => array($customerNo[$i])));
                if ($customerEmail[$i] & $customerPassword[$i] & $customerName[$i] & $customerNo[$i]) {
                    if ($customerEmail[$i] != $customerdata->getEmail()) {
                        $outputData['status']  = 442;
                        $outputData['message'] = "Email is not Registered in associated website";
                    } elseif (count($customerNumChk) > 1) {
                        $outputData['status']  = 442;
                        $outputData['message'] = "Please Add Unique Customer Number";
                    } else {
                        $password = $customerPassword[$i];
                        if (strlen($customerPassword[$i]) <= 8) {
                            $outputData['status']  = 442;
                            $outputData['message'] = "Your Password Must Contain At Least 8 Characters!";
                        }
                        elseif (strlen($customerPassword[$i]) >= 22) {
                            $outputData['status']  = 442;
                            $outputData['message'] = "Your Password Must Contain Less than 22 Characters!";
                        }
                        elseif(!preg_match("#[0-9]+#",$password)) {
                            $outputData['status']  = 442;
                            $outputData['message'] = "Your Password Must Contain At Least 1 Number!";
                        }
                        elseif(!preg_match("#[A-Z]+#",$password)) {
                            $outputData['status']  = 442;
                            $outputData['message'] = "Your Password Must Contain At Least 1 Capital Letter!";
                        }
                        elseif(!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $password)) {
                            $outputData['status']  = 442;
                            $outputData['message'] = "Your Password Must Contain At Least 1 Special Character !";
                        }
                        elseif(!preg_match("#[a-z]+#",$password)) {
                            $outputData['status']  = 442;
                            $outputData['message'] = "Your Password Must Contain At Least 1 Lowercase Letter!";
                        } else {
                            $customerdata->setWebsiteId($websiteId);
                            $customerdata->setEmail($customerEmail[$i]); 
                            $customerdata->setFirstname($Cname[$i][0]);
                            $customerdata->setLastname($Cname[$i][1]);
                            $customerdata->setCustomer_no($customerNo[$i]);
                            $customerdata->setCustomer_lead_no($customerLeadNo[$i]);
                            $customerdata->setCustomer_loyalty_points($customerLoyaltyPoints[$i]);
                            $customerdata->setPassword($customerPassword[$i]);
                            $customerdata->save();
                            $customerdata->unsetData();
                            $outputData['status']  = 200;
                            $outputData['message'] = "Users are Updated sucessfully in associated website";
                        }           
                    }
                } else {
                    $outputData['status']  = 500;
                    $outputData['message'] = "Compulsory Require Email, Password, Customer Name and Customer Number";
                }
                $value['output'][] = [
                    'Message'.' '.$customerNo[$i] => $outputData['message'],
                    'Status' => $outputData['status']
                ];
            }
            /*$json = json_encode($value);
            print_r($json);*/
            $response = $this->resultFactory
                ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
            ->setData($value);
            return $response;
        else :
            echo $outputData['message'] =  "Users are not updated sucessfully";
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