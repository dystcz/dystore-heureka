<?php

namespace Modules\DystoreHeureka;

use Illuminate\Support\Arr;
use Modules\DystoreHeureka\Domain\ProductVariants\Data\HeurekaFeedTransformer;
use Spatie\Feed\Feedable;
use Spatie\Feed\FeedItem;

class HeurekaFeedItem implements Feedable
{
    public function __construct(
        protected HeurekaFeedTransformer $data,
    ) {}

    public function toFeedItem(): FeedItem
    {
        return FeedItem::create(array_filter([
            'ITEM_ID' => $this->data->id(),
            'PRODUCT' => $this->data->productName(),
            'DESCRIPTION' => $this->data->description(),
            'MANUFACTURER' => $this->data->brand(),
            'URL' => $this->data->url(),
            'IMGURL' => $this->data->image(),
            'IMGURL_ALTERNATIVE' => Arr::first(
                array: $this->data->additionalImages(),
                default: null,
            ),
            'PRICE_VAT' => $this->data->price(),
            'CATEGORYTEXT' => $this->data->category(),
            'EAN' => $this->data->ean(),
            'DELIVERY_DATE' => $this->data->deliveryDate(),
            'VIDEO_URL' => $this->data->videoUrl(),

            'id' => $this->data->id(),
            'title' => $this->data->feedTitle(),
            'summary' => $this->data->feedSummary(),
            'updated' => $this->data->feedUpdatedAt(),
            'link' => $this->data->feedLink(),
            'authorName' => $this->data->feedAuthor(),
        ], fn (mixed $value) => ! is_null($value)));
    }
}
