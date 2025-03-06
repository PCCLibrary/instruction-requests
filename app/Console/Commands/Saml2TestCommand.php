<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Slides\Saml2\Facades\Auth as Saml2Auth;
use Slides\Saml2\Models\Tenant;

class Saml2TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saml2:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test SAML2 configuration and tenant setup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing SAML2 integration...');

        // Check if we have tenants
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $this->error('No SAML2 tenants found. Please create one using "php artisan saml2:create-tenant"');
            return 1;
        }

        $this->info('Found ' . $tenants->count() . ' tenant(s):');

        foreach ($tenants as $tenant) {
            $this->info('-----------------------------------');
            $this->info('Tenant ID: ' . $tenant->id);
            $this->info('Tenant UUID: ' . $tenant->uuid);
            $this->info('Tenant Key: ' . $tenant->key);
            $this->info('Entity ID: ' . $tenant->entity_id);
            $this->info('Login URL: ' . $tenant->login_url);
            $this->info('Logout URL: ' . $tenant->logout_url);
            $this->info('Has x509 cert: ' . (!empty($tenant->x509cert) ? 'Yes' : 'No'));

            $this->info('Testing auth service with this tenant...');

            try {
                // Try to instantiate the SAML service with this tenant
                $auth = Saml2Auth::tenantId($tenant->uuid);

                // Try to get metadata
                $metadata = $auth->getMetadata();
                $this->info('Successfully generated metadata');

                // Display SP endpoints that should be registered with the IdP
                $this->info('SP Entity ID: ' . config('saml2.sp.entityId', '[not set]'));
                $this->info('SP ACS URL: ' . config('app.url') . '/saml2/' . $tenant->uuid . '/acs');
                $this->info('SP SLS URL: ' . config('app.url') . '/saml2/' . $tenant->uuid . '/sls');

            } catch (\Exception $e) {
                $this->error('Error testing tenant: ' . $e->getMessage());
                $this->error('Stack trace:');
                $this->error($e->getTraceAsString());
            }
        }

        $this->info('-----------------------------------');
        $this->info('SAML2 test complete');

        return 0;
    }
}
