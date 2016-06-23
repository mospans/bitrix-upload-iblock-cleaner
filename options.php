<?
IncludeModuleLangFile(__FILE__);

CJSCore::Init(array('ajax'));

$APPLICATION->AddHeadString('<script type="text/javascript" src="/bitrix/js/mospans.uploadiblockcleaner/script.js"></script>');
$APPLICATION->AddHeadString('<link rel="stylesheet" href="/bitrix/panel/mospans.uploadiblockcleaner/style.css">');
?>
<h1><?=GetMessage('OPTIONS_TITLE')?></h1>

<a class="adm-btn adm-btn-save mospans-run-clean"><?=GetMessage('OPTIONS_RUN_CLEAN')?></a>

<div class="mospans-action-state mospans-action-state_iblock-analysis">
	<?=GetMessage('OPTIONS_ACTION_STATE_IBLOCK_ANALYSIS')?>
</div>
<div class="mospans-action-state mospans-action-state_iblock-analysis-complete">
	<?=GetMessage('OPTIONS_ACTION_STATE_IBLOCK_ANALYSIS_COMPLETE')?>
</div>
<div class="mospans-action-state mospans-action-state_file-analysis">
	<?=GetMessage('OPTIONS_ACTION_STATE_FILE_ANALYSIS')?>
</div>
<div class="mospans-action-state mospans-action-state_file-analysis-complete">
	<?=GetMessage('OPTIONS_ACTION_STATE_FILE_ANALYSIS_COMPLETE')?>
</div>
<div class="mospans-action-state mospans-action-state_file-deleting">
	<?=GetMessage('OPTIONS_ACTION_STATE_FILE_DELETING')?>
</div>
<div class="mospans-action-state mospans-action-state_file-deleting-complete">
	<?=GetMessage('OPTIONS_ACTION_STATE_FILE_DELETING_COMPLETE')?>
</div>
<div class="mospans-action-state mospans-action-state_folder-update">
	<?=GetMessage('OPTIONS_ACTION_STATE_FOLDER_UPDATE')?>
</div>
<div class="mospans-action-state mospans-action-state_folder-update-complete">
	<?=GetMessage('OPTIONS_ACTION_STATE_FOLDER_UPDATE_COMPLETE')?>
</div>

<div class="mospans-progressbar">
	<div class="mospans-progressbar__line"></div>
	<div class="mospans-progressbar__content"></div>
</div>