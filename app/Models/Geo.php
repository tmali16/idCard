<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Geo extends Model
{
    use HasFactory;
    protected $table = "CELL_LAC_GEO";
    protected $connection = "geo";
//    protected $table = "t_cellid_geo";
    public $timestamps = false;
    protected $fillable = ['*'];
}
