<?
IncludeModuleLangFile(__FILE__);

CJSCore::Init(array('ajax'));

$APPLICATION->AddHeadString('<script type="text/javascript" src="/bitrix/js/mospans.uploadiblockcleaner/script.js"></script>');
$APPLICATION->AddHeadString('<link rel="stylesheet" href="/bitrix/panel/mospans.uploadiblockcleaner/style.css">');
?>
<h1><?=GetMessage('OPTIONS_TITLE')?></h1>

<a class="adm-btn adm-btn-save mospans-run-clean"><?=GetMessage('OPTIONS_RUN_CLEAN')?></a>

<div class="mospans-action-state mospans-action-state_analysis">
	<?=GetMessage('OPTIONS_ACTION_STATE_ANALYSIS')?>
</div>
<div class="mospans-action-state mospans-action-state_analysis-complete">
	<?=GetMessage('OPTIONS_ACTION_STATE_ANALYSIS_COMPLETE')?>
</div>
<div class="mospans-action-state mospans-action-state_file-processing">
	<?=GetMessage('OPTIONS_ACTION_STATE_FILE_PROCESSING')?>
</div>
<div class="mospans-action-state mospans-action-state_file-processing-complete">
	<?=GetMessage('OPTIONS_ACTION_STATE_FILE_PROCESSING_COMPLETE')?>
</div>

<div class="mospans-progressbar">
	<div class="mospans-progressbar__line"></div>
	<div class="mospans-progressbar__content"></div>
</div>