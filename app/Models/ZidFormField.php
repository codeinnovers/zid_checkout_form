<?php

namespace App\Models;

use App\Enums\FormFieldStatusEnum;
use App\Traits\HasDataGrid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZidFormField extends Model
{
    use HasDataGrid;

    protected array $gridFilterable = [
        'status'     => 'exact',
        'field_type' => 'exact',
        'label'      => 'like',
    ];

    protected array $gridSortable = [
        'id', 'field_type', 'label', 'name', 'sort_order', 'status', 'created_at',
    ];

    protected array $gridSearchable = [
        'label', 'name',
    ];
    protected $fillable = [
        'zid_merchant_id',
        'field_type',
        'name',
        'label',
        'label_ar',
        'placeholder',
        'placeholder_ar',
        'options',
        'default_value',
        'validation_rules',
        'sort_order',
        'is_required',
        'status',
    ];

    protected $casts = [
        'options'          => 'array',
        'validation_rules' => 'array',
        'is_required'      => 'boolean',
        'status'           => FormFieldStatusEnum::class,
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(ZidMerchant::class, 'zid_merchant_id');
    }
}
