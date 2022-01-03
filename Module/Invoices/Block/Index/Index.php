<?php
declare(strict_types=1);

namespace Iram\Invoices\Block\Index;

class Index extends \Magento\Framework\View\Element\Template
{
    protected $invoiceFactory;

    protected $invoiceDetailsFactory;

    protected $productRepository;

    protected $storeManagerInterface;

    protected $_escaper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Iram\Invoices\Model\InvoiceFactory $invoiceFactory
     * @param \Iram\Invoices\Model\InvoiceDetailsFactory $invoiceDetailsFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
     * @var \Magento\Framework\Escaper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Iram\Invoices\Model\InvoiceFactory $invoiceFactory,
        \Iram\Invoices\Model\InvoiceDetailsFactory $invoiceDetailsFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\Escaper $_escaper,
        array $data = []
    ) {
        $this->invoiceFactory = $invoiceFactory;
        $this->invoiceDetailsFactory = $invoiceDetailsFactory;
        $this->productRepository = $productRepository;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->_escaper = $_escaper;
        parent::__construct($context, $data);
    }

    public function getPurchaseHistory(){
        $om = \Magento\Framework\App\ObjectManager::getInstance();  
        $customerSession = $om->create('Magento\Customer\Model\Session');  
        $Customer_no = $customerSession->getCustomer()->getCustomer_no();
        $PurchaseHistory = $this->invoiceFactory->create()->getCollection()
                           ->addFieldToFilter('customer_no',$Customer_no);
        return $PurchaseHistory;
    }
    public function getPurchaseHistoryDetails($invoice_num){
        $PurchaseHistoryDetails =   $this->invoiceDetailsFactory->create()
                                    ->getCollection()
                                    ->addFieldToFilter('invoice_number',$invoice_num);
        return $PurchaseHistoryDetails;
    }
    public function getProduct($productId){
        $product = $this->productRepository->getById($productId);
        return $product;
    }
    public function getProductUrl()
    {
        $currentStore = $this->storeManagerInterface->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product/';
        return $mediaUrl;
    }
    public function escapeHtml($data, $allowedTags = null){
        return $this->_escaper->escapeHtml($data, $allowedTags);
    }
    public function escapeUrl($string){
        return $this->_escaper->escapeUrl($string);
    }
}
