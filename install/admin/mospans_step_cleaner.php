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

if ($step <= 0) {
	$result['error'] = true;
	$action = 'error';
	break;
}

switch ($action) {
	case 'iblock_analysis':
		if ($step == 1) {
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
		
	case 'file_analysis':
		if ($step == 1) {
			$_SESSION['mospans.uploadiblockcleaner']['count_files'] = $stepCleaner->getFilesCount();
			$_SESSION['mospans.uploadiblockcleaner']['count_file_analysis_steps'] = ceil($_SESSION['mospans.uploadiblockcleaner']['count_files'] / $stepCleaner->getFilesInStep());
			if ($_SESSION['mospans.uploadiblockcleaner']['count_file_analysis_steps'] == 0) {
				$_SESSION['mospans.uploadiblockcleaner']['count_file_analysis_steps'] = 1;
			}
			$_SESSION['mospans.uploadiblockcleaner']['not_using_file_ids'] = array();
		}
		
		$files = $stepCleaner->getFilesListByStep($step);
		foreach ($files as $file) {
			if (!in_array($file['ID'], $_SESSION['mospans.uploadiblockcleaner']['using_file_ids'])) {
				$_SESSION['mospans.uploadiblockcleaner']['not_using_file_ids'][] = $file['ID'];
			}
		}
		
		$result['percentage'] = round(100 * $step / $_SESSION['mospans.uploadiblockcleaner']['count_file_analysis_steps']);
		if ($step == (int) $_SESSION['mospans.uploadiblockcleaner']['count_file_analysis_steps']) {
			$result['action_complete'] = true;
			unset($_SESSION['mospans.uploadiblockcleaner']['count_files']);
			unset($_SESSION['mospans.uploadiblockcleaner']['count_file_analysis_steps']);
		}
		break;
	
	case 'file_deleting':
		$filesInStep = 100;
		
		if ($step == 1) {
			$_SESSION['mospans.uploadiblockcleaner']['count_not_using_files'] = count($_SESSION['mospans.uploadiblockcleaner']['not_using_file_ids']);
			$_SESSION['mospans.uploadiblockcleaner']['count_file_deleting_steps'] = ceil($_SESSION['mospans.uploadiblockcleaner']['count_not_using_files'] / $filesInStep);
			if ($_SESSION['mospans.uploadiblockcleaner']['count_file_deleting_steps'] == 0) {
				$_SESSION['mospans.uploadiblockcleaner']['count_file_deleting_steps'] = 1;
			}
		}
		
		$stepFileIds = array_slice($_SESSION['mospans.uploadiblockcleaner']['not_using_file_ids'], ($step - 1) * $filesInStep, $filesInStep);
		foreach ($stepFileIds as $fileId) {
			CFile::Delete($fileId);
		}
		$result['percentage'] = round(100 * $step / $_SESSION['mospans.uploadiblockcleaner']['count_file_deleting_steps']);
		if ($step == (int) $_SESSION['mospans.uploadiblockcleaner']['count_file_deleting_steps']) {
			$result['action_complete'] = true;
			unset($_SESSION['mospans.uploadiblockcleaner']['count_not_using_files']);
			unset($_SESSION['mospans.uploadiblockcleaner']['count_file_deleting_steps']);
			unset($_SESSION['mospans.uploadiblockcleaner']['not_using_file_ids']);
		}
		break;
	
	case 'folder_updating':
		break;
}
echo json_encode($result);
?> 