<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Housing extends Model
{
    use HasFactory;
    use HasUuids;

    // Disable incrementing because id is not an integer
    public $incrementing = false;

    // Set the table name if it's not the plural form of the model
    protected $table = 'housing';

    protected $guarded = ['id'];

}
