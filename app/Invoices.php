<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Sections;

class Invoices extends Model
{
    protected $guarded = [];
    public function section()
    {
        return $this->belongsTo(Sections::class);
    }
}
