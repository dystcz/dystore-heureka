<?php

namespace Modules\DystoreHeureka;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Lunar\Models\Contracts\Product as ProductContract;
use Lunar\Models\Contracts\ProductVariant as ProductVariantContract;
use Lunar\Models\Product;

class HeurekaFeed
{
    public function __construct() {}

    public static function make(): self
    {
        return new static;
    }

    public function getFeedItems(): Collection
    {
        return $this->getProducts()
            ->flatMap(
                fn (ProductContract $product) => $product->variants->map(
                    fn (ProductVariantContract $variant) => new HeurekaFeedItem(
                        Config::get('dystore-heureka.transformer')::make($variant, $product),
                    ),
                ),
            );
    }

    protected function getProducts(): Collection
    {
        return Product::modelClass()::query()
            ->whereIn('status', ['published'])
            ->where(fn ($query) => $query->has('thumbnail')->orHas('variants.thumbnail'))
            ->with([
                'brand',
                'collections' => fn ($query) => $query->where('collection_group_id', 1), // main
                'collections.defaultUrl',
                'defaultUrl',
                'images',
                'prices',
                'prices.currency',
                'productType',
                'thumbnail',
                'variants',
                'variants.defaultUrl',
                'variants.images',
                'variants.thumbnail',
                'variants.values',
                'variants.values.option',
            ])
            ->get();
    }
}
