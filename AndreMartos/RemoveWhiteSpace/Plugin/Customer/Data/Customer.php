<?php
namespace AndreMartos\RemoveWhiteSpace\Plugin\Customer\Data;

class Customer
{
    public function beforeSetFirstname(\Magento\Customer\Model\Data\Customer $customer, $value)
    {
        $value = preg_replace('/\s+/', '', $value);
        return [$value];
    }
}
