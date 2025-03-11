<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Config;

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
    protected $description = 'Test SAML2 configuration with Socialite';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing SAML2 integration with Socialite...');

        try {
            // Get the SAML2 configuration from services.php
            $config = Config::get('services.saml2');

            if (empty($config)) {
                $this->error('No SAML2 configuration found in config/services.php');
                return 1;
            }

            $this->info('SAML2 Configuration:');
            $this->info('-----------------------------------');

            // Display IdP information
            if (isset($config['metadata'])) {
                $this->info('IdP Metadata URL/XML: ' . (is_string($config['metadata']) && strlen($config['metadata']) > 50
                        ? substr($config['metadata'], 0, 47) . '...'
                        : $config['metadata']));
            } elseif (isset($config['entityid'])) {
                $this->info('IdP Entity ID: ' . $config['entityid']);
                $this->info('IdP ACS URL: ' . ($config['acs'] ?? '[not set]'));
                $this->info('IdP Certificate: ' . (isset($config['certificate'])
                        ? (strlen($config['certificate']) > 50 ? 'Present (truncated)' : $config['certificate'])
                        : '[not set]'));
            } else {
                $this->warn('No IdP configuration found. Please configure "metadata" or "entityid" in services.saml2');
            }

            // Display SP information
            $this->info('SP Entity ID: ' . ($config['sp_entityid'] ?? Socialite::driver('saml2')->getServiceProviderEntityId()));
            $this->info('SP ACS URL: ' . Socialite::driver('saml2')->getServiceProviderAssertionConsumerUrl());

            // Test metadata generation
            $this->info('Testing metadata generation...');
            $metadata = Socialite::driver('saml2')->getServiceProviderMetadata();
            $this->info('Successfully generated metadata (' . strlen($metadata) . ' bytes)');

            // Show routes
            $this->info('Routes:');
            $this->info('Login URL: ' . url('saml2/login'));
            $this->info('ACS URL: ' . url('saml2/acs'));
            $this->info('Metadata URL: ' . url('saml2/metadata'));

            $this->info('-----------------------------------');
            $this->info('SAML2 test complete - Configuration looks valid');

        } catch (\Exception $e) {
            $this->error('Error testing SAML2 configuration: ' . $e->getMessage());
            $this->error('Stack trace:');
            $this->error($e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
