<?php

namespace Modules\DystoreHeureka\Domain\ProductTypes\Filament\Resources;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Illuminate\Support\Arr;
use Lunar\Admin\Support\Extending\ResourceExtension;
use Modules\DystoreHeureka\Domain\Filament\Components\HeurekaFeedCategorySelect;

class ProductTypeResourceExtension extends ResourceExtension
{
    public function extendForm(Form $form): Form
    {
        $components = $form->getComponents(withHidden: true);

        $section = Arr::first($components, fn (Component $component) => $component instanceof Section);
        /** @var Section $section */
        $section->schema([
            ...$section->getChildComponents(),

            HeurekaFeedCategorySelect::make('heureka_feed_category')
                ->label('Heureka Category'),
        ]);

        return $form->schema([
            ...$components,
        ]);
    }
}
