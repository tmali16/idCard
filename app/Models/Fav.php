<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fav extends Model
{
    use HasFactory;
    protected $connection = 'bill';
    protected $table = "favorites";
//    public $timestamps = false;
    protected $guarded = ['id'];
}
