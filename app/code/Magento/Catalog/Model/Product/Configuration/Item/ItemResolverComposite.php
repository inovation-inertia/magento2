<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Catalog\Model\Product\Configuration\Item;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ObjectManager;

/**
 * {@inheritdoc}
 */
class ItemResolverComposite implements ItemResolverInterface
{
    /** @var string[] */
    private $itemResolvers = [];

    /**
     * @param string[] $itemResolvers
     */
    public function __construct(array $itemResolvers)
    {
        $this->itemResolvers = $itemResolvers;
    }

    /**
     * {@inheritdoc}
     */
    public function getFinalProduct(ItemInterface $item) : ProductInterface
    {
        $product = $item->getProduct();
        foreach ($this->itemResolvers as $resolver) {
            $resolvedProduct = $this->getItemResolverInstance($resolver)->getFinalProduct($item);
            if ($resolvedProduct !== $product) {
                $product = $resolvedProduct;
                break;
            }
        }
        return $product;
    }

    /**
     * Get the instance of the item resolver by class name
     *
     * @param string $className
     * @return ItemResolverInterface
     */
    private function getItemResolverInstance(string $className)
    {
        return ObjectManager::getInstance()->get($className);
    }
}
