<?php
namespace Smart\Osc\Model\ResourceModel\TestApi;
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Smart\Osc\Model\TestApi','Smart\Osc\Model\ResourceModel\TestApi');
    }
}
