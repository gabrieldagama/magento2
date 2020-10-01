<?php

namespace Magento\EngcomTest\Cron;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;

class InventoryCron
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
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * ProductCron constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param StockRegistryInterface $stockRegistry
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        StockRegistryInterface $stockRegistry,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function execute(): void
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('type_id', 'simple')->create();
        $productList = $this->productRepository->getList($searchCriteria);

        foreach ($productList->getItems() as $product) {
            $stockItem = $this->stockRegistry->getStockItemBySku($product->getSku());
            $productQty = $stockItem->getQty() + 1;
            $stockItem->setQty($productQty);
            $this->stockRegistry->updateStockItemBySku($product->getSku(), $stockItem);
        }
    }
}
