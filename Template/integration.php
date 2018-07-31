<h3><img src="<?= $this->url->dir() ?>plugins/Pullmailtasks/pullmailtasks-icon.png"/>&nbsp;PullMailTasks</h3>
    <p class="form-help"><a href="https://github.com/stratmaster/kanboard-plugin-pullmailtasks/blob/master/README.md" target="_blank"><?= t('Help on PullMailTasks Plugin') ?></a></p>
<div class="panel">
    <input type="text" class="auto-select" readonly="readonly" value="<?= $this->url->href('Webhook', 'pullmail', array('plugin' => 'pullmailtasks', 'token' => $values['webhook_token']), false, '', true) ?>">

    <?= $this->form->label(t('Default task color'), 'pullmailtasks_color') ?>
    <?= $this->form->select('pullmailtasks_color', array('yellow' => t('Yellow'), 'blue' => t('Blue'), 'green' => t('Green'), 'purple' => t('Purple'), 'red' => t('Red'), 'orange' => t('Orange'), 'grey' => t('Grey'), 'brown' => t('Brown'), 'deep_orange' => t('Deep Orange'), 'dark_grey' => t('Dark Grey'), 'pink' => t('Pink'), 'teal' => t('Teal'), 'cyan' => t('Cyan'), 'lime' => t('Lime'), 'light_green' => t('Light Green'), 'amber' => t('Amber')), $values) ?>

    <?= $this->form->label(t('Tags'), 'pullmailtasks_tag') ?>
    <?= $this->form->text('pullmailtasks_tag', $values, array()) ?>
    <p class="form-help">(<?= t('Comma-separated') ?>)</p>

    <div class="form-actions">
        <input type="submit" value="<?= t('Save') ?>" class="btn btn-blue">
    </div>
</div>
