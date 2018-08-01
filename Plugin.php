<?php

namespace Kanboard\Plugin\PullMailTasks;

use Kanboard\Core\Security\Role;
use Kanboard\Core\Translator;
use Kanboard\Core\Plugin\Base;

/**
 * PullMailTasks Plugin
 *
 * @package  PullMailTasks
 * @author   Ralf Blumenthal/stratmaster
 */
class Plugin extends Base
{
    public function initialize()
    {
        $this->template->hook->attach('template:config:integrations', 'PullMailTasks:config/integration');
        $this->route->addRoute('/pullmailtasks/handler/:token', 'Webhook', 'pullmail', 'PullMailTasks');
        $this->applicationAccessMap->add('Webhook', 'pullmail', Role::APP_PUBLIC);
    }

    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
    }

    public function getPluginName()
    {
        return 'PullMailTasks';
    }

    public function getPluginDescription()
    {
        return t('Pull Email-Tasks from IMAP Account');
    }

    public function getPluginAuthor()
    {
        return 'Ralf Blumenthal/stratmaster';
    }

    public function getPluginVersion()
    {
        return '0.0.3';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/stratmaster/kanboard-plugin-pullmailtasks';
    }

    public function getCompatibleVersion()
    {
        return '>=1.0.40';
    }
}
