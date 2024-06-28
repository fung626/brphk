<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        //
        Passport::routes();

        // Passport::tokensExpireIn(Carbon::now()->addDays(30));
        // Passport::personalAccessTokensExpireIn(Carbon::now()->addDays(30));

        Passport::tokensCan([
            //
            'view-user' => 'View User',
            'create-user' => 'Create User',
            'update-user' => 'Update User',
            'delete-user' => 'Delete User',
            'export-user' => 'Export User',
            //
            'view-company' => 'View Company',
            'create-company' => 'Create Company',
            'update-company' => 'Update Company',
            'delete-company' => 'Delete Company',
            //
            'view-company-bank-account' => 'View Company Bank Account',
            'create-company-bank-account' => 'Create Company Bank Account',
            'update-company-bank-account' => 'Update Company Bank Account',
            'delete-company-bank-account' => 'Delete Company Bank Account',
            //
            'view-debt' => 'View Debt',
            'create-debt' => 'Create Debt',
            'update-debt' => 'Update Debt',
            'delete-debt' => 'Delete Debt',
            //
            'view-rent' => 'View Rent',
            'create-rent' => 'Create Rent',
            'update-rent' => 'Update Rent',
            'delete-rent' => 'Delete Rent',
            //
            'view-profit' => 'View Profit',
            'create-profit' => 'Create Profit',
            'update-profit' => 'Update Profit',
            'delete-profit' => 'Delete Profit',
            //
            'view-expenses' => 'View Expenses',
            'create-expenses' => 'Create Expenses',
            'update-expenses' => 'Update Expenses',
            'delete-expenses' => 'Delete Expenses',
            //
            'view-expected-expenses' => 'View Expected Expenses',
            'create-expected-expenses' => 'Create Expected Expenses',
            'update-expected-expenses' => 'Update Expected Expenses',
            'delete-expected-expenses' => 'Delete Expected Expenses',
            //
            'view-venue' => 'View Venue',
            'create-venue' => 'Create Venue',
            'update-venue' => 'Update Venue',
            'delete-venue' => 'Delete Venue',

        ]);

    }
}