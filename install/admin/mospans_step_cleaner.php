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

$cleaner = new CUploadIblockCleaner();
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
	// анализ инфоблоков на наличие файлов. Id файлов сохраняются в массив $_SESSION['mospans.uploadiblockcleaner']['using_file_ids']
		if ($step == 1) {
			$_SESSION['mospans.uploadiblockcleaner']['iblocks_info'] = $cleaner->getIblockInfo();
			$_SESSION['mospans.uploadiblockcleaner']['using_file_ids'] = array();
			$_SESSION['mospans.uploadiblockcleaner']['count_iblock_analysis_steps'] = $cleaner->getStepsInIblockAnalysis();
		}
		
		// вычисляем инфоблок и номер страницы для выборки на основании шага
		$numPage = $step;
		$iblockId = -1;
		foreach ($_SESSION['mospans.uploadiblockcleaner']['iblocks_info'] as $iblockInfo) {
			if ($numPage <= $iblockInfo['STEPS']) {
				$iblockId = (int) $iblockInfo['ID'];
				break;
			} else {
				$numPage -= $iblockInfo['STEPS'];
			}
		}
		
		// если был найден номер инфоблока, то делаем выборку id файлов
		if ($iblockId > 0) {			
			// получаем очередную порцию id файлов из инфоблоков:
			$fileIds = $cleaner->getFilesIdInIblocks($iblockId, $numPage, $_SESSION['mospans.uploadiblockcleaner']['iblocks_info'][$iblockId]['PROPERTY_NAMES'], $_SESSION['mospans.uploadiblockcleaner']['iblocks_info'][$iblockId]['PROPERTY_NAMES_IN_RESULT']);
			if (is_array($fileIds)) { 
				// дописываем id файлов в сессию
				$_SESSION['mospans.uploadiblockcleaner']['using_file_ids'] = array_merge($_SESSION['mospans.uploadiblockcleaner']['using_file_ids'], $fileIds);
			}
		}
		
		$result['percentage'] = round(100 * $step / $_SESSION['mospans.uploadiblockcleaner']['count_iblock_analysis_steps']);
		if ($step == $_SESSION['mospans.uploadiblockcleaner']['count_iblock_analysis_steps']) {
			$result['action_complete'] = true;
			unset($_SESSION['mospans.uploadiblockcleaner']['iblocks_info']);
			unset($_SESSION['mospans.uploadiblockcleaner']['count_iblock_analysis_steps']);
		}
		break;
		
	case 'file_analysis':
	// анализ таблицы зарегистрированных файлов b_file.
	// Для каждой записи выполняется проверка наличия в массиве id файлов, используемых в инфоблоках $_SESSION['mospans.uploadiblockcleaner']['using_file_ids']:
	// если файл используется, то мы физически копируем его во временную директорию
	// иначе добавляем его id в массив зарегистрированных, но не используемых файлов $_SESSION['mospans.uploadiblockcleaner']['not_using_file_ids']
		if ($step == 1) {
			$_SESSION['mospans.uploadiblockcleaner']['count_files'] = $cleaner->getFilesCount();
			$_SESSION['mospans.uploadiblockcleaner']['count_file_analysis_steps'] = ceil($_SESSION['mospans.uploadiblockcleaner']['count_files'] / $cleaner->getFilesInStep());
			if ($_SESSION['mospans.uploadiblockcleaner']['count_file_analysis_steps'] == 0) {
				$_SESSION['mospans.uploadiblockcleaner']['count_file_analysis_steps'] = 1;
			}
			$_SESSION['mospans.uploadiblockcleaner']['not_using_file_ids'] = array();
			$cleaner->createTmpDir();
		}
		
		$files = $cleaner->getFilesListByStep($step);
		foreach ($files as $file) {
			if (!in_array($file['ID'], $_SESSION['mospans.uploadiblockcleaner']['using_file_ids'])) {
				$_SESSION['mospans.uploadiblockcleaner']['not_using_file_ids'][] = $file['ID'];
			} else {
				$cleaner->moveFileToTmpDir('/' . $file['SUBDIR'] . '/' . $file['FILE_NAME']);
			}
		}
		
		$result['percentage'] = round(100 * $step / $_SESSION['mospans.uploadiblockcleaner']['count_file_analysis_steps']);
		if ($step == (int) $_SESSION['mospans.uploadiblockcleaner']['count_file_analysis_steps']) {
			$result['action_complete'] = true;
			unset($_SESSION['mospans.uploadiblockcleaner']['using_file_ids']);
			unset($_SESSION['mospans.uploadiblockcleaner']['count_files']);
			unset($_SESSION['mospans.uploadiblockcleaner']['count_file_analysis_steps']);
		}
		break;
	
	case 'file_deleting':
	// удаление зарегистрированных, но не используемых файлов из массива $_SESSION['mospans.uploadiblockcleaner']['not_using_file_ids']
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
	
	case 'folder_update':
	// удаляем старую директорию /iblock/, а временную директорию переименовываем в /iblock/
		$cleaner->updateFolders();
		unset($_SESSION['mospans.uploadiblockcleaner']);
		$result['percentage'] = 100;
		$result['action_complete'] = true;
		break;
}
echo json_encode($result);
?> 