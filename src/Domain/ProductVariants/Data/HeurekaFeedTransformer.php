<?php

namespace Modules\DystoreHeureka\Domain\ProductVariants\Data;

use Carbon\Carbon;
use Dystcz\LunarApi\Domain\Products\Enums\Availability;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Lunar\Models\Contracts\Product;
use Lunar\Models\Contracts\ProductOptionValue;
use Lunar\Models\Contracts\ProductVariant;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property \Domain\ProductVariants\Models\ProductVariant $variant
 * @property \Domain\Products\Models\Product $product
 */
class HeurekaFeedTransformer
{
    public function __construct(
        public ProductVariant $variant,
        public Product $product,
    ) {}

    public static function make(ProductVariant $variant, Product $product): self
    {
        return new static($variant, $product);
    }

    public function id(): string
    {
        return $this->variant->id;
    }

    public function ean(): ?string
    {
        return $this->variant->ean;
    }

    public function title(): string
    {
        return $this->variant->name;
    }

    public function productName(): string
    {
        return implode(' ', array_filter([
            $this->brand(),
            $this->title(),
            $this->size(),
            $this->color(),
        ]));
    }

    public function url(): string
    {
        $query = array_filter([
            'size' => $this->size() ? Str::slug($this->size()) : null,
            'color' => $this->color() ? Str::slug($this->color()) : null,
        ]);

        $query = http_build_query($query);

        $url = env('CLIENT_APP_URL')."/product/{$this->product->defaultUrl->slug}";

        return $query ? "{$url}?{$query}" : $url;
    }

    public function image(): ?string
    {
        if ($image = $this->variant->getThumbnail()) {
            return $image->getUrl('small');
        }

        if ($image = $this->product->thumbnail) {
            return $image->getUrl('small');
        }

        return null;
    }

    public function additionalImages(): Collection
    {
        if ($this->variant->getImages()->count() < 1) {
            return Collection::make([]);
        }

        return $this->variant
            ->getImages()
            ->where('id', '!=', $this->variant->getThumbnail()?->id)
            ->map(fn (Media $media) => $media->getUrl('small'));
    }

    public function availability(): string
    {
        $availability = Availability::of($this->variant);

        if ($availability === Availability::ALWAYS) {
            return Availability::IN_STOCK->value;
        }

        return $availability->value;
    }

    public function preorderDate(): ?string
    {
        if (! $this->variant->attr('eta') || Availability::of($this->variant) !== Availability::PREORDER) {
            return null;
        }

        return Carbon::parse($this->variant->attr('eta'))->toIso8601String();
    }

    public function deliveryDate(): ?string
    {
        $availability = Availability::of($this->variant);

        $defaults = Config::get('dystore-heureka.defaults.delivery_dates');

        return match ($availability) {
            Availability::IN_STOCK => $defaults[Availability::IN_STOCK->value],
            Availability::OUT_OF_STOCK => $defaults[Availability::OUT_OF_STOCK->value],
            Availability::BACKORDER => $defaults[Availability::BACKORDER->value],
            Availability::PREORDER => $defaults[Availability::PREORDER->value],
            default => $defaults['default'],
        };
    }

    public function price(): string
    {
        $price = $this->variant->prices->first();
        $decimal = $price->price->decimal();

        return "{$decimal} {$price->currency->code}";
    }

    public function size(): ?string
    {
        $size = $this->variant->values->first(
            fn (ProductOptionValue $value) => $value->option->handle === 'size',
        )?->translate('name');

        return $size ? Str::lower($size, App::getLocale()) : null;
    }

    public function color(): ?string
    {
        $color = $this->variant->values->first(
            fn (ProductOptionValue $value) => $value->option->handle === 'color',
        )?->translate('name', 'en');

        return $color ? Str::lower($color, App::getLocale()) : null;
    }

    public function description(): string
    {
        $description = $this->product->translateAttribute('description');

        return $description ? strip_tags($description) : '';
    }

    public function brand(): ?string
    {
        return $this->product->brand?->name ?? Config::get('dystore-heureka.defaults.brand');
    }

    public function category(): ?string
    {
        if ($category = $this->product->heureka_feed_category) {
            return $category;
        }

        foreach ($this->product->collections as $collection) {
            if ($category = $collection->heureka_feed_category) {
                return $category;
            }
        }

        return $this->product->productType->heureka_feed_category ?? Config::get('dystore-heureka.defaults.category');
    }

    public function videoUrl(): ?string
    {
        return $this->product->translateAttribute('youtube_link');
    }

    public function feedAuthor(): string
    {
        return env('APP_NAME');
    }

    public function feedTitle(): string
    {
        return Config::get('feed.feeds.heureka.title');
    }

    public function feedSummary(): string
    {
        return Config::get('feed.feeds.heureka.description');
    }

    public function feedLink(): string
    {
        $appUrl = env('APP_URL');
        $feedRoute = Config::get('feed.feeds.heureka.url');

        return "{$appUrl}/{$feedRoute}";
    }

    public function feedUpdatedAt(): ?Carbon
    {
        return $this->variant->updated_at;
    }
}
