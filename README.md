PullMailTasks Plugin for Kanboard
============================
*(Based on Mailgun Plugin)*

Use your IMAP Email account to create tasks directly by email

- Create tasks from incoming emails sent to a mailbox
- Optionally set color and/or tag(s) for all pulled tasks
- Experimental support for attachments (any help welcome)


Incoming Emails for Kanboard are identified using the email subject `kanboard+MYPROJECT:TASKNAME`. Emails are deleted after task creation. Other emails will be ignored.


Developed using [Kanboard](https://kanboard.org) Version 1.2.5

Authors
------

- Ralf Blumenthal, with enhancements by stratmaster
- License MIT
- Entypo Email pictogram by Daniel Bruce — www.entypo.com, released under CC BY-SA 4.0

Installation
------------

- Create a folder **plugins/PullMailTasks**
- Copy all files under this directory
- php-imap extension required
- Setup a cron job calling the URL to pull email task (URL is found under **Settings > Integrations > PullMailTasks**)

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
3. Send an email to your project using the email subject `kanboard+MYPROJECT:TASKNAME`.
> **Attention**: The from address must be same as the email address in the user profile in Kanboard and the user must be member of the project.
4. Pull email by calling URL, which is found under **Settings > Integrations > PullMailTasks** along with
some other settings
5. *Optionally* set color, tag(s) and support for receiving attachments for all pulled tasks. Furthermore `kanboard+MYPROJECT:TASKNAME` as email subject with keyword `kanboard` is the default value for all pulled tasks. Alternatively, the keyword could be suspended completely. This may be useful, if the imap account is used only for pulling tasks. In this case, the necessary email subject could be changed to `MYPROJECT:TASKNAME`
