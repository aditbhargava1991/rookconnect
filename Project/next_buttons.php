<span class="pull-right hide-on-mobile"><a href="?<?= $previous_tab == '' ? '' : 'edit='.$projectid.'&tab='.$previous_tab ?>" class="btn brand-btn" onclick="return waitForSave(this);"><?= $previous_tab == '' ? 'Back to Dashboard' : 'Previous' ?></a>
<a href="?<?= $next_set ? 'edit='.$projectid.'&tab='.$next_tab : '' ?>" class="btn brand-btn" onclick="return waitForSave(this);"><?= $next_set ? 'Next' : 'Finish' ?></a></span>