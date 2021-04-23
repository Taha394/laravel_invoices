<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoices extends Model
{
    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'due_date',
        'product',
        'section',
        'discount',
        'rate_tax',
        'value_tax',
        'total',
        'status',
        'value_status',
        'note',
        'user',
        'deleted_at',
    ];
}
