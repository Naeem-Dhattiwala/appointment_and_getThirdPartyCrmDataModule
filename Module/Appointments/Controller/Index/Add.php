<?php

/**
 *
 * @author        Viha Digital Commerce Team <naeem@vdcstore.com>.
 * @copyright     Copyright(c) 2021 Viha Digital Commerce
 * @link          https://www.vihadigitalcommerce.com/
 * @date          09/Sep/2021
 */

namespace Iram\Appointments\Controller\Index;

class Add extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;

    protected $_customerSession;

    protected $messageManager;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $session
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_customerSession = $session;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($this->_customerSession->isLoggedIn()) {
            $this->resultPage = $this->resultPageFactory->create(); 
            $this->resultPage->getConfig()->getTitle()->prepend(__('Book Appointment'));
            return $this->resultPage;
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            $redirectLink = '/customer/account/login/'; 
            $resultRedirect->setUrl($redirectLink);
            $this->messageManager->addWarning('Please Login in a Website.');
            return $resultRedirect;
        }
    }
}

