<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ZidMerchant extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'username',
        'phone',
        'email',
        'token',
        'xmanager_token',
        'refresh_token',
        'token_expiration',
        'store_reference',
    ];

    protected $casts = [
        'token_expiration' => 'datetime',
    ];

    protected $hidden = [
        'token',
        'xmanager_token',
        'refresh_token',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function formFields(): HasMany
    {
        return $this->hasMany(ZidFormField::class, 'zid_merchant_id');
    }
}
