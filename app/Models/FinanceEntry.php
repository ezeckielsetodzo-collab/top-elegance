<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceEntry extends Model
{
    protected $fillable = ['entry_date', 'label', 'income', 'expense'];

    protected $casts = [
        'entry_date' => 'date',
    ];
}
