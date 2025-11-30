<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menus extends Model
{
    use HasFactory;
    protected $table = 'admin_menus';
    protected $guarded = ['id'];

    public function submenus()
    {
        return $this->hasMany(Submenus::class, 'menu_id', 'id');
    }

    public function hakakses()
    {
        return $this->hasMany(Hakakses::class, 'idmenu', 'id');
    }
}
