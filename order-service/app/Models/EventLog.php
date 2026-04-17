<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventLog extends Model
{
    //
    protected $fillable = ['event', 'created_at', 'updated_at'];
}
