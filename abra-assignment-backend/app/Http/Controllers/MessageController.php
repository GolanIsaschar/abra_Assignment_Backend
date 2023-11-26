<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user)
                return response()->json(['error' => 'Unauthorized - Please login and try again'], 401);

            $messageData = $request->validate([
                'userRecivierId' => 'required',
                'messageContent' => 'required',
                'messageSubject' => 'required'
            ]);

            $messageData['messageContent'] = strip_tags($messageData['messageContent']);
            $messageData['messageSubject'] = strip_tags($messageData['messageSubject']);
            $messageData['userSenderId'] = auth()->id();
            $messageData['isRead'] = 0;
            $newMessage = Message::create($messageData);

            return response()->json(['success' => 'Your messages sent succesfully', 'message' => $newMessage]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send message - please check your input'], 500);
        }
    }

    public function getAllMessage()
    {
        $user = $this->authenticateUser();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized - Please login and try again'], 401);
        }

        $messages = Message::where('userRecivierId', $user->id)->get();
        if ($messages->isEmpty())
            return response()->json(['error' => 'Not found messages for the logged user'], 404);

        return response()->json(['success' => 'Here is all your messages', 'messages' => $messages]);
    }

    public function getAllUnreadMessage()
    {
        $user = $this->authenticateUser();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized - Please login and try again'], 401);
        }

        $unreadMessages = Message::where('userRecivierId', $user->id)
            ->where('isRead', 0)
            ->get();

        if ($unreadMessages->isEmpty())
            return response()->json(['success' => 'No unread messages found for the logged user', 'unread_messages' => []]);

        return response()->json(['success' => 'Here is all your unread messages', 'unread messages' => $unreadMessages]);
    }

    public function readMessage($messageId)
    {
        $user = $this->authenticateUser();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized - Please login and try again'], 401);
        }

        $message = Message::where('messageId', $messageId)
            ->where('userRecivierId', $user->id)
            ->first();

        if (!$message) {
            return response()->json(['error' => 'Not found Unread message for the logged user - please choose message that you are the recivier'], 404);
        }

        if ($message->isRead == 1) {
            return response()->json(['error' => 'Message is already read']);
        }

        $message->isRead = 1;
        $message->save();

        return response()->json(['success' => 'Message mark as read successfully', 'message' => $message]);
    }

    public function deleteMessage($messageId)
    {
        $user = $this->authenticateUser();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized - Please login and try again'], 401);
        }

        $message = Message::find($messageId);
        if (!$message) {
            return response()->json(['error' => 'Message not found'], 404);
        }

        if ($user->id === $message->userSenderId || $user->id === $message->userRecivierId) {
            $deletedMessage = $message;
            $message->delete();
            return response()->json(['success' => 'Message deleted successfully', 'message' => $deletedMessage]);
        }

        return response()->json(['error' => 'You are not authorized to delete this message'], 403);
    }


    private function authenticateUser()
    {
        $user = Auth::user();

        if (!$user)
            return null;

        return $user;
    }
}
