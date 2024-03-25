<?php

namespace App\Service\Integration;

use Hyperf\Config\ConfigFactory;
use Hyperf\Context\ApplicationContext;

class NotificationService extends AbstractIntegrationService
{
    protected const TOKEN = '';

    protected string $url;

    public function __construct()
    {
        $config = new ConfigFactory();
        $config = $config(ApplicationContext::getContainer());
        $this->url = $config->get('integrations.notification_url');
        $this->make('');
    }

}