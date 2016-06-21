<?
IncludeModuleLangFile(__FILE__);

CJSCore::Init(array('ajax'));

$APPLICATION->AddHeadString('<script type="text/javascript" src="/bitrix/js/mospans.uploadiblockcleaner/script.js"></script>');
$APPLICATION->AddHeadString('<link rel="stylesheet" href="/bitrix/panel/mospans.uploadiblockcleaner/style.css">');
?>
<h1><?=GetMessage('OPTIONS_TITLE')?></h1>

<a class="adm-btn adm-btn-save mospans-run-clean"><?=GetMessage('OPTIONS_RUN_CLEAN')?></a>

<div class="mospans-progressbar">
	<div class="mospans-progressbar__line"></div>
	<div class="mospans-progressbar__content"></div>
</div>