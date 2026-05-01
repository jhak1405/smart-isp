<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
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

        return view('tecnico.dashboard', compact('tickets'));
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
        $request->validate([
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
            'evidencia' => 'required|image|max:10240', // 10MB max
            'nota_tecnico' => 'nullable|string'
        ]);

        $ticket = Ticket::where('user_id', Auth::id())->findOrFail($id);

        if ($request->hasFile('evidencia')) {
            $path = $request->file('evidencia')->store('evidencias-tickets', 'public');
            $ticket->evidencia = $path; // Esto asume que evidencia no está en fillable pero sí en DB, lo haremos manual
        }

        $ticket->estado = 'Resuelto';
        $ticket->latitud_capturada = $request->latitud;
        $ticket->longitud_capturada = $request->longitud;
        $ticket->nota_tecnico = $request->nota_tecnico;
        $ticket->save();

        return redirect()->route('tecnico.dashboard')->with('success', 'Ticket resuelto correctamente.');
    }
}
