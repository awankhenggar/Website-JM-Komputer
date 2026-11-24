<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceUpdate extends Model
{
    protected $fillable = ['service_request_id', 'status', 'note'];
}
