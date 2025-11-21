<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BelanjaR extends Model
{
    use HasFactory;
    protected $table = 'belanjaR';
    protected $guarded = ['id'];
}
