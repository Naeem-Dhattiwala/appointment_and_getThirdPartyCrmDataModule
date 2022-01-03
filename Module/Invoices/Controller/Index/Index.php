<?php
declare(strict_types=1);

namespace Iram\Invoices\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;

    protected $invoiceFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Iram\Invoices\Model\InvoiceFactory $invoiceFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Iram\Invoices\Model\InvoiceFactory $invoiceFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->invoiceFactory = $invoiceFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();  
        $customerSession = $om->create('Magento\Customer\Model\Session');
        $Customer_no = $customerSession->getCustomer()->getCustomer_no();
        $PurchaseHistory = $this->invoiceFactory->create()->getCollection()
                           ->addFieldToFilter('customer_no',$Customer_no);
        $this->resultPage = $this->resultPageFactory->create();  
        $this->resultPage->getConfig()->getTitle()->prepend(__('My Purchase History'));
        if(count($PurchaseHistory) == 0){
            $this->messageManager->addNotice(__('You have no items in your Purchase History.'));
        }
        return $this->resultPage;
    }
}