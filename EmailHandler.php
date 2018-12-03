<?php

namespace Kanboard\Plugin\PullMailTasks;

use Kanboard\Core\Base;
use Kanboard\Core\Tool;
use Kanboard\Core\Mail\ClientInterface;

defined('PMT_DOMAIN') or define('PMT_DOMAIN', '');
defined('PMT_MSGBOX') or define('PMT_MSGBOX', '');
defined('PMT_USER') or define('PMT_USER', '');
defined('PMT_PASWORD') or define('PMT_PASWORD', '');

/**
 * PullMailTasks Plugin
 *
 * @package  PullMailTasks
 * @author   Ralf Blumenthal/Benedikt Hopmann
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
	* Get getMailBodyAttachment
	*
	* @access public
	* @return string
	*/
	public function getMailBodyAttachment()
	{
		if (defined('receive_email_body')) {
				$key = receive_email_body;
		} else {
				$key = $this->configModel->get('receive_email_body');
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
	* Get getAddressTag
	*
	* @access public
	* @return string
	*/
	public function getAddressTag()
	{
		if (defined('pullmailtasks_addresstag')) {
				$key = pullmailtasks_addresstag;
		} else {
				$key = $this->configModel->get('pullmailtasks_addresstag');
		}
		return trim($key);
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

				$structure = imap_fetchstructure($mbox, $num);

				$obj_section = $structure;
				$section = "1";
				for ($i = 0 ; $i < 10 ; $i++) {
				    if ($obj_section->type == 0) {
				        break;
				    } else {
				        $obj_section = $obj_section->parts[0];
				        $section.= ($i > 0 ? ".1" : "");
				    }
				}

				$body = imap_fetchbody($mbox, $num, $section);
				if ($obj_section->encoding == 0) {
				    $body = mb_convert_encoding($body, "UTF-8", "auto");
				} else if ($obj_section->encoding == 1) {
				    $body = imap_8bit($body);
				} else if ($obj_section->encoding == 2) {
				    $body = imap_base64(imap_binary($body));
				} else if ($obj_section->encoding == 3) {
				    $body = imap_base64($body);
				} else if ($obj_section->encoding == 4) {
				    $body = imap_qprint($body);
				}
				foreach ($obj_section->parameters as $obj_param) {
				    if (($obj_param->attribute == "charset") && (mb_strtoupper($obj_param->value) != "UTF-8")) {
				        $body = utf8_encode($body);
				        break;
				    }
				}
				$body = quoted_printable_decode(strip_tags($body));

			 	echo "<pre>";var_dump($body);echo "</pre>";
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
								} elseif ((strpos($from->to[0]->mailbox,'-') !== false) && ($this->getAddressTag() != '') ) {
									list(, $identifier) = explode($this->getAddressTag(), $from->to[0]->mailbox);
									$subject = $header['Subject'];
								} elseif ($this->getAddressTag() != '') {
									$identifier = $target;
								}

                if ( ! empty($identifier) && ! empty($subject) ) {
                    $task = array(
												'sender'=>$from->from[0]->mailbox.'@'.$from->from[0]->host,
                        'subject'=>$subject,
                        'recipient'=>$identifier,
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
    {global $identifier;
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

        // Get the plaintext contents
        if (! empty($payload['stripped-text'])) {
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
					if 	($this->getMailBodyAttachment() == 1) {
						$this->addEmailBodyAsAttachment($taskId, $payload);
					}
					if 	($this->getAttachments() == 1) {
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
				if (! empty($payload['stripped-text'])) {
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
