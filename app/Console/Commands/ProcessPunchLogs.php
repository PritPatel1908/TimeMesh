<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\PunchLog;
use App\Models\WhatsappStatus;
use Illuminate\Console\Command;
use App\Services\WhatsAppService;

class ProcessPunchLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'punch-logs:process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process unprocessed punch logs and send WhatsApp messages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing unprocessed punch logs...');

        // Get unprocessed punch logs
        $unprocessedLogs = PunchLog::where('is_process', false)
            ->orderBy('punch_date_time', 'asc')
            ->get();

        if ($unprocessedLogs->isEmpty()) {
            $this->info('No unprocessed punch logs found.');
            return 0;
        }

        $this->info("Found {$unprocessedLogs->count()} unprocessed punch logs.");

        $processed = 0;
        $failed = 0;
        $whatsAppService = new WhatsAppService();

        foreach ($unprocessedLogs as $log) {
            // Get user information
            $user = User::where('user_code', $log->user_code)->first();

            if (!$user) {
                $this->warn("User not found for user_code: {$log->user_code}");
                // Mark as processed even if user not found to avoid repeated processing
                $log->is_process = true;
                $log->save();
                $failed++;
                continue;
            }

            // Prepare message based on punch status
            $status = $log->punch_status ? 'entered' : 'exited';
            $message = "Dear {$user->guardian_name}, your ward {$user->first_name} {$user->last_name} has {$status} the hostel at " .
                $log->punch_date_time->format('d-m-Y h:i A') . ".";

            $this->line("Sending WhatsApp message to {$user->guardian_contact_no} for {$user->first_name} {$user->last_name}");

            // Get WhatsApp status
            $whatsappStatus = WhatsappStatus::first();

            if (!$whatsappStatus) {
                $whatsappStatus = WhatsappStatus::create([
                    'total_message_count' => 0,
                    'success_message_count' => 0,
                ]);
            }

            // Send WhatsApp message
            $messageSent = $whatsAppService->sendMessage($user->guardian_contact_no, $message);

            // Update WhatsApp status counts
            $whatsappStatus->total_message_count += 1;

            if ($messageSent) {
                // Message sent successfully
                $this->info("Message sent successfully to {$user->guardian_contact_no}");
                $whatsappStatus->success_message_count += 1;
                $log->send_message = true;
                $processed++;
            } else {
                // Message sending failed
                $this->error("Failed to send message to {$user->guardian_contact_no}");
                $failed++;
            }

            $whatsappStatus->save();

            // Mark log as processed regardless of message status
            $log->is_process = true;
            $log->save();
        }

        $this->info("Processed: {$processed}, Failed: {$failed}, Total: {$unprocessedLogs->count()}");
        return 0;
    }
}
