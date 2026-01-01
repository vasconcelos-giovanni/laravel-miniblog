<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('orderByName', function (Builder $builder) {
            $builder->orderByName();
        });
    }

    public function scopeOrderByName(Builder $query): void
    {
        $query->orderBy('name');
    }
}
