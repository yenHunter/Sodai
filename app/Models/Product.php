<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable; // Import Scout

class Product extends Model
{
    use HasFactory, Searchable;

    protected $fillable = ['vendor_id', 'name', 'slug', 'description', 'status'];

    // Scout: Define what data is searchable
    public function toSearchableArray()
    {
        // Only return columns that actually exist in the 'products' table
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
