<?php

namespace App\Observers;

use App\Models\Ticket;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TicketObserver
{
    /**
     * Handle the Ticket "creating" event.
     * Auto-clasificación síncrona instantánea antes de guardar en la BD.
     */
    public function creating(Ticket $ticket): void
    {
        try {
            $apiKey = env('GEMINI_API_KEY');

            if (!$apiKey) return;

            $prompt = 'Actúa como un experto en soporte técnico de un ISP (Proveedor de Internet). Analiza esta descripción de avería: "' . $ticket->descripcion . '". Devuelve ÚNICAMENTE un JSON válido sin formato markdown ni comillas invertidas, con las siguientes tres claves exactas: "ia_categoria" (Opciones: Fibra, Router, Antena, Pagos, General), "ia_prioridad" (Opciones: Alta, Media, Baja), y "ia_resumen" (Un resumen técnico de la falla en máximo 15 palabras).';

            $response = Http::post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey,
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'response_mime_type' => 'application/json',
                    ],
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                $aiText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                
                if ($aiText) {
                    // Limpieza por si acaso, aunque generationConfig lo garantiza
                    $cleanJson = preg_replace('/^```json|```$/m', '', $aiText);
                    $decoded = json_decode(trim($cleanJson), true);

                    if ($decoded) {
                        $ticket->ia_categoria = $decoded['ia_categoria'] ?? 'General';
                        $ticket->ia_prioridad = $decoded['ia_prioridad'] ?? 'Media';
                        $ticket->ia_resumen = $decoded['ia_resumen'] ?? 'Sin resumen';
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Excepción al clasificar ticket con Gemini: ' . $e->getMessage());
        }
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        // Si el ticket se marcó como Resuelto y tiene coordenadas
        if ($ticket->estado === 'Resuelto' && $ticket->latitud_capturada && $ticket->longitud_capturada) {
            $cliente = $ticket->cliente;
            if ($cliente) {
                // Actualizamos las coordenadas maestras del cliente
                $cliente->latitud = $ticket->latitud_capturada;
                $cliente->longitud = $ticket->longitud_capturada;
                $cliente->saveQuietly();
            }
        }
    }
}
