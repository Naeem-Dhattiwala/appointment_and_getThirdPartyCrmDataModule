<?php

/**
 *
 * @author        Viha Digital Commerce Team <naeem@vdcstore.com>.
 * @copyright     Copyright(c) 2021 Viha Digital Commerce
 * @link          https://www.vihadigitalcommerce.com/
 * @date          09/Sep/2021
 */

namespace Iram\Appointments\Controller\Index;

class Save extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */

    protected $_appointment;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Iram\Appointments\Model\AppointmentFactory  $appointment
     */

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Iram\Appointments\Model\AppointmentFactory $appointment
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_appointment = $appointment;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $model = $this->_appointment->create();
            $model->setData($data);
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('Your Appointments is booked Sucessfully!'));
                return $resultRedirect->setPath('*/*/add');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Data.'));
            }
            return $resultRedirect->setPath('*/*/');
        }
        return $resultRedirect->setPath('*/*/');
    }
}