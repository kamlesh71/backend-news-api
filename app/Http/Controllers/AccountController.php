<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdatePreferenceRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function user(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    public function updatePreference(UpdatePreferenceRequest $request): JsonResponse
    {
        $validated = $request->validated();

        /** @var User $user */
        $user = $request->user();

        $user->fill($validated);
        $user->save();

        return response()->json([
            'success' => true
        ]);
    }
}
