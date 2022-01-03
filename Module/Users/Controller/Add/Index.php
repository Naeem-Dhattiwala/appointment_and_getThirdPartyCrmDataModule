<?php

/**
 *
 * @author        Viha Digital Commerce Team <naeem@vdcstore.com>.
 * @copyright     Copyright(c) 2021 Viha Digital Commerce
 * @link          https://www.vihadigitalcommerce.com/
 * @date          15/Sep/2021
 */

namespace Iram\Users\Controller\Add;

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
        $customer   = $this->customerFactory->create();
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
                $customerEmailChk = $this->customerFactory->create()->getCollection()
                                ->addFieldToFilter('email',  array('in' => array($customerEmail[$i])))
                                ->addFieldToSelect('email');
                $customerNumChk = $this->customerFactory->create()->getCollection()
                                  ->addAttributeToSelect('*')
                                  ->addFieldToFilter('customer_no',  array('in' => array($customerNo[$i])));
                if ($customerEmail[$i] & $customerPassword[$i] & $customerName[$i] & $customerNo[$i]) {
                    if(!filter_var($customerEmail[$i], FILTER_VALIDATE_EMAIL)){
                        $outputData['status']  = 442;
                        $outputData['message'] = "Invalid Email Format !";
                    } else {
                        if (count($customerEmailChk) >= 1) {
                            $outputData['status']  = 442;
                            $outputData['message'] = "Please Add Unique Email, this Email is already registered in Website !";
                        } elseif (count($customerNumChk) >= 1) {
                            $outputData['status']  = 442;
                            $outputData['message'] = "Please Add Unique Customer Number, this Customer Number is already registered in Website !";
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
                                if (!is_int($customerLoyaltyPoints[$i])) {
                                    $outputData['status']  = 442;
                                    $outputData['message'] = "Require  loyalty points in Int";
                                } else {
                                    $customer->setWebsiteId($websiteId);
                                    $customer->setEmail($customerEmail[$i]); 
                                    $customer->setFirstname($Cname[$i][0]);
                                    $customer->setLastname($Cname[$i][1]);
                                    $customer->setCustomer_no($customerNo[$i]);
                                    $customer->setCustomer_lead_no($customerLeadNo[$i]);
                                    $customer->setCustomer_loyalty_points($customerLoyaltyPoints[$i]);
                                    $customer->setPassword($customerPassword[$i]);
                                    $customer->save();
                                    $customer->unsetData();
                                    $outputData['status'] = 200;
                                    $outputData['message'] = "Users are submitted sucessfully in associated website !";
                                }
                            }           
                        }
                    }
                } else {
                    $outputData['status']  = 442;
                    $outputData['message'] = "Compulsory Require Email, Password, Customer Name and Customer Number !";
                }
                $value['output'][] = [
                    'Message'.' '.$customerNo[$i] => $outputData['message'],
                    'Status' => $outputData['status']
                ];
            }
            $json = json_encode($value);
            $response = $this->resultFactory
                ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
            ->setData($value);
            return $response;
        else :
            echo $outputData['message'] =  "Users are not submitted sucessfully !";
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