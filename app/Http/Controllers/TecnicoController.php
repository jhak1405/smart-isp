<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Pendiente;
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

        // Pendientes asignados al técnico (solo los no completados), ordenados por fecha
        $pendientes = Pendiente::with('cliente')
            ->where('user_id', Auth::id())
            ->where('estado', 'Pendiente')
            ->orderBy('fecha_recordatorio', 'asc')
            ->get();

        return view('tecnico.dashboard', compact('tickets', 'notifications', 'pendientes'));
    }

    public function getNotifications()
    {
        $notifications = Auth::user()->notifications()->whereNull('read_at')->latest()->limit(5)->get();
        return response()->json($notifications);
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
            'latitud'      => 'nullable|numeric',
            'longitud'     => 'nullable|numeric',
            'evidencia'    => 'required|image|max:10240',
            'foto_fachada' => 'nullable|image|max:10240',
            'nota_tecnico' => 'nullable|string',
        ]);

        $ticket = Ticket::with('cliente')->where('user_id', Auth::id())->findOrFail($id);

        // 1. Guardar la Evidencia del trabajo (PERTENECE AL TICKET)
        if ($request->hasFile('evidencia')) {
            $pathEvidencia = $request->file('evidencia')->store('evidencias-tickets', 'public');
            $ticket->evidencia = $pathEvidencia;
        }

        // 2. Guardar la Foto de Fachada (PERTENECE AL CLIENTE)
        if ($request->hasFile('foto_fachada') && $ticket->cliente) {
            $pathFachada = $request->file('foto_fachada')->store('fotos-clientes', 'public');
            $ticket->cliente->foto_fachada = $pathFachada;
        }

        $ticket->estado            = 'Resuelto';
        $ticket->latitud_capturada = $request->latitud;
        $ticket->longitud_capturada = $request->longitud;
        $ticket->nota_tecnico      = $request->nota_tecnico;
        $ticket->save();

        // Actualizar coordenadas en el cliente si fueron provistas
        if ($ticket->cliente && $request->latitud && $request->longitud) {
            $ticket->cliente->latitud  = $request->latitud;
            $ticket->cliente->longitud = $request->longitud;
            $ticket->cliente->save();
        }

        return redirect()->route('tecnico.dashboard')->with('success', 'Ticket resuelto correctamente.');
    }

    /**
     * Marcar notificaciones como leídas (para el técnico)
     */
    public function markNotificationsAsRead()
    {
        Auth::user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    /**
     * Marcar un pendiente como completado desde el dashboard del técnico.
     */
    public function completarPendiente(Request $request, $id)
    {
        $pendiente = Pendiente::where('user_id', Auth::id())->findOrFail($id);
        $pendiente->estado = 'Completado';
        $pendiente->save();

        return redirect()->route('tecnico.dashboard')->with('success', 'Pendiente marcado como completado.');
    }
}
