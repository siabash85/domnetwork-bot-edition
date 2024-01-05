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
        $user = auth()->user();

        if ($user->is_partner) {
            $users = $user->users;
        } else {
            $users = User::query()->get();
        }
        $users = UserResource::collection($users);
        return $this->successResponse($users, "");
    }

    public function select()
    {
        $user = auth()->user();
        if ($user->is_partner) {
            $users = $user->users;
        } else {
            $users = User::query()->get();
        }
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
        $user = auth()->user();
        $data = [
            'is_superuser' => $request->is_superuser,
            'is_notifable' => $request->is_notifable,
            'is_partner' => $request->is_partner,
            'partner_id' => $user->id,
            'status' => $request->status,
            'username' => $request->username,
            'first_name' => $request->first_name,
            'email' => $request->email,
            // 'mobile' => $request->mobile,
            'wallet' => $request->wallet,
        ];
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }
        $user = User::query()->create($data);
        return $this->successResponse($user, "ایجاد  با موفقیت انجام شد");
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
            'first_name' => $request->first_name,
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
