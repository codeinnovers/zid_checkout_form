<?php

namespace App\Http\Controllers\Merchant\FormFields;

use App\Enums\FormFieldStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\ZidFormField;
use App\Models\ZidMerchant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class FormFieldController extends Controller
{
    private function currentMerchant(): ZidMerchant
    {
        $merchant = auth()->user()->merchant;
        abort_if(! $merchant, 403, 'No merchant account linked to this user.');

        return $merchant;
    }

    public function index(): Response
    {
        $merchant = $this->currentMerchant();

        $fields = ZidFormField::query()
            ->where('zid_merchant_id', $merchant->id)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('FormFields/Index', [
            'fieldStatuses' => FormFieldStatusEnum::options(),
            'initialFields' => [
                'data' => $fields->items(),
                'meta' => [
                    'total'        => $fields->total(),
                    'per_page'     => $fields->perPage(),
                    'current_page' => $fields->currentPage(),
                    'last_page'    => $fields->lastPage(),
                ],
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $merchant = $this->currentMerchant();

        $validated = $request->validate([
            'field_type'        => ['required', 'string', 'in:text,email,phone,number,textarea,select,radio,checkbox,date,file'],
            'label'             => ['required', 'string', 'max:255'],
            'label_ar'          => ['nullable', 'string', 'max:255'],
            'name'              => ['required', 'string', 'max:100', 'alpha_dash', Rule::unique('zid_form_fields', 'name')->where('zid_merchant_id', $merchant->id)],
            'placeholder'       => ['nullable', 'string', 'max:255'],
            'placeholder_ar'    => ['nullable', 'string', 'max:255'],
            'default_value'     => ['nullable', 'string', 'max:255'],
            'options'           => ['nullable', 'array'],
            'options.*.label'   => ['required_with:options', 'string', 'max:255'],
            'options.*.value'   => ['required_with:options', 'string', 'max:100'],
            'validation_rules'  => ['nullable', 'array'],
            'sort_order'        => ['required', 'integer', 'min:0'],
            'is_required'       => ['required', 'boolean'],
            'status'            => ['required', 'string', 'in:active,inactive'],
        ]);

        $field = ZidFormField::create([
            ...$validated,
            'zid_merchant_id' => $merchant->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Form field created successfully.'),
            'field'   => $field,
        ], 201);
    }

    public function list(Request $request): JsonResponse
    {
        $merchant = $this->currentMerchant();

        $fields = ZidFormField::query()
            ->where('zid_merchant_id', $merchant->id)
            ->applyDataGrid($request)
            ->paginateForGrid($request);

        return response()->json(ZidFormField::formatGridResponse($fields));
    }

    public function updateStatus(Request $request, ZidFormField $formField): JsonResponse
    {
        $merchant = $this->currentMerchant();

        abort_if($formField->zid_merchant_id !== $merchant->id, 403);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $formField->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => __('Field status updated successfully.'),
            'field'   => $formField->fresh(),
        ]);
    }

    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        $merchant = $this->currentMerchant();

        $validated = $request->validate([
            'ids'    => ['required', 'array', 'min:1'],
            'ids.*'  => ['required', 'integer', 'exists:zid_form_fields,id'],
            'status' => ['required', 'string', 'in:active,inactive'],
        ]);

        $count = ZidFormField::query()
            ->where('zid_merchant_id', $merchant->id)
            ->whereIn('id', $validated['ids'])
            ->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => __(':count field(s) status updated successfully.', ['count' => $count]),
        ]);
    }

    public function destroy(ZidFormField $formField): JsonResponse
    {
        $merchant = $this->currentMerchant();

        abort_if($formField->zid_merchant_id !== $merchant->id, 403);

        $formField->delete();

        return response()->json([
            'success' => true,
            'message' => __('Field deleted successfully.'),
        ]);
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $merchant = $this->currentMerchant();

        $validated = $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:zid_form_fields,id'],
        ]);

        $count = ZidFormField::query()
            ->where('zid_merchant_id', $merchant->id)
            ->whereIn('id', $validated['ids'])
            ->delete();

        return response()->json([
            'success' => true,
            'message' => __(':count field(s) deleted successfully.', ['count' => $count]),
        ]);
    }
}
