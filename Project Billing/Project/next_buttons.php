<?php if(!IFRAME_PAGE) { ?>
	<span class="pull-right hide-on-mobile"><a href="?<?= $previous_tab == '' ? '' : 'edit='.($projectid > 0 ? $projectid : 0).'&tab='.$previous_tab ?>" class="btn brand-btn" onclick="return waitForSave(this);"><?= $previous_tab == '' ? 'Back to Dashboard' : 'Previous' ?></a>
	<a href="?<?= $next_set ? 'edit='.($projectid > 0 ? $projectid : 0).'&tab='.$next_tab : '' ?>" class="btn brand-btn" onclick="return waitForSave(this);"><?= $next_set ? 'Next' : 'Finish' ?></a></span>
<?php } ?>