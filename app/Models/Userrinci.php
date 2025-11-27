<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Userrinci extends Model
{
    use HasFactory;
    protected $table = 'user_rinci';
    protected $guarded = ['id'];
}
