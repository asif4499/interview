<?php

use App\Models\ProductVariant;

function productVariant($subRow){
    $variant['one'] = ProductVariant::where('id', $subRow['product_variant_one'])->pluck('variant')->first();
    $variant['two'] = ProductVariant::where('id', $subRow['product_variant_two'])->pluck('variant')->first();
    $variant['three'] = ProductVariant::where('id', $subRow['product_variant_three'])->pluck('variant')->first();

    return implode(" / ", $variant);
}
