<?php

namespace Modules\DystoreHeureka\Domain\Orders\Actions;

use Dystcz\LunarApi\Domain\Orders\Events\OrderCreated;
use Dystcz\LunarApi\Support\Actions\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Lunar\Models\Contracts\Order;
use Lunar\Models\ProductVariant as LunarProductVariant;

class SendHeurekaOverenoZakazniky extends Action implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function handle(Order $order): void
    {
        if (isset($order->meta['heureka']) && $order->meta['heureka'] == false) {
            return;
        }

        $order->load([
            'lines' => fn ($query) => $query->where(
                'purchasable_type',
                LunarProductVariant::modelClass()
            ),
        ]);

        $productIds = $order->lines
            ->pluck('purchasable')
            ->pluck('product_id')
            ->toArray();

        $params = [
            'id' => 'e0af1ea60bb248f2e6254ea26ba00e1c',
            'email' => $order->shippingAddress->contact_email,
            'itemId' => $productIds,
            'orderid' => $order->id,
        ];

        $url = 'https://www.heureka.cz/direct/dotaznik/objednavka.php?'.http_build_query($params);
        $response = Http::get($url);
    }

    /**
     * Send order paid notification as event listener.
     */
    public function asListener(OrderCreated $event): void
    {
        $this->handle($event->order);
    }
}
