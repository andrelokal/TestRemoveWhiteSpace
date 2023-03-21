<?php

namespace AndreMartos\RemoveWhiteSpace\Plugin;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Controller\Account\CreatePost as Subject;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filesystem\Driver\File;
use Psr\Log\LoggerInterface;
use AndreMartos\RemoveWhiteSpace\Helper\Email;
use Magento\Customer\Model\Session;


class CustomerCreatePost
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var File
     */
    protected $file;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Email
     */
    protected $emailHelper;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * CustomerCreatePost constructor.
     * @param RequestInterface $request
     * @param File $file
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestInterface $request,
        File             $file,
        LoggerInterface  $logger,
        Email            $emailHelper,
        Session          $customerSession
    )
    {
        $this->request = $request;
        $this->file = $file;
        $this->logger = $logger;
        $this->emailHelper = $emailHelper;
        $this->customerSession = $customerSession;
    }

    /**
     * @param Subject $subject
     * @param CustomerInterface $result
     * @return CustomerInterface
     */
    public function afterExecute(Subject $subject, $result)
    {
        $customerData = $this->request->getPostValue();
        $customerName = $customerData['firstname'] ?? '';
        $customerLastName = $customerData['lastname'] ?? '';
        $customerEmail = $customerData['email'] ?? '';
        $customer = $this->customerSession->getCustomer();
        // Log customer data
        $logMessage = sprintf("New customer registered: %s %s (%s)", $customerName, $customerLastName, $customerEmail);
        $this->logger->info($logMessage);

        //send Email
        $this->emailHelper->sendEmail($customer);

        return $result;
    }
}
