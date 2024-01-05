<?php

namespace Modules\Common\Http\Controllers\Panel;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\User\Entities\User;
use Modules\Order\Entities\Order;
use Modules\Server\Entities\Service;
use Modules\Payment\Entities\Payment;
use Modules\Server\Entities\Subscription;
use Modules\Support\Entities\SupportMessage;
use Modules\Common\Http\Controllers\Api\ApiController;

class DashboardController extends ApiController
{

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $user = auth()->user();
        $payments = Payment::query()->where('status', 'success')->get();
        $orders = Order::query()->whereHas("user", function ($q) use ($user) {
            $q->where("partner_id", $user->id);
        })->get();
        $payments_total = $payments->sum('amount');
        $payments_count = $payments->count();
        if ($user->is_partner) {
            $users = $user->users;
            $active_services = Subscription::query()->where('status', 'active')->whereHas("user", function ($q) use ($user) {
                $q->where("partner_id", $user->id);
            })->get()->count();
        } else {
            $users = User::query()->get();
            $active_services = Subscription::query()->where('status', 'active')->get()->count();
        }
        $users_count = $users->count();
        $messages = SupportMessage::query();
        $messages_count = $messages->get()->count();
        $answered_messages_count = $messages->where('status', 'answered')->get()->count();
        $pending_messages_count = $messages->where('status', 'pending')->get()->count();

        $orders_total = $orders->sum('payable_price');
        $orders_count = $orders->count();
        $data = [
            'orders_total' => $orders_total,
            'orders_count' => $orders_count,
            'payments_total' => $payments_total,
            'payments_count' => $payments_count,
            'users_count' => $users_count,
            'messages_count' => $messages_count,
            'answered_messages_count' => $answered_messages_count,
            'pending_messages_count' => $pending_messages_count,
            'active_services' => $active_services
        ];
        return $this->successResponse($data);
    }
}
