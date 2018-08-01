<?php

namespace Kanboard\Plugin\Pullmailtasks;

use Kanboard\Core\Security\Role;
use Kanboard\Core\Translator;
use Kanboard\Core\Plugin\Base;

/**
 * Pull Mail Tasks Plugin
 *
 * @package  Pullmailtasks
 * @author   Ralf Blumenthal/stratmaster
 */
class Plugin extends Base
{
    public function initialize()
    {
        $this->template->hook->attach('template:config:integrations', 'pullmailtasks:integration');
        $this->route->addRoute('/pullmailtasks/handler/:token', 'Webhook', 'pullmail', 'pullmailtasks');
        $this->applicationAccessMap->add('Webhook', 'pullmail', Role::APP_PUBLIC);
    }

    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
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
