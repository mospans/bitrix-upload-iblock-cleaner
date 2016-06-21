<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

CModule::IncludeModule("mospans.uploadiblockcleaner");

if (!array_key_exists('step', $_POST)) {
	$step = -1;
} else {
	$step = (int) $_POST['step'];
}

$result = array('error' => false);

$stepCleaner = new CUploadIblockCleaner();

if ($step == 0) {
	$_SESSION['count_files'] = $stepCleaner->getFilesCount();
	$_SESSION['all_steps'] = ceil($_SESSION['count_files'] / $stepCleaner->getFilesInStep());
}
if ($step >= 0) {
	
	$result['percentage'] = round(100 * $step / $_SESSION['all_steps']);
}
echo json_encode($result);
?> 