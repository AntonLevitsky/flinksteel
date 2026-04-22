<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'placed_at' => 'datetime',
            'requested_delivery_date' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'bestaetigt' => 'Bestätigt',
            'in_bearbeitung' => 'In Bearbeitung',
            'versandt' => 'Versandt',
            'zugestellt' => 'Zugestellt',
            default => $this->status,
        };
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            'bestaetigt' => 'blue',
            'in_bearbeitung' => 'yellow',
            'versandt' => 'green',
            'zugestellt' => 'green',
            default => 'gray',
        };
    }

    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        $latest = static::where('order_number', 'like', "B-{$year}%")->max('order_number');
        if ($latest) {
            $num = (int) substr($latest, strlen("B-{$year}")) + 1;
        } else {
            $num = 1;
        }
        return sprintf("B-%s%05d", $year, $num);
    }
}
