<?php

namespace Modules\User\Http\Controllers\Panel;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Telegram\Bot\Laravel\Facades\Telegram;
use Modules\Common\Http\Controllers\Api\ApiController;
use Modules\User\Entities\User;
use Modules\User\Http\Requests\Panel\UserRequest;
use Modules\User\Transformers\Panel\UserResource;

class UserController extends ApiController
{

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $users = User::query()->get();
        $users = UserResource::collection($users);
        return $this->successResponse($users, "");
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(UserRequest $request)
    {
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $user = User::query()->find($id);
        $user = new UserResource($user);
        return $this->successResponse($user, "");
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(UserRequest $request, $id)
    {
        $user = User::query()->find($id);
        $data = [
            'is_superuser' => $request->is_superuser,
            'is_notifable' => $request->is_notifable,
            'status' => $request->status,
            'username' => $request->username,
            'fist_name' => $request->fist_name,
            'email' => $request->email,
            // 'mobile' => $request->mobile,
            'wallet' => $request->wallet,


        ];
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        return $this->successResponse($user, "ویرایش  با موفقیت انجام شد");
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $user = User::query()->find($id);
        $user->delete();
        return $this->successResponse($user, "حذف  با موفقیت انجام شد");
    }
}
