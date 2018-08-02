<?php

namespace Kanboard\Plugin\PullMailTasks;

require_once __DIR__.'/vendor/autoload.php';

use Kanboard\Core\Base;
use Kanboard\Core\Tool;
use Kanboard\Core\Mail\ClientInterface;
use League\HTMLToMarkdown\HtmlConverter;

defined('PMT_DOMAIN') or define('PMT_DOMAIN', '');
defined('PMT_MSGBOX') or define('PMT_MSGBOX', '');
defined('PMT_USER') or define('PMT_USER', '');
defined('PMT_PASWORD') or define('PMT_PASWORD', '');

/**
 * PullMailTasks Plugin
 *
 * @package  PullMailTasks
 * @author   Ralf Blumenthal/stratmaster
 * @author   Frédéric Guillot
 */
class EmailHandler extends Base
{

	/**
 	* Get getColor
 	*
 	* @access public
 	* @return string
 	*/
	public function getColor()
	{
		if (defined('pullmailtasks_color')) {
				$key = pullmailtasks_color;
		} else {
				$key = $this->configModel->get('pullmailtasks_color');
		}
		return trim($key);
	}

	/**
 	* Get getTag
 	*
 	* @access public
 	* @return string
 	*/
	public function getTag()
	{
		if (defined('pullmailtasks_tag')) {
				$key = pullmailtasks_colour;
		} else {
				$key = $this->configModel->get('pullmailtasks_tag');
		}
		return trim($key);
	}

	/**
 	* Get getAttachments
 	*
 	* @access public
 	* @return string
 	*/
	public function getAttachments()
	{
		if (defined('receive_attachments')) {
				$key = receive_attachments;
		} else {
				$key = $this->configModel->get('receive_attachments');
		}
		return trim($key);
	}

	/**
 	* Get getKeyWord
 	*
 	* @access public
 	* @return string
 	*/
	public function getKeyWord()
	{
		if (defined('pullmailtasks_keyword')) {
				$key = pullmailtasks_keyword;
		} else {
				$key = $this->configModel->get('pullmailtasks_keyword');
		}
		if ( ! empty($key) ) {
			return trim('SUBJECT "'.$key.'+"');
		} else {
			return 'ALL';
		}
	}

	/**
	 * Fetch Mail
	 *
	 * @access public
	 * @return array
	 */
	public function pullEmail()
	{
		$res = array(0,0);
		$domain = '{' .PMT_DOMAIN. '}';
		$msgbox = PMT_MSGBOX;
		$user = PMT_USER;
		$password = PMT_PASWORD;

		$mbox = imap_open("{$domain}{$msgbox}", $user, $password)
			 or die("can't connect: " . imap_last_error());

		$mails = imap_search($mbox,$this->getKeyWord());
		if ( ! empty($mails) ) {
			foreach( $mails as $num) {
				$from = imap_headerinfo( $mbox, $num );
				$header = iconv_mime_decode_headers(imap_fetchheader( $mbox, $num ), 0, "utf-8");
				#echo "<pre>";var_dump($header);echo "</pre>";
				$body = imap_qprint(imap_fetchbody($mbox, $num,1.1));
				if ($body == "") {
					$body = imap_qprint(imap_fetchbody($mbox, $num, 1));
					$body_html = imap_qprint(imap_fetchbody($mbox, $num, 1.2));
				}

				/* get mail structure for fetching attachments */
        $structure = imap_fetchstructure($mbox, $num);
        $attachments = array();
        /* if any attachments found... */
        if(isset($structure->parts) && count($structure->parts))
        {
            for($i = 0; $i < count($structure->parts); $i++)
            {
                $attachments[$i] = array(
                    'is_attachment' => false,
                    'filename' => '',
                    'name' => '',
                    'attachment' => ''
                );

                if($structure->parts[$i]->ifdparameters)
                {
                    foreach($structure->parts[$i]->dparameters as $object)
                    {
                        if(strtolower($object->attribute) == 'filename')
                        {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['filename'] = $object->value;
                        }
                    }
                }

                if($structure->parts[$i]->ifparameters)
                {
                    foreach($structure->parts[$i]->parameters as $object)
                    {
                        if(strtolower($object->attribute) == 'name')
                        {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['name'] = $object->value;
                        }
                    }
                }

                if($attachments[$i]['is_attachment'])
                {
                    $attachments[$i]['attachment'] = imap_fetchbody($mbox, $num, $i+1);

                    /* 4 = QUOTED-PRINTABLE encoding */
                    if($structure->parts[$i]->encoding == 3)
                    {
												$attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                    }
                    /* 3 = BASE64 encoding */
                    elseif($structure->parts[$i]->encoding == 4)
                    {
                        $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                    }
                }
            }
        }

                $subject = $header['Subject'];

                list($target, $subject) = explode(':', $header['Subject'], 2);
								if (strstr($target, '+') && ! strstr($this->getKeyWord(), 'ALL')) {
                	list(, $identifier) = explode('+', $target);
								} else {
									$identifier = $target;
								}
                if ( ! empty($identifier) && ! empty($subject) ) {
                    $task = array(
												'sender'=>$from->from[0]->mailbox.'@'.$from->from[0]->host,
                        'subject'=>$subject,
                        'recipient'=>$identifier,
                        'stripped-html'=>$body_html,
                        'stripped-text'=>$body,
												'attachments'=>$attachments,
                    );
                    $r = $this->receiveEmail($task);
                    $res[$r?0:1]++;
                    if ( $r )
                        imap_delete($mbox,$num);
                }
			}
		}
        if ( $res[0]>0 )
		  imap_expunge ($mbox);
		imap_close($mbox);
		return $res;
	}


