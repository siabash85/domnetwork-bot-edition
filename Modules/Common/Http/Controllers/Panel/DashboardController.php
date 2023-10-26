<?php

namespace Modules\Common\Http\Controllers\Panel;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Common\Http\Controllers\Api\ApiController;
use Modules\Payment\Entities\Payment;
use Modules\Server\Entities\Service;
use Modules\Server\Entities\Subscription;
use Modules\Support\Entities\SupportMessage;
use Modules\User\Entities\User;

class DashboardController extends ApiController
{

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $payments = Payment::query()->where('status', 'success')->get();
        $payments_total = $payments->sum('amount');
        $payments_count = $payments->count();
        $users = User::query()->get();
        $users_count = $users->count();
        $messages = SupportMessage::query();
        $messages_count = $messages->get()->count();
        $answered_messages_count = $messages->where('status', 'answered')->get()->count();
        $pending_messages_count = $messages->where('status', 'pending')->get()->count();
        $active_services = Subscription::query()->where('status', 'active')->get()->count();
        $data = [
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
