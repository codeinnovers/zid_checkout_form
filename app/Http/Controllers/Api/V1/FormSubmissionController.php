<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ZidFormField;
use App\Models\ZidFormSubmission;
use App\Models\ZidMerchant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class FormSubmissionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // Validate base fields first
        $request->validate([
            'store_reference' => ['required', 'string'],
            'order_number'    => ['required', 'string', 'max:255'],
        ]);

        $merchant = ZidMerchant::where('store_reference', $request->store_reference)->first();

        if (! $merchant) {
            return response()->json([
                'success' => false,
                'message' => 'Merchant not found.',
            ], 404);
        }

        // Load active form fields for this merchant
        $fields = ZidFormField::query()
            ->where('zid_merchant_id', $merchant->id)
            ->where('status', 'active')
            ->orderBy('sort_order')
            ->get();

        // Build dynamic validation rules from field definitions
        $rules = [];

        foreach ($fields as $field) {
            $fieldRules = $field->is_required ? ['required'] : ['nullable'];

            match ($field->field_type) {
                'file'     => $fieldRules = array_merge($fieldRules, ['file', 'max:10240']),
                'email'    => $fieldRules = array_merge($fieldRules, ['string', 'email', 'max:255']),
                'number'   => $fieldRules = array_merge($fieldRules, ['numeric']),
                'date'     => $fieldRules = array_merge($fieldRules, ['date']),
                'checkbox' => $fieldRules = array_merge($fieldRules, ['boolean']),
                default    => $fieldRules = array_merge($fieldRules, ['string', 'max:2000']),
            };

            // Apply extra validation_rules defined on the field
            if ($field->validation_rules) {
                $isNumeric = in_array($field->field_type, ['number']);
                if (isset($field->validation_rules['min_length']) && ! $isNumeric) {
                    $fieldRules[] = 'min:' . $field->validation_rules['min_length'];
                }
                if (isset($field->validation_rules['max_length']) && ! $isNumeric) {
                    $fieldRules[] = 'max:' . $field->validation_rules['max_length'];
                }
                if (isset($field->validation_rules['min_value']) && $isNumeric) {
                    $fieldRules[] = 'min:' . $field->validation_rules['min_value'];
                }
                if (isset($field->validation_rules['max_value']) && $isNumeric) {
                    $fieldRules[] = 'max:' . $field->validation_rules['max_value'];
                }
            }

            $rules[$field->name] = $fieldRules;
        }

        try {
            $validated = $request->validate($rules);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        }

        // Separate file fields from regular fields
        $formData    = [];
        $attachments = [];

        foreach ($fields as $field) {
            if ($field->field_type === 'file') {
                if ($request->hasFile($field->name) && $request->file($field->name)->isValid()) {
                    $path = $request->file($field->name)
                        ->store("form-submissions/{$merchant->id}", 'public');
                    $attachments[$field->name] = $path;
                }
            } else {
                if ($request->has($field->name) && $request->input($field->name) !== null) {
                    $formData[$field->name] = $request->input($field->name);
                }
            }
        }

        $submission = ZidFormSubmission::create([
            'zid_merchant_id' => $merchant->id,
            'order_number'    => $request->order_number,
            'form_data'       => $formData,
            'attachments'     => $attachments ?: null,
            'ip_address'      => $request->ip(),
        ]);

        return response()->json([
            'success'       => true,
            'message'       => 'Form submitted successfully.',
            'submission_id' => $submission->id,
        ], 201);
    }
}
