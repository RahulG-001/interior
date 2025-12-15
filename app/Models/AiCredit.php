<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiCredit extends Model
{
    protected $fillable = ['room_type', 'room_style', 'cost', 'model', 'size', 'ip_address'];
}
