<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaraniuran extends Model
{
    use HasFactory;
    protected $table = 'iuran';
    protected $guarded = ['id'];
}
