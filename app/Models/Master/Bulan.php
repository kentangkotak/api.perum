<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bulan extends Model
{
    use HasFactory;
    protected $table = 'bulan';
    protected $guarded = ['id'];
}
