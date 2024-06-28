<?php

namespace App\Console\Commands;

use App\Models\Company\BankAccount;
use App\Models\Company\BankAccountBalance;
use App\Models\Users;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateCompanyBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bank:balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Company bank balance';

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
        $accounts = BankAccount::get();
        foreach ($accounts as $account) {
            $today = BankAccountBalance::whereDate('created_at', Carbon::today())
                ->where(['company_bank_account_id' => $account->id])
                ->first();
            if (!$today) {
                $yesterday = BankAccountBalance::whereDate('created_at', Carbon::yesterday())
                    ->where(['company_bank_account_id' => $account->id])
                    ->first();
                if ($yesterday) {
                    $user = Users::where(['id' => $yesterday->user_id])->first();
                    if ($user) {
                        BankAccountBalance::create([
                            'user_id' => $yesterday->user_id,
                            'company_id' => $yesterday->company_id,
                            'company_bank_account_id' => $yesterday->company_bank_account_id,
                            'balance' => $yesterday->balance,
                            'remark' => 'System auto generated',
                        ]);
                    }
                }

            }
        }
        return 0;
    }
}
