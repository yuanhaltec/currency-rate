<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = ['currency', 'created_by', 'updated_by'];

    protected function currency(): Attribute
    {
        return new Attribute(
            set: fn ($value) => strtoupper($value)
        );
    }
}