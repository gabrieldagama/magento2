<?php

namespace Magento\EngcomTest\Cron;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;

class ProductCron
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * ProductCron constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws StateException
     */
    public function execute(): void
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('type_id', 'simple')->create();
        $productList = $this->productRepository->getList($searchCriteria);

        foreach ($productList->getItems() as $product) {
            $productPrice = $product->getPrice() + 1;
            $product->setPrice($productPrice);
            $this->productRepository->save($product);
        }
    }
}
