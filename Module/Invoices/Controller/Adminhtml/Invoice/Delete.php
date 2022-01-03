<?php
declare(strict_types=1);

namespace Iram\Invoices\Controller\Adminhtml\Invoice;

class Delete extends \Iram\Invoices\Controller\Adminhtml\Invoice
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('invoice_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\Iram\Invoices\Model\Invoice::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Invoice.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['invoice_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Invoice to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}

