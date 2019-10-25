<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatbotUserCollection;
use App\Http\Resources\ChatbotUserResource;
use App\Http\Resources\MessageCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenDialogAi\ConversationLog\ChatbotUser;
use OpenDialogAi\ConversationLog\Message;

class ChatbotUsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $order = $request->get('order');
        $sort = ($request->get('sort')) ? $request->get('sort') : 'desc';
        $interact = ($request->get('interact')) ? true : false;

        $chatbotUsersQuery = ChatbotUser::when($order, function ($query, $order) use ($sort) {
            if ($order == 'first_seen') {
                $query->orderBy('chatbot_users.created_at', $sort);
            } elseif ($order == 'last_seen') {
                $query->leftJoin('messages', 'chatbot_users.user_id', '=', 'messages.user_id')
                    ->select(
                        'chatbot_users.*',
                        DB::raw('greatest(ifnull(max(messages.created_at), 0), chatbot_users.created_at) last_seen')
                    )
                    ->groupBy('chatbot_users.user_id')
                    ->orderBy('last_seen', $sort);
            }
        }, function ($query) {
            $query;
        });

        if ($interact) {
            $chatbotUsersQuery
                ->join('messages as m', 'chatbot_users.user_id', '=', 'm.user_id')
                ->where('m.author', '<>', 'them')
                ->where('m.type', '<>', 'chat_open');
        }

        $chatbotUsers = $chatbotUsersQuery->paginate(50);

        return new ChatbotUserCollection($chatbotUsers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return new ChatbotUserResource(ChatbotUser::find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function messages($id)
    {
        $messages = Message::where('user_id', $id)
            ->where('type', '<>', 'chat_open')
            ->where('type', '<>', 'trigger')
            ->orderBy('microtime')
            ->get();

        return new MessageCollection($messages);
    }
}
