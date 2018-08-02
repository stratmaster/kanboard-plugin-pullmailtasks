<h3><img src="<?= $this->url->dir() ?>plugins/PullMailTasks/Asset/pullmailtasks-icon.png"/>&nbsp;PullMailTasks</h3>
<!-- Entypo Email pictogram by Daniel Bruce — www.entypo.com -->
<div class="panel">

    <p class="form-help"><a href="https://github.com/stratmaster/kanboard-plugin-pullmailtasks/blob/master/README.md" target="_blank"><?= t('Help on PullMailTasks Configuration and Use') ?></a></p>

    <?= $this->form->label(t('PullMailTasks URL'), 'webhook_token') ?>
    <input type="text" class="auto-select" readonly="readonly" value="<?= $this->url->href('WebhookController', 'pullmail', array('plugin' => 'PullMailTasks', 'token' => $values['webhook_token']), false, '', true) ?>">

    <?= $this->form->label(t('Email Subject'), 'pullmailtasks_keyword') ?>
    <?= $this->form->select('pullmailtasks_keyword', array('kanboard' => 'kanboard+MYPROJECT:TASKNAME','' => 'MYPROJECT:TASKNAME'), $values) ?>
    <p class="form-help"><b>kanboard+MYPROJECT:TASKNAME</b> <?= t('as email subject with keyword') ?> <b>kanboard</b> <?= t('is the default value. Alternatively, the keyword could be suspended completely. This may be useful, if the imap account is used only for pulling Email-Tasks.') ?></p>

    <?= $this->form->label(t('Default task color'), 'pullmailtasks_color') ?>
    <?= $this->form->select('pullmailtasks_color', array('yellow' => t('Yellow'), 'blue' => t('Blue'), 'green' => t('Green'), 'purple' => t('Purple'), 'red' => t('Red'), 'orange' => t('Orange'), 'grey' => t('Grey'), 'brown' => t('Brown'), 'deep_orange' => t('Deep Orange'), 'dark_grey' => t('Dark Grey'), 'pink' => t('Pink'), 'teal' => t('Teal'), 'cyan' => t('Cyan'), 'lime' => t('Lime'), 'light_green' => t('Light Green'), 'amber' => t('Amber')), $values) ?>

    <?= $this->form->label(t('Tags'), 'pullmailtasks_tag') ?>
    <?= $this->form->text('pullmailtasks_tag', $values, array()) ?>
    <p class="form-help"><?= t('Several Tags will be comma-separated') ?></p>

    <?= $this->form->hidden('receive_attachments', array('receive_attachments' => 0)) ?>
    <?= $this->form->checkbox('receive_attachments', t('Receive attachments along with Email-Tasks'), 1, isset($values['receive_attachments']) && $values['receive_attachments'] == 1) ?>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue">
    </div>
</div>
