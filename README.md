PullMailTasks Plugin for Kanboard
============================
*(Based on Mailgun Plugin)*

Use your IMAP Mail account to create tasks directly by email

- Create tasks from incoming emails sent to a mailbox
- Optionally set color and/or tag(s) for all pulled tasks
- Experimental support for attachments (any help welcome)


Incoming Mails for Kanboard are identified using the mails subject `kanboard+MYPROJECT:TASKNAME`. Mails are deleted after task creation.


Developed using [Kanboard](https://kanboard.org) Version 1.2.5

Authors
------

- Ralf Blumenthal, with enhancements by stratmaster
- License MIT

Installation
------------

- Create a folder **plugins/PullMailTasks**
- Copy all files under this directory
- php-imap extension required
- Setup a cron job calling the URL to pull mail task (URL is found under **Settings > Integrations > PullMailTasks**)

Plugin Setup
-------------------------------------------

Define those constants in your `config.php` file :

```php
### PullMailTasks Plugin Settings ###
// System name, port and flags
define('PMT_DOMAIN', 'imap.goneo.de:993/imap/ssl');
// Mailbox name
define('PMT_MSGBOX', 'INBOX');
// Username and password
define('PMT_USER', '');
define('PMT_PASWORD', '');
```

### Kanboard configuration

1. Be sure that your users have an email address in their profiles
2. Assign a project identifier to the desired projects: **Project settings > Edit Project > Identifier**. The format has to be alphanumeric like `MYPROJECT`
3. Send an email to your project using the mail subject `kanboard+MYPROJECT:TASKNAME`.
> **Attention**: The from address must be same as the email address in the user profile in Kanboard and the user must be member of the project.
4. Pull mail by calling URL (URL is found under **Settings > Integrations > PullMailTasks**)
5. Optionally set color, tag(s) and support for receiving attachments for all pulled tasks under **Settings > Integrations > PullMailTasks**
