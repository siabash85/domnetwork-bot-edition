<?php

namespace Modules\Support\Http\Controllers\Panel;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Telegram\Bot\Laravel\Facades\Telegram;
use Modules\Support\Entities\SupportMessage;
use Modules\Common\Http\Controllers\Api\ApiController;
use Modules\Support\Http\Requests\Panel\SupportMessageRequest;
use Modules\Support\Transformers\Panel\SupportMessageResource;

class SupportController extends ApiController
{

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $messages = SupportMessage::query()->get();
        $messages = SupportMessageResource::collection($messages);
        return $this->successResponse($messages, "");
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(SupportMessageRequest $request)
    {
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $message = SupportMessage::query()->find($id);
        $message = new SupportMessageResource($message);
        return $this->successResponse($message, "");
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(SupportMessageRequest $request, $id)
    {
        $message = SupportMessage::query()->find($id);
        $message->update([
            'answer' => $request->answer,
            'status' => "answered",
        ]);
        Telegram::sendMessage([
            'text' => $request->answer,
            'chat_id' => $message->user->uid,
        ]);
        return $this->successResponse($message, "ویرایش  با موفقیت انجام شد");
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $message = SupportMessage::query()->find($id);
        $message->delete();
        return $this->successResponse($message, "حذف  با موفقیت انجام شد");
    }
}
