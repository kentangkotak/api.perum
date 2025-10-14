<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submenus extends Model
{
    use HasFactory;
    protected $table = 'admin_subs';
    protected $guarded = ['id'];
}
