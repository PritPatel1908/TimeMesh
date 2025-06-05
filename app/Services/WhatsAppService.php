<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class WhatsAppService
{
    /**
     * Send WhatsApp message using the WhatsApp API.
     *
     * @param string $phoneNumber
     * @param string $message
     * @param string|null $firstName
     * @param string|null $lastName
     * @return bool
     */
    public function sendMessage(string $phoneNumber, string $message, string $firstName = null, string $lastName = null): bool
    {
        try {
            // Remove any spaces or special characters from the phone number
            $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

            // Add country code if not present (assuming India +91)
            if (strlen($phoneNumber) === 10) {
                $phoneNumber = '91' . $phoneNumber;
            }

            // Log the attempt
            Log::info("Attempting to send WhatsApp message to: {$phoneNumber}");

            // Get API settings from config
            $fromMobileNo = Config::get('services.whatsapp.from_mobile_no', '918200599525');
            $fbToken = Config::get('services.whatsapp.fb_token', 'EAB8uAArmndABO4ewJWGktpcbuBfZAiFV6XUIOYdEr9FR8L05gprGxlb1Sx9DNFwY9q1A8f3NcZAcs4b7DhE9nZBT5ZACPLxNIL3J0vTNHpvuam7zfrsHcwYilrmVybIowxY7xWapORJIUoPMcqWykvnFjgsnBqvZBURIzKEkUMZAfdPlQJdQ0b6W63W2sBgJ6i');
            $apiEndpoint = Config::get('services.whatsapp.api_endpoint', 'https://us-central1-pristine-nomad-264707.cloudfunctions.net/SendTemplateWhatsappV2');
            $hostelLocation = Config::get('services.whatsapp.hostel_location', 'Nikol');
            $contactNumber = Config::get('services.whatsapp.contact_number', '9876543210');
            $hostelName = Config::get('services.whatsapp.hostel_name', 'LN Stay');

            // Parse message to extract required variables
            $messageParts = $this->parseMessage($message, $firstName, $lastName);

            // Prepare payload for WhatsApp API
            $payload = [
                "FromMobileNo" => $fromMobileNo,
                "FBToken" => $fbToken,
                "toMobileNo" => $phoneNumber,
                "TemplateName" => "inoutpunch",
                "TemplateLanguage" => "en",
                "TemplateMsgString" => "{{ 1 }}\nDear {{ 2 }},\n\nWe would like to inform you that your ward {{ 3 }} has {{ 4 }} the hostel premises {{ 5 }} at {{ 6 }}\n\nIf you have any questions, feel free to contact us at {{ 7 }}\n\n- {{ 8 }}\n\n{{ 9 }}",
                "EmployeeName" => Config::get('services.whatsapp.employee_name', 'Suhag Patel'),
                "Variables" => [
                    ["Variable" => "Greetings"],
                    ["Variable" => $messageParts['guardian_name']],
                    ["Variable" => $messageParts['student_name']],
                    ["Variable" => ucfirst($messageParts['status'])],
                    ["Variable" => $hostelLocation],
                    ["Variable" => $messageParts['time']],
                    ["Variable" => $contactNumber],
                    ["Variable" => $hostelName],
                    ["Variable" => "Thank you"]
                ]
            ];

            // Send request to WhatsApp API
            $response = Http::post($apiEndpoint, $payload);

            // Check if the request was successful
            if ($response->successful()) {
                Log::info("WhatsApp message sent successfully to: {$phoneNumber}");
                return true;
            } else {
                Log::error("Failed to send WhatsApp message. API response: " . $response->body());
                return false;
            }
        } catch (Exception $e) {
            // Log any exceptions
            Log::error("Exception while sending WhatsApp message: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Parse message to extract relevant information for the template.
     *
     * @param string $message
     * @param string|null $firstName
     * @param string|null $lastName
     * @return array
     */
    private function parseMessage(string $message, string $firstName = null, string $lastName = null): array
    {
        $result = [
            'guardian_name' => 'Guardian',
            'student_name' => 'Student',
            'status' => 'entered',
            'time' => date('H:i'),
        ];

        // Extract guardian name
        if (preg_match('/Dear\s+([^,]+),/', $message, $matches)) {
            $result['guardian_name'] = trim($matches[1]);
        }

        // Use provided first name and last name if available
        if ($firstName !== null && $lastName !== null) {
            $result['student_name'] = trim("$firstName $lastName");
        } else {
            // Fallback to extracting from message
            if (preg_match('/your ward\s+([^has]+)has/', $message, $matches)) {
                $result['student_name'] = trim($matches[1]);
            }
        }

        // Extract status (entered/exited)
        if (preg_match('/has\s+(entered|exited)/', $message, $matches)) {
            $result['status'] = trim($matches[1]);
        }

        // Extract time
        if (preg_match('/at\s+([0-9]{1,2}-[0-9]{1,2}-[0-9]{4}\s+[0-9]{1,2}:[0-9]{2}\s+[AP]M)/', $message, $matches)) {
            $time = \DateTime::createFromFormat('d-m-Y h:i A', trim($matches[1]));
            if ($time) {
                $result['time'] = $time->format('H:i');
            }
        }

        return $result;
    }
}
