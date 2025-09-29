<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailSend extends Command
{
    protected $signature = 'mail:test {to}';
    protected $description = 'Send a test mail to the given address';

    public function handle()
    {
        $to = $this->argument('to');
        Mail::raw('Dies ist eine Testmail vom THW-Trainer!', function ($message) use ($to) {
            $message->to($to)
                ->subject('THW-Trainer Testmail');
        });
        $this->info('Testmail wurde an ' . $to . ' gesendet.');
    }
}
