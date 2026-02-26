<?php

namespace App\Models;

use App\Enums\FormSubmissionStatusEnum;
use App\Traits\HasDataGrid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ZidFormSubmission extends Model
{
    use HasDataGrid, HasFactory;

    protected $fillable = [
        'zid_merchant_id',
        'order_number',
        'status',
        'form_data',
        'attachments',
        'ip_address',
    ];

    protected $casts = [
        'form_data'   => 'array',
        'attachments' => 'array',
        'status'      => FormSubmissionStatusEnum::class,
    ];

    protected array $gridFilterable = [
        'order_number' => 'like',
        'status'       => 'exact',
    ];

    protected array $gridSortable = [
        'id', 'order_number', 'status', 'created_at',
    ];

    protected array $gridSearchable = [
        'order_number',
    ];

    protected static function booted(): void
    {
        static::deleting(function (ZidFormSubmission $submission) {
            if ($submission->attachments) {
                foreach ($submission->attachments as $path) {
                    Storage::disk('public')->delete($path);
                }
            }
        });
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(ZidMerchant::class, 'zid_merchant_id');
    }
}
