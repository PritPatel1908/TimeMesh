<?php

namespace App\Services;

use Exception;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class WhatsAppService
{
    /**
     * Send WhatsApp message using the Twilio API.
     *
     * @param string $phoneNumber
     * @param string $message
     * @return bool
     */
    public function sendMessage(string $phoneNumber, string $message): bool
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

            // Get Twilio credentials from config
            $sid = Config::get('services.twilio.sid');
            $token = Config::get('services.twilio.token');
            $fromNumber = Config::get('services.twilio.whatsapp_from');
            $contentSid = Config::get('services.twilio.content_sid');

            // Create Twilio client
            $twilio = new Client($sid, $token);

            // Get user details from the message
            // Extract name, status, time, etc. from the message
            $messageParts = $this->parseMessage($message);

            // Send WhatsApp message using Twilio template
            $twilioMessage = $twilio->messages->create(
                'whatsapp:+' . $phoneNumber, // to
                [
                    'from' => $fromNumber,
                    'contentSid' => $contentSid,
                    'contentVariables' => json_encode([
                        '1' => $messageParts['guardian_name'] ?? 'Guardian',
                        '2' => $messageParts['student_name'] ?? 'Student',
                        '3' => $messageParts['location'] ?? 'Hostel',
                        '4' => $messageParts['time'] ?? date('H:i'),
                        '5' => $phoneNumber,
                        '6' => 'SardarPatel Girls Hostel',
                        '7' => 'Provided by www.sardarpatelhostel.com',
                    ]),
                ]
            );

            // Log the response
            Log::info("Twilio WhatsApp message sent with SID: " . $twilioMessage->sid);
            return true;
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
     * @return array
     */
    private function parseMessage(string $message): array
    {
        $result = [
            'guardian_name' => 'Guardian',
            'student_name' => 'Student',
            'location' => 'Hostel',
            'time' => date('H:i'),
        ];

        // Extract guardian name
        if (preg_match('/Dear\s+([^,]+),/', $message, $matches)) {
            $result['guardian_name'] = trim($matches[1]);
        }

        // Extract student name
        if (preg_match('/your ward\s+([^has]+)has/', $message, $matches)) {
            $result['student_name'] = trim($matches[1]);
        }

        // Extract time
        if (preg_match('/at\s+([0-9]{1,2}-[0-9]{1,2}-[0-9]{4}\s+[0-9]{1,2}:[0-9]{2}\s+[AP]M)/', $message, $matches)) {
            $time = \DateTime::createFromFormat('d-m-Y h:i A', trim($matches[1]));
            if ($time) {
                $result['time'] = $time->format('H:i');
            }
        }

        // Extract location (default is "Hostel")
        $result['location'] = 'Nikol';

        return $result;
    }
}
