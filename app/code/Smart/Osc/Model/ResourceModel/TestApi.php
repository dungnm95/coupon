<?php
namespace Smart\Osc\Model\ResourceModel;
class TestApi extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('smart_osc_testapi','testapi_id');
    }
}
