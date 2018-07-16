<?php
namespace Smart\Osc\Model;
class TestApi extends \Magento\Framework\Model\AbstractModel implements \Smart\Osc\Api\Data\TestApiInterface, \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'smart_osc_testapi';

    protected function _construct()
    {
        $this->_init('Smart\Osc\Model\ResourceModel\TestApi');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