    /**
     * Parse incoming email
     *
     * @access public
     * @param  array   $payload   Incoming email
     * @author   Frédéric Guillot
     * @return boolean
     */
    public function receiveEmail(array $payload)
    {
        if (empty($payload['sender']) || empty($payload['subject']) || empty($payload['recipient']) || strstr($identifier, '+')) {
            return false;
        }

        // The user and its email address must exist in Kanboard
        $user = $this->userModel->getByEmail($payload['sender']);
        if (empty($user)) {
            $this->logger->debug('PullMailTasks: ignored => user not found');
            return false;
        }

        // The project must have a short name (identifier)
				$project = $this->projectModel->getByIdentifier($payload['recipient']);

        if (empty($project)) {
            $this->logger->debug('PullMailTasks: ignored => project not found');
            return false;
        }

        // The user must be member of the project
				if (! $this->projectPermissionModel->isAssignable($project['id'], $user['id'])) {
            $this->logger->debug('PullMailTasks: ignored => user is not member of the project');
            return false;
        }

        // Get the Markdown contents, otherwise plaintext
        if (! empty($payload['stripped-html'])) {
						$htmlConverter = new HtmlConverter(array(
							'strip_tags'   => true,
							'remove_nodes' => 'meta script style link img span',
						));
            $description = $htmlConverter->convert($payload['stripped-html']);
        }
        else if (! empty($payload['stripped-text'])) {
            $description = $payload['stripped-text'];
        }
        else {
            $description = '';
        }

				// Finally, we create the task
        $taskId = $this->taskCreationModel->create(array(
            'project_id' => $project['id'],
            'title' => $payload['subject'],
            'description' => trim($description),
            'creator_id' => $user['id'],
						'swimlane_id' => $this->getSwimlaneId($project),
						'color_id' => $this->getColor(),
						'tags' => array($this->getTag()),
        ));

				if ($taskId > 0) {
				if 	($this->getAttachments() == 1) {
					#$this->addEmailBodyAsAttachment($taskId, $payload);
					$this->uploadAttachments($taskId, $payload);
				}
					return true;
				}

				return false;

			}

			/**
			* Get swimlane id
			*
			* @access public
			* @param  array $project
			* @return string
			*/
			public function getSwimlaneId(array $project)
			{
				$swimlane = $this->swimlaneModel->getFirstActiveSwimlane($project['id']);
				return empty($swimlane) ? 0 : $swimlane['id'];
			}

			protected function addEmailBodyAsAttachment($taskId, array $payload)
			{
				$filename = t('Email') . '.txt';
				$data = '';
				if (! empty($payload['stripped-html'])) {
					$data = $payload['stripped-html'];
					$filename = t('Email') . '.html';
				} elseif (! empty($payload['stripped-text'])) {
					$data = $payload['stripped-text'];
				}
				if (! empty($data)) {
					$this->taskFileModel->uploadContent($taskId, $filename, $data, false);
				}
			}

			protected function uploadAttachments($taskId, array $payload)
    	{
        if (! empty($payload['attachments'])) {
            foreach ($payload['attachments'] as $attachment) {

							if($attachment['is_attachment'] == 1)
							{
								$filename = $attachment['name'];
								if(empty($filename)) $filename = $attachment['filename'];
								if(empty($filename)) $filename = time() . ".dat";

              	$this->taskFileModel->uploadContent($taskId, $filename, $attachment['attachment']);

							}
          	}
        }
    }
}
