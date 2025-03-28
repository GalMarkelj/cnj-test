<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Housing extends Model
{
    use HasFactory;

    // Disable incrementing because ULID is not an integer
    public $incrementing = false;

    // Set the primary key
    protected $primaryKey = 'uuid';

    // Enable timestamps for created_at and updated_at
    public $timestamps = true;

    // Set the table name if it's not the plural form of the model
    protected $table = 'housing';

    // Set the fillable columns
    protected $fillable = [
        'uuid',
        'date',
        'area',
        'average_price',
        'code',
        'houses_sold',
        'no_of_crimes',
        'borough_flag',
    ];

    // Automatically generate ULID before creating the model
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->uuid = (string) Str::ulid(); // Automatically generate ULID
        });
    }
}
