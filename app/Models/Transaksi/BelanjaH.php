<?php

namespace App\Models\Transaksi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BelanjaH extends Model
{
    use HasFactory;
    protected $table = 'belanjaH';
    protected $guarded = ['id'];

    public function rincian()
    {
        return $this->hasMany(BelanjaR::class, 'notrans', 'notrans');
    }
}
