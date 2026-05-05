<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Jobs\BroadcastTicketResolved;
use Illuminate\Support\Facades\Auth;

class TecnicoController extends Controller
{
    public function dashboard()
    {
        // Obtener solo tickets asignados al técnico actual que no estén cerrados o resueltos
        $tickets = Ticket::where('user_id', Auth::id())
            ->whereIn('estado', ['Abierto', 'En Proceso'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Obtener notificaciones sin leer del técnico actual
        $notifications = Auth::user()->notifications()->whereNull('read_at')->latest()->limit(5)->get();

        return view('tecnico.dashboard', compact('tickets', 'notifications'));
    }

    public function updateStatus(Request $request, $id)
    {
        $ticket = Ticket::where('user_id', Auth::id())->findOrFail($id);

        $ticket->estado = 'En Proceso';
        $ticket->save();

        return redirect()->back();
    }

    public function resolver(Request $request, $id)
    {
        \Illuminate\Support\Facades\Log::info("Intentando resolver ticket ID: " . $id);
        $request->validate([
            // Permitimos lat/long nulos para que el técnico pueda completar el ticket
            // si el GPS no está disponible; se recomienda usar GPS pero no bloquear la acción.
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'evidencia' => 'required|image|max:10240', // 10MB max
            'nota_tecnico' => 'nullable|string'
        ]);

        $ticket = Ticket::where('user_id', Auth::id())->findOrFail($id);

        if ($request->hasFile('evidencia')) {
            $path = $request->file('evidencia')->store('evidencias-tickets', 'public');
            $ticket->evidencia = $path;
        }

        $ticket->estado = 'Resuelto';
        $ticket->latitud_capturada = $request->latitud;
        $ticket->longitud_capturada = $request->longitud;
        $ticket->nota_tecnico = $request->nota_tecnico;
        $ticket->save();

        return redirect()->route('tecnico.dashboard')->with('success', 'Ticket resuelto correctamente.');
    }

    /**
     * Marcar notificaciones como leídas (para el tecnico)
     */
    public function markNotificationsAsRead()
    {
        Auth::user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
