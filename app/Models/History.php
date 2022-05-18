<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function cell()
    {
        return $this->hasOne(Geo::class, 'id', 'cell_id');
    }

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
