<?=
/* Using an echo tag here so the `<? ... ?>` won't get parsed as short tags */
'
<?xml version="1.0" encoding="utf-8"?>' . PHP_EOL ?>
<SHOP>
    @foreach ($items as $item)
        <SHOPITEM>
            <ITEM_ID>{!! \Spatie\Feed\Helpers\Cdata::out($item->ITEM_ID) !!}</ITEM_ID>
            <PRODUCTNAME>{!! \Spatie\Feed\Helpers\Cdata::out($item->PRODUCT) !!}</PRODUCTNAME>
            <PRODUCT>{!! \Spatie\Feed\Helpers\Cdata::out($item->PRODUCT) !!}</PRODUCT>
            <DESCRIPTION>{!! \Spatie\Feed\Helpers\Cdata::out($item->DESCRIPTION) !!}</DESCRIPTION>
            <URL>{!! \Spatie\Feed\Helpers\Cdata::out($item->URL) !!}</URL>
            <IMGURL>{!! \Spatie\Feed\Helpers\Cdata::out($item->IMGURL) !!}</IMGURL>
            @if (isset($item->IMGURL_ALTERNATIVE))
                <IMGURL_ALTERNATIVE>{!! \Spatie\Feed\Helpers\Cdata::out($item->IMGURL_ALTERNATIVE) !!}</IMGURL_ALTERNATIVE>
            @endif
            @if (isset($item->VIDEO_URL))
                <VIDEO_URL>{!! \Spatie\Feed\Helpers\Cdata::out($item->VIDEO_URL) !!}</VIDEO_URL>
            @endif
            <PRICE_VAT>{!! \Spatie\Feed\Helpers\Cdata::out($item->PRICE_VAT) !!}</PRICE_VAT>
            <VAT>21%</VAT>
            <CATEGORYTEXT>{!! \Spatie\Feed\Helpers\Cdata::out($item->CATEGORYTEXT) !!}</CATEGORYTEXT>
            @if (isset($item->EAN))
                <EAN>{!! \Spatie\Feed\Helpers\Cdata::out($item->EAN) !!}</EAN>
            @endif
            <MANUFACTURER>{!! \Spatie\Feed\Helpers\Cdata::out($item->MANUFACTURER) !!}</MANUFACTURER>
            <DELIVERY_DATE>{!! \Spatie\Feed\Helpers\Cdata::out($item->DELIVERY_DATE) !!}</DELIVERY_DATE>
            <DELIVERY>
                <DELIVERY_ID>ZASILKOVNA_NA_ADRESU</DELIVERY_ID>
                <DELIVERY_PRICE>97</DELIVERY_PRICE>
                <DELIVERY_PRICE_COD>134</DELIVERY_PRICE_COD>
            </DELIVERY>
            <DELIVERY>
                <DELIVERY_ID>ZASILKOVNA</DELIVERY_ID>
                <DELIVERY_PRICE>77</DELIVERY_PRICE>
                <DELIVERY_PRICE_COD>114</DELIVERY_PRICE_COD>
            </DELIVERY>
            <!-- <GIFT>Ke každé objednávce dárek zdarma</GIFT> -->
        </SHOPITEM>
    @endforeach
</SHOP>
