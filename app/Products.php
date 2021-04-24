<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
//    protected $fallible = ['product_name', 'description', 'section_id'];

    protected $guarded = [];


    public function section(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Sections::class);
    }
}
