<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FontteService
{
    private $token;
    private $baseUrl;

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
        $this->baseUrl = 'https://api.fonnte.com';
    }

    /**
     * Send WhatsApp message
     */
    public function sendMessage(string $target, string $message): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->baseUrl . '/send', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62', // Indonesia
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status']) {
                Log::info('WhatsApp message sent successfully', [
                    'target' => $target,
                    'response' => $result
                ]);
                return ['success' => true, 'data' => $result];
            } else {
                Log::error('Failed to send WhatsApp message', [
                    'target' => $target,
                    'response' => $result
                ]);
                return ['success' => false, 'error' => $result['reason'] ?? 'Unknown error'];
            }
        } catch (\Exception $e) {
            Log::error('Exception while sending WhatsApp message', [
                'target' => $target,
                'error' => $e->getMessage()
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send domain expiry notification
     */
    public function sendDomainExpiryNotification(string $target, $website, int $daysLeft): array
    {
        $message = "🚨 *PERINGATAN DOMAIN AKAN EXPIRED* 🚨\n\n";
        $message .= "📌 *Website:* {$website->name}\n";
        $message .= "🌐 *Domain:* {$website->domain}\n";
        $message .= "📅 *Tanggal Expired:* {$website->domain_expiry->format('d/m/Y')}\n";
        $message .= "⏰ *Sisa Waktu:* {$daysLeft} hari\n";
        $message .= "🏢 *Registrar:* {$website->registrar}\n\n";
        $message .= "⚠️ Segera lakukan perpanjangan domain untuk menghindari website down!";

        return $this->sendMessage($target, $message);
    }

    /**
     * Send hosting expiry notification
     */
    public function sendHostingExpiryNotification(string $target, $website, int $daysLeft): array
    {
        $message = "🚨 *PERINGATAN HOSTING AKAN EXPIRED* 🚨\n\n";
        $message .= "📌 *Website:* {$website->name}\n";
        $message .= "🌐 *URL:* {$website->url}\n";
        $message .= "📅 *Tanggal Expired:* {$website->hosting_expiry->format('d/m/Y')}\n";
        $message .= "⏰ *Sisa Waktu:* {$daysLeft} hari\n";
        $message .= "🏢 *Provider:* {$website->hosting_provider}\n\n";
        $message .= "⚠️ Segera lakukan perpanjangan hosting untuk menghindari website down!";

        return $this->sendMessage($target, $message);
    }

    /**
     * Send WiFi expiry notification
     */
    public function sendWifiExpiryNotification(string $target, $wifi, int $daysLeft): array
    {
        $message = "🚨 *PERINGATAN WiFi AKAN EXPIRED* 🚨\n\n";
        $message .= "📶 *Nama WiFi:* {$wifi->name}\n";
        $message .= "📍 *Lokasi:* {$wifi->location}\n";
        $message .= "📅 *Tanggal Expired:* {$wifi->service_expiry_date->format('d/m/Y')}\n";
        $message .= "⏰ *Sisa Waktu:* {$daysLeft} hari\n";
        $message .= "🏢 *Provider:* " . ($wifi->provider ? $wifi->provider->name : 'Tidak ada') . "\n";
        $message .= "💰 *Biaya Bulanan:* Rp " . number_format($wifi->monthly_cost ?? 0, 0, ',', '.') . "\n\n";
        $message .= "⚠️ Segera lakukan perpanjangan layanan WiFi untuk menghindari gangguan koneksi internet!";

        return $this->sendMessage($target, $message);
    }
}