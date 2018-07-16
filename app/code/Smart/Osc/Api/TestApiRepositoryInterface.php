<?php
namespace Smart\Osc\Api;

use Smart\Osc\Api\Data\TestApiInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface TestApiRepositoryInterface 
{
    public function save(TestApiInterface $page);

    public function getById($id);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(TestApiInterface $page);

    public function deleteById($id);
}
