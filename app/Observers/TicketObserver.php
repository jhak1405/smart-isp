<?php

namespace App\Observers;

use App\Models\Ticket;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TicketObserver
{
    /**
     * Handle the Ticket "creating" event.
     * Usa la API de Google Gemini para auto-clasificar el ticket
     * antes de guardarlo en la base de datos.
     */
    public function creating(Ticket $ticket): void
    {
        try {
            $apiKey = env('GEMINI_API_KEY');

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
                ]
            );

            // Extracción de datos con acceso directo al array
            $data = $response->json();
            $aiText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            // Limpieza de formato: eliminar bloques de código markdown
            $cleanJson = preg_replace('/^```json|```$/m', '', $aiText);
            $cleanJson = trim($cleanJson);

            $decoded = json_decode($cleanJson, true);

            if ($decoded) {
                $ticket->ia_categoria = $decoded['ia_categoria'] ?? 'General';
                $ticket->ia_prioridad = $decoded['ia_prioridad'] ?? 'Media';
                $ticket->ia_resumen = $decoded['ia_resumen'] ?? 'Sin resumen';
            }
        } catch (\Exception $e) {
            Log::error('Gemini API error al clasificar ticket: ' . $e->getMessage());

            $ticket->ia_categoria = null;
            $ticket->ia_prioridad = null;
            $ticket->ia_resumen = null;
        }
    }
}
