<?php

namespace AndreMartos\RemoveWhiteSpace\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Translate\Inline\StateInterface;

class Email extends AbstractHelper
{
    const XML_PATH_EMAIL_RECIPIENT = 'trans_email/ident_support/email';
    const XML_PATH_EMAIL_SENDER = 'trans_email/ident_support/name';
    const XML_PATH_EMAIL_TEMPLATE = 'removewhite_space/settings/email_template';
    const XML_PATH_EMAIL_SUBJECT = 'removewhite_space/settings/subject';


    protected $transportBuilder;
    protected $_scopeConfig;

    /**
     * @var StateInterface
     */
    protected $inlineTranslation;

    public function __construct(
        Context          $context,
        TransportBuilder $transportBuilder,
        StateInterface   $inlineTranslation
    )
    {
        parent::__construct($context);
        $this->transportBuilder = $transportBuilder;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->inlineTranslation = $inlineTranslation;
    }

    public function sendEmail($customer)
    {
        $emailTemplate = $this->_scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, ScopeInterface::SCOPE_STORE);
        $senderName = $this->_scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, ScopeInterface::SCOPE_STORE);
        $senderEmail = $this->_scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, ScopeInterface::SCOPE_STORE);
        $emailSubject = $this->_scopeConfig->getValue(self::XML_PATH_EMAIL_SUBJECT, ScopeInterface::SCOPE_STORE);

        $templateVars = [
            'customer_name' => $customer->getName(),
            'customer_lastname' => $customer->getLastname(),
            'customer_email' => $customer->getEmail(),
            'subject' => $emailSubject,
        ];

        $this->inlineTranslation->suspend();
        $transport = $this->transportBuilder
            ->setTemplateIdentifier($emailTemplate)
            ->setTemplateOptions([
                'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
            ])
            ->setTemplateVars($templateVars)
            ->setFrom([
                'name' => $senderName,
                'email' => $senderEmail,
            ])
            ->addTo($customer->getEmail())
            ->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }
}
