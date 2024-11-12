<?php

use Dystcz\LunarApi\Domain\Products\Enums\Availability;

return [
    'name' => 'DystoreHeureka',

    'defaults' => [
        'brand' => 'Plantbee',
        'category' => 'Dílna, stavba, zahrada | Zahrada | Péče o rostliny a pěstování rostlin',

        'shipping' => [
            'shipping_label' => 'Zásilkovna',
            'country' => 'CZ',
            'ships_from_country' => 'CZ',
            'price' => '67 CZK',
            'free_shipping_threshold' => 'CZ:1499 CZK',
        ],

        'delivery_dates' => [
            Availability::IN_STOCK->value => 0,
            Availability::OUT_OF_STOCK->value => 31,
            Availability::BACKORDER->value => 7,
            Availability::PREORDER->value => 15,
            'default' => 7,
        ],
    ],

    'transformer' => \Modules\DystoreHeureka\Domain\ProductVariants\Data\HeurekaFeedTransformer::class,
];
