<?php

namespace Modules\Notification\Http\Controllers\Panel;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\User\Entities\User;
use App\Telegram\Keyboard\KeyboardHandler;
use Telegram\Bot\Laravel\Facades\Telegram;
use Modules\Notification\Entities\UserMessage;
use Modules\Common\Http\Controllers\Api\ApiController;
use Modules\Notification\Http\Requests\Panel\UserMessageRequest;

class UserMessageController extends ApiController
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $messages = UserMessage::query()->get();
        return $this->successResponse($messages, "");
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(UserMessageRequest $request)
    {
        $message =  UserMessage::query()->create([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        $notif_message = "📌 {$request->title} \n\n" .
            "{$request->content}";

        $selected_users =  explode(",", $request->users);

        if (!$request->users) {
            $users = User::query()->get();
        } else {
            $users = User::query()->whereIn('id', $selected_users)->get();
        }

        foreach ($users as $key => $user) {
            try {
                Telegram::sendMessage([
                    'text' => $notif_message,
                    "chat_id" => $user->uid,
                    // 'parse_mode' => 'MarkdownV2',
                    'reply_markup' => KeyboardHandler::home(),
                ]);
            } catch (\Throwable $th) {
                //throw $th;
            }
        };


        return $this->successResponse($message, "ارسال  با موفقیت انجام شد");
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $message = UserMessage::query()->find($id);
        return $this->successResponse($message, "");
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(UserMessageRequest $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
    }
}
