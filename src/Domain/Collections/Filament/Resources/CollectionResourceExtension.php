<?php

namespace Modules\DystoreHeureka\Domain\Collections\Filament\Resources;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Illuminate\Support\Arr;
use Lunar\Admin\Support\Extending\ResourceExtension;
use Modules\DystoreHeureka\Domain\Filament\Components\HeurekaFeedCategorySelect;

class CollectionResourceExtension extends ResourceExtension
{
    public function extendForm(Form $form): Form
    {
        $components = $form->getComponents(withHidden: true);

        $sectionName = 'Product Feeds';

        /** @var Section $section */
        $section = Arr::first(
            $components,
            fn (Component $component) => $component instanceof Section && $component->getHeading() === $sectionName,
        );

        $component = HeurekaFeedCategorySelect::make('heureka_feed_category')
            ->label('Heureka Category');

        // Add to existing section
        if ($section) {
            $section->schema([
                ...$section->getChildComponents(),
                $component,
            ]);

            return $form;
        }

        // Make new section
        $section = Section::make($sectionName)
            ->schema([
                $component,
            ]);

        return $form->schema([
            ...$components,
            $section,
        ]);
    }
}
