<?php

namespace App\Http\Controllers\Admin\Merchants;

use App\Http\Controllers\Controller;
use App\Models\ZidMerchant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MerchantController extends Controller
{
    public function index(): Response
    {
        $merchants = ZidMerchant::query()
            ->with('user:id,name,email')
            ->withCount('formFields')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Admin/Merchants/Index', [
            'initialMerchants' => [
                'data' => $merchants->items(),
                'meta' => [
                    'total'        => $merchants->total(),
                    'per_page'     => $merchants->perPage(),
                    'current_page' => $merchants->currentPage(),
                    'last_page'    => $merchants->lastPage(),
                ],
            ],
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $merchants = ZidMerchant::query()
            ->with('user:id,name,email')
            ->withCount('formFields')
            ->applyDataGrid($request)
            ->paginateForGrid($request);

        return response()->json(ZidMerchant::formatGridResponse($merchants));
    }

    public function destroy(ZidMerchant $merchant): JsonResponse
    {
        $merchant->delete();

        return response()->json([
            'success' => true,
            'message' => __('Merchant deleted successfully.'),
        ]);
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:zid_merchants,id'],
        ]);

        $count = ZidMerchant::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => __(':count merchant(s) deleted successfully.', ['count' => $count]),
        ]);
    }
}
