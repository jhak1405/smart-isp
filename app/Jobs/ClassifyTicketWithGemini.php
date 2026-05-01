<?php

namespace App\Jobs;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClassifyTicketWithGemini implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * El número de veces que se puede intentar el job.
     */
    public $tries = 5;

    public function __construct(
        public Ticket $ticket
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $apiKey = env('GEMINI_API_KEY');

        if (!$apiKey) {
            Log::error('Gemini API Key is missing. Cannot classify ticket.');
            return;
        }

        $prompt = 'Actúa como un experto en soporte técnico de un ISP (Proveedor de Internet). Analiza esta descripción de avería: "' . $this->ticket->descripcion . '". Devuelve ÚNICAMENTE un JSON válido sin formato markdown ni comillas invertidas, con las siguientes tres claves exactas: "ia_categoria" (Opciones: Fibra, Router, Antena, Pagos, General), "ia_prioridad" (Opciones: Alta, Media, Baja), y "ia_resumen" (Un resumen técnico de la falla en máximo 15 palabras).';

        try {
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

            // Si es un error 429 (Too Many Requests) o 503 (Service Unavailable),
            // pausamos y reintentamos.
            if ($response->status() === 429 || $response->status() === 503) {
                Log::warning('Gemini API limit reached or unavailable (Status ' . $response->status() . '). Retrying job in 30 seconds.');
                $this->release(30);
                return;
            }

            if ($response->failed()) {
                Log::error('Gemini API falló irremediablemente: ' . $response->body());
                return;
            }

            $data = $response->json();
            $aiText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

            if (!$aiText) {
                Log::error('Gemini API no devolvió texto. Respuesta: ' . $response->body());
                return;
            }

            $cleanJson = preg_replace('/^```json|```$/m', '', $aiText);
            $cleanJson = trim($cleanJson);

            $decoded = json_decode($cleanJson, true);

            if ($decoded) {
                // Actualizar el ticket en la BD sin disparar eventos de nuevo para no causar loops infinitos
                $this->ticket->ia_categoria = $decoded['ia_categoria'] ?? 'General';
                $this->ticket->ia_prioridad = $decoded['ia_prioridad'] ?? 'Media';
                $this->ticket->ia_resumen = $decoded['ia_resumen'] ?? 'Sin resumen';
                $this->ticket->saveQuietly();
            } else {
                Log::error('Gemini API devolvió JSON inválido: ' . $cleanJson);
            }
        } catch (\Exception $e) {
            Log::error('Excepción al clasificar ticket con Gemini: ' . $e->getMessage());
            // Reintentar si falla la conexión
            $this->release(30);
        }
    }
}
