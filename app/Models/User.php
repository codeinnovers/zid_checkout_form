<?php

namespace App\Models;

use App\Enums\UserStatusEnum;
use App\Traits\HasDataGrid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasDataGrid, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'status',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Grid filterable columns configuration.
     *
     * @var array<string, string|array<string, mixed>>
     */
    protected array $gridFilterable = [
        'status' => 'exact',
        'name' => 'like',
        'email' => 'like',
    ];

    /**
     * Grid sortable columns.
     *
     * @var array<string>
     */
    protected array $gridSortable = [
        'id',
        'name',
        'email',
        'status',
        'created_at',
    ];

    /**
     * Grid searchable columns.
     *
     * @var array<int|string, string|array<string, mixed>>
     */
    protected array $gridSearchable = [
        'name',
        'email',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => UserStatusEnum::class,
        ];
    }

    public function merchant(): HasOne
    {
        return $this->hasOne(ZidMerchant::class);
    }
}
