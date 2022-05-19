<?php

namespace App\Models;

use App\Utils\HistoryTypes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Phone extends Model
{
    use HasFactory;
    protected $table = 'phone';

    function history(){
        return $this->hasMany(History::class, 'phone_id', 'id')
            ->where('type', HistoryTypes::REQUEST_PHONE);
    }

    function lastRequest(){
        return $this->history()
            ->whereNull('answer_at')
            ->where('user_id', Auth::id());
    }
}
