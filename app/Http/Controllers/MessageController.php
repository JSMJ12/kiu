<?php

namespace App\Http\Controllers;

use App\Notifications\NewMessageNotification2;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Events\MessageUpdated;
use RealRashid\SweetAlert\Facades\Alert;
use Config;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 5);
        $messages = auth()->user()->receivedMessages()
                                ->orderBy('created_at', 'desc') // Ordenar por fecha de envío en orden descendente
                                ->with('sender', 'receiver') // Cargar las relaciones sender y receiver
                                ->paginate($perPage); // Paginar los resultados

        return view('messages.index', compact('messages', 'perPage'));
    }

    public function create()
    {
        return view('messages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required',
            'receiver_id' => 'required',
            'attachment' => 'nullable|file|max:100240',
        ]);

        // Crear un nuevo mensaje
        $message = new Message([
            'sender_id' => auth()->user()->id,
            'receiver_id' => $request->input('receiver_id'),
            'message' => $request->input('message'),
        ]);

        // Manejar la subida de archivos
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
            $message->attachment = $attachmentPath;
        }

        // Guardar el mensaje
        
        $message->save();
        
        $receiver = $message->receiver;
        
        $receiver->notify(new NewMessageNotification2($message));

        Alert::success('Mensaje enviado', 'El mensaje se envió correctamente');

        $unreadMessagesCount = Message::where('receiver_id', auth()->id())
        ->whereNull('read_at')
        ->count();

        return back();
    }
    public function destroy($id)
    {
        $message = Message::find($id);
        $message->delete();
        return redirect()->route('messages.index')->with('success', 'Message deleted successfully.');
    }
    public function destroyMultiple(Request $request)
    {
        // Obtener los IDs de los mensajes a eliminar
        $messageIds = $request->input('message_ids', []);
        Message::whereIn('id', $messageIds)->delete();
        return redirect()->route('messages.index')->with('notifications', 'Mensajes eliminados con éxito.');
    }
}
