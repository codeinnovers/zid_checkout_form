<?php

namespace App\Http\Controllers\Admin\Users;

use App\Enums\UserRoleEnum;
use App\Enums\UserStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::query()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Admin/Users/Index', [
            'userStatuses' => UserStatusEnum::options(),
            'userRoles'    => UserRoleEnum::options(),
            'initialUsers' => [
                'data' => $users->items(),
                'meta' => [
                    'total'        => $users->total(),
                    'per_page'     => $users->perPage(),
                    'current_page' => $users->currentPage(),
                    'last_page'    => $users->lastPage(),
                ],
            ],
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $users = User::query()
            ->applyDataGrid($request)
            ->paginateForGrid($request);

        return response()->json(User::formatGridResponse($users));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', 'string', Rule::in(array_column(UserRoleEnum::cases(), 'value'))],
            'status'   => ['required', 'string', Rule::in(array_column(UserStatusEnum::cases(), 'value'))],
        ]);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => __('User created successfully.'),
            'user'    => $user->fresh(),
        ]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role'   => ['required', 'string', Rule::in(array_column(UserRoleEnum::cases(), 'value'))],
            'status' => ['required', 'string', Rule::in(array_column(UserStatusEnum::cases(), 'value'))],
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => __('User updated successfully.'),
            'user'    => $user->fresh(),
        ]);
    }

    public function updateStatus(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(array_column(UserStatusEnum::cases(), 'value'))],
        ]);

        $user->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => __('User status updated successfully.'),
            'user'    => $user->fresh(),
        ]);
    }

    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids'    => ['required', 'array', 'min:1'],
            'ids.*'  => ['required', 'integer', 'exists:users,id'],
            'status' => ['required', 'string', Rule::in(array_column(UserStatusEnum::cases(), 'value'))],
        ]);

        $count = User::whereIn('id', $validated['ids'])->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => __(':count user(s) status updated successfully.', ['count' => $count]),
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => __('User deleted successfully.'),
        ]);
    }

    public function bulkDestroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'exists:users,id'],
        ]);

        $count = User::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'success' => true,
            'message' => __(':count user(s) deleted successfully.', ['count' => $count]),
        ]);
    }
}
