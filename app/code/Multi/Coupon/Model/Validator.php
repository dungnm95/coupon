<?php

namespace Multi\Coupon\Model;

use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Item\AbstractItem;

class Validator extends \Magento\SalesRule\Model\Validator
{

    public function process(AbstractItem $item)
    {
        $item->setDiscountAmount(0);
        $item->setBaseDiscountAmount(0);
        $item->setDiscountPercent(0);

        $itemPrice = $this->getItemPrice($item);
        if ($itemPrice < 0) {
            return $this;
        }
        $array_coupon = explode(',', $this->getCouponCode());
        foreach ($array_coupon as $coupon){
            $get_rule = $this->_getRule($item->getAddress(), $coupon);
            $appliedRuleIds = $this->rulesApplier->applyRules(
                $item,
                $get_rule,
                $this->_skipActionsValidation,
                $coupon
            );
            $this->rulesApplier->setAppliedRuleIds($item, $appliedRuleIds);
        }


        return $this;
    }

    protected function _getRule(Address $address = null, $couponCode)
    {
        $addressId = $this->getAddressId($address);
        $key = $this->getWebsiteId() . '_'
            . $this->getCustomerGroupId() . '_'
            . $couponCode . '_'
            . $addressId;
        if (!isset($this->_rules[$key])) {
            $this->_rules[$key] = $this->_collectionFactory->create()
                ->setValidationFilter(
                    $this->getWebsiteId(),
                    $this->getCustomerGroupId(),
                    $couponCode,
                    null,
                    $address
                )
                ->addFieldToFilter('is_active', 1)
                ->load();
        }
        return $this->_rules[$key];
    }


}
