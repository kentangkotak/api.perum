<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nominaliuran extends Model
{
    use HasFactory;
    protected $table = 'nominaliuran';
    protected $guarded = ['id'];
}
