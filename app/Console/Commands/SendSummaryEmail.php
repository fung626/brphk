<?php

namespace App\Console\Commands;

use App\Mail\SummaryReminder;
// use App\Models\Rent\Payment as RentPayment;
use App\Models\User\Notification;
use App\Mylibs\CashFlow;
use App\Mylibs\Common;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendSummaryEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send summary email';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $rentPayment = RentPayment::sum('amount');
        $cashFlow = CashFlow::total();
        // $bankBalance = BankAccountBalance::sum('balance');
        // $expenses = Expenses::sum('amount');

        $data = [
            [
                'name' => __('Cash Flow') . ' ' . __('Amount'),
                'amount' => Common::formatPrice($cashFlow),
            ],
        ];
        // Log::debug($data);
        $to = [];

        $notifications = Notification::with('user')
            ->where([
                'type' => 'summary',
                'enable' => 1,
            ])->get();

        foreach ($notifications as $notification) {
            if (isset($notification->user->email)) {
                $to[] = [
                    'email' => $notification->user->email,
                    'name' => $notification->user->name ? $notification->user->name : $notification->user->email,
                ];
            }
        }

        try {
            Mail::to($to)->locale('tc')->send(new SummaryReminder($data));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return 0;
    }
}