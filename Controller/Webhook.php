<?php

namespace Kanboard\Plugin\PullMailTasks\Controller;

use Kanboard\Controller\BaseController;
use Kanboard\Plugin\PullMailTasks\EmailHandler;

/**
 * Webhook Controller
 *
 * @package  PullMailTasks
 * @author   Ralf Blumenthal/stratmaster
 */
class Webhook extends BaseController
{
    /**
     * Handle webhooks
     *
     * @access public
     */
    public function pullmail()
    {
        $this->checkWebhookToken();

        $handler = new EmailHandler($this->container);
        $res =  $handler->pullEmail();
		    echo  "{$res[0]} ".t('Parsed')." {$res[1]} ".t('Ignored');
    }
}
