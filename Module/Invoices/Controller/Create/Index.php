<?php
declare(strict_types=1);

namespace Iram\Invoices\Controller\Create;

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;

class Index extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{

    protected $resultPageFactory;

    private $jsonResultFactory;

    protected $request;

    protected $invoicesFactory;

    protected $invoicesDetailsFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Webapi\Rest\Request $request
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
     * @param \Iram\Invoices\Model\InvoiceFactory $invoicesFactory
     * @param \Iram\Invoices\Model\InvoiceDetails $invoicesDetailsFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Webapi\Rest\Request $request,
        \Iram\Invoices\Model\InvoiceFactory $invoicesFactory,
        \Iram\Invoices\Model\InvoiceDetailsFactory $invoicesDetailsFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->invoicesFactory = $invoicesFactory;
        $this->invoicesDetailsFactory = $invoicesDetailsFactory;
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
        $bodyParam = $this->request->getBodyParams();
        $Invoice_number = [];
        $Invoice_type = [];
        $Branch_name = [];
        $Serial_no = [];
        $Amount = [];
        $Total_Amount = [];
        $Specs = [];
        $Customer_no = [];
        $invoice = $this->invoicesFactory->create();
        $invoiceDetails = $this->invoicesDetailsFactory->create();
        if ($bodyParam){
            foreach ($bodyParam['data'] as $key => $bodyParams) {
                $Invoice_number[] = $bodyParams['Invoice_number'];
                $Invoice_type[] = $bodyParams['Invoice_type'];
                $Branch_name[] = $bodyParams['Branch_name'];
                $Customer_no[] = $bodyParams['customer_no'];
                $invoiceNumberChk = $this->invoicesFactory->create()->getCollection()
                                ->addFieldToFilter('invoice_number',  array('in' => array($Invoice_number[$key])));
                if (empty($Invoice_number[$key] && $Invoice_type[$key] && $Branch_name[$key] && $Customer_no[$key])) {
                    $outputData['status']  = 442;
                    $outputData['message'] = "Invoice Number, Invoice Type, Customer No and Branch Name is Required";
                } elseif (count($invoiceNumberChk) >= 1) {
                    $outputData['status']  = 442;
                    $outputData['message'] = "Invoice Number Already Exists in associated website";
                } else {
                    $Total_Amount[] = array_sum(array_column($bodyParams['Items_Details'],'amount'));
                    foreach ($bodyParams['Items_Details'] as $key2 => $ItemDetails) {
                        $Serial_no = $ItemDetails['serial_no'];
                        $Amount = $ItemDetails['amount'];
                        $Specs = $ItemDetails['specs'];
                        if ($Serial_no || $Amount || $Specs) {
                            $invoiceDetails->setInvoice_number($Invoice_number[$key]);
                            $invoiceDetails->setSerial_no($Serial_no);
                            $invoiceDetails->setAmount($Amount);
                            $invoiceDetails->setSpecs($Specs);
                            $invoiceDetails->save();
                            $invoiceDetails->unsetData();
                        }
                    }
                    $invoice->setInvoice_number($Invoice_number[$key]);
                    $invoice->setInvoice_type($Invoice_type[$key]);
                    $invoice->setBranch_name($Branch_name[$key]);
                    $invoice->setTotal_amounts($Total_Amount[$key]);
                    $invoice->setCustomer_no($Customer_no[$key]);
                    $invoice->save();
                    $invoice->unsetData();
                    $outputData['status'] = 200;
                    $outputData['message'] = "Invoice are submitted sucessfully in associated website !";
                }
                $value['output'][] = [
                    'Message'.' '.$Invoice_number[$key] => $outputData['message'],
                    'Status' => $outputData['status']
                ];
            }
        } else {
            $value['output'][] = [
                'Message'.' '.$Invoice_number[$key] => 'Please add Invoices',
                'Status' => 442
            ];
        }
        /*$json = json_encode($value);
        print_r($json);*/
        $response = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)->setData($value);
        return $response;
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

