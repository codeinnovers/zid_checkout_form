<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ZidFormField;
use App\Models\ZidMerchant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FormFieldController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'store_reference' => ['required', 'string'],
        ]);

        $merchant = ZidMerchant::where('store_reference', $request->store_reference)->first();

        if (! $merchant) {
            return response()->json([
                'success' => false,
                'message' => 'Merchant not found.',
            ], 404);
        }

        $fields = ZidFormField::query()
            ->where('zid_merchant_id', $merchant->id)
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get([
                'id',
                'field_type',
                'label',
                'label_ar',
                'name',
                'placeholder',
                'placeholder_ar',
                'default_value',
                'options',
                'validation_rules',
                'sort_order',
                'is_required',
            ]);

        return response()->json([
            'success' => true,
            'data'    => $fields,
        ]);
    }
}
