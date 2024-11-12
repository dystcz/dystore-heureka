<?php

namespace Modules\DystoreHeureka\Domain\Filament\Components;

use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class HeurekaFeedCategorySelect extends Select
{
    public static function make(string $name): static
    {
        $url = 'https://www.heureka.cz/direct/xml-export/shops/heureka-sekce.xml';

        $categories = Cache::remember(
            'heureka-feed-categories',
            Carbon::now()->addDay(),
            function () use ($url) {
                $xml = simplexml_load_string(file_get_contents($url));
                $json = json_encode($xml);

                return Collection::make(json_decode($json, true)['CATEGORY']);
            }
        );

        $categories = Collection::make($categories)
            ->skip(1)
            ->mapWithKeys(fn (array $category) => [
                $category['CATEGORY_NAME'] => $category['CATEGORY'],
            ])
            ->map(fn (array $categories, string $category) => Collection::make($categories)
                ->flatten()
                ->filter(fn (string $value) => Str::contains($value, 'Heureka.cz'))
                // ->mapWithKeys(fn (string $category) => [$category => Str::afterLast($category, ' | ')])
                ->mapWithKeys(fn (string $category) => [$category => Str::after($category, 'Heureka.cz | ')])
                ->prepend("{$category}", "Heureka.cz | {$category}")
            );

        $static = parent::make($name)
            ->options($categories)
            ->native(false)
            ->hint(
                str("[Categories]({$url})")
                    ->inlineMarkdown()
                    ->toHtmlString()
            )
            ->hintIcon('heroicon-m-question-mark-circle')
            ->searchable()
            ->optionsLimit(100)
            ->searchDebounce(500);

        return $static;
    }
}
