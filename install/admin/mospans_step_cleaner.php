<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

CModule::IncludeModule('mospans.uploadiblockcleaner');

if (!array_key_exists('action', $_POST)) {
	$action = '';
} else {
	$action = trim($_POST['action']);
}

if (!array_key_exists('step', $_POST)) {
	$step = -1;
} else {
	$step = (int) $_POST['step'];
}

$result = array(
	'error' => false,
	'action_complete' => false
);

$stepCleaner = new CUploadIblockCleaner();
if (!array_key_exists('mospans.uploadiblockcleaner', $_SESSION)) {
	$_SESSION['mospans.uploadiblockcleaner'] = array();
}

switch ($action) {
	case 'analysis':
		if ($step <= 0) {
			$result['error'] = true;
			break;
		}
		if (!array_key_exists('using_file_ids', $_SESSION['mospans.uploadiblockcleaner']) || $step == 1) {
			$_SESSION['mospans.uploadiblockcleaner']['using_file_ids'] = array();
		}
		// получаем очередную порцию id файлов из инфоблоков:
		$fileIds = $stepCleaner->getFilesIdInIblocksByStep($step);
		if (is_array($fileIds)) {
			// дописываем id файлов в сессию
			$_SESSION['mospans.uploadiblockcleaner']['using_file_ids'] = array_merge($_SESSION['mospans.uploadiblockcleaner']['using_file_ids'], $fileIds);
		}
		
		$result['percentage'] = round(100 * $step / $stepCleaner->getAnalysisSteps());
		if ($step == $stepCleaner->getAnalysisSteps()) {
			$result['action_complete'] = true;
		}
		break;
	case 'file_processing':
		if ($step == 0) {
			$_SESSION['mospans.uploadiblockcleaner']['count_files'] = $stepCleaner->getFilesCount();
			$_SESSION['mospans.uploadiblockcleaner']['all_steps'] = ceil($_SESSION['mospans.uploadiblockcleaner']['count_files'] / $stepCleaner->getFilesInStep());
		}
		if ($step >= 0) {
			$files = $stepCleaner->getFilesListByStep($step);
			foreach ($files as $file) {
				
			}
			$result['percentage'] = round(100 * $step / $_SESSION['mospans.uploadiblockcleaner']['all_steps']);
		}
		if ($step == (int) $_SESSION['mospans.uploadiblockcleaner']['all_steps']) {
			$result['action_complete'] = true;
			unset($_SESSION['mospans.uploadiblockcleaner']['count_files']);
			unset($_SESSION['mospans.uploadiblockcleaner']['all_steps']);
		}
		break;
}
echo json_encode($result);
?> 