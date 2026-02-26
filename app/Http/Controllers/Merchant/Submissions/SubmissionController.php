<?php

namespace App\Http\Controllers\Merchant\Submissions;

use App\Http\Controllers\Controller;
use App\Models\ZidFormField;
use App\Models\ZidFormSubmission;
use App\Models\ZidMerchant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubmissionController extends Controller
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

        $submissions = ZidFormSubmission::query()
            ->where('zid_merchant_id', $merchant->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $formFields = ZidFormField::query()
            ->where('zid_merchant_id', $merchant->id)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'label', 'label_ar', 'field_type']);

        return Inertia::render('Merchant/Submissions/Index', [
            'initialSubmissions' => [
                'data' => $submissions->items(),
                'meta' => [
                    'total'        => $submissions->total(),
                    'per_page'     => $submissions->perPage(),
                    'current_page' => $submissions->currentPage(),
                    'last_page'    => $submissions->lastPage(),
                ],
            ],
            'formFields' => $formFields,
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $merchant = $this->currentMerchant();

        $submissions = ZidFormSubmission::query()
            ->where('zid_merchant_id', $merchant->id)
            ->applyDataGrid($request)
            ->paginateForGrid($request);

        return response()->json(ZidFormSubmission::formatGridResponse($submissions));
    }

    public function destroy(ZidFormSubmission $submission): JsonResponse
    {
        $merchant = $this->currentMerchant();
        abort_if($submission->zid_merchant_id !== $merchant->id, 403);

        $submission->delete();

        return response()->json([
            'success' => true,
            'message' => __('Submission deleted successfully.'),
        ]);
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $merchant = $this->currentMerchant();

        $validated = $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:zid_form_submissions,id'],
        ]);

        $count = ZidFormSubmission::query()
            ->where('zid_merchant_id', $merchant->id)
            ->whereIn('id', $validated['ids'])
            ->get()
            ->each(fn ($s) => $s->delete()) // triggers model event for file cleanup
            ->count();

        return response()->json([
            'success' => true,
            'message' => __(':count submission(s) deleted successfully.', ['count' => $count]),
        ]);
    }
}
