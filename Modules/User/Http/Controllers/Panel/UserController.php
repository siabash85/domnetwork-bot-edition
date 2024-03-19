<?php

namespace Modules\User\Http\Controllers\Panel;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\User\Entities\User;
use Modules\Order\Entities\Order;
use Illuminate\Support\Facades\Hash;
use Modules\Server\Entities\Subscription;
use Telegram\Bot\Laravel\Facades\Telegram;
use Modules\User\Http\Requests\Panel\UserRequest;
use Modules\User\Transformers\Panel\UserResource;
use Modules\Common\Http\Controllers\Api\ApiController;

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
    public function report(Request $request, $id)
    {
        $user = User::find($id);
        $users = $user->users;
        $users = UserResource::collection($users);
        $orders = Order::query()->where('status', 'success')->whereHas("user", function ($q) use ($user) {
            $q->where("partner_id", $user->id);
        })->get();
        $active_services = Subscription::query()->where('status', 'active')->whereHas("user", function ($q) use ($user) {
            $q->where("partner_id", $user->id);
        })->get()->count();
        $orders_total = $orders->sum('payable_price');
        $orders_count = $orders->count();
        $users_count = $users->count();
        $data = [
            "users" => $users,
            'statics' => [
                'users_count' => $users_count,
                'orders_total' => $orders_total,
                'orders_count' => $orders_count,
                'active_services' => $active_services
            ]
        ];
        return $this->successResponse($data, "");
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
            'is_partner' => $request->is_partner,
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
