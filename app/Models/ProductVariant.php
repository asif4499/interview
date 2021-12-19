<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $guarded = [];
    public function variantType(){
        return $this->belongsTo(Variant::class, 'variant_id', 'id');
    }
}
