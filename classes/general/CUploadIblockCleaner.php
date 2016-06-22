<?php
class CUploadIblockCleaner {
	private $MODULE_ID = 'iblock'; // id модуля из таблицы b_file, файлы которого будут чиститься
	private $filesInStep = 100; // число файлов, обрабатываемых за один шаг
	
	private $elementsInStep = 2000; // число файлов, обрабатываемых за один шаг
	private $analysisSteps = 1; // число шагов при получении id файлов из инфоблоков
	private $fileIds = array(); // id файлов, используемых в инфоблоках
	
	public function __construct()
	{
		if (!CModule::IncludeModule('iblock')) {
			throw new Exception('Module iblock not installed');
		}
	}
	
	/**
	 * Возвращает массив названий свойств инфоблоков типа F (файлы) с приставкой 'PROPERTY_'
	 */
	private function getIblockPropertiesFiles()
	{
		$fileProperties = array();

		$propertiesResult = CIBlockProperty::GetList(Array(), Array('PROPERTY_TYPE' => 'F'));
		while ($property = $propertiesResult->GetNext()) {
			$fileProperties[] = 'PROPERTY_' . $property["CODE"];
		}
		
		return $fileProperties;
	}
	
	/**
	 * Добавляет id файла в $this->fileIds, проверяя отсутствие $id в массиве и является ли $id числом
	 */
	private function addFileId($id)
	{
		$id = (int) $id;
		if ($id > 0 && !in_array($id, $this->fileIds)) {
			$this->fileIds[] = $id;
		}
	}
	
	/**
	 * Добавляет массив id файлов в $this->fileIds
	 */
	private function addArrayToFileIds($array)
	{
		if (is_array($array)) {
			foreach ($array as $id) {
				$this->addFileId($id);
			}
		}
	}
	
	/**
	 * Возвращает $this->fileIds
	 */
	private function getFileIds()
	{
		return array_unique($this->fileIds);
	}
	
	public function getAnalysisSteps()
	{
		return $this->analysisSteps;
	}
	
	public function setAnalysisSteps($value)
	{
		$this->analysisSteps = (int) $value;
	}
	
	/**
	 * Возвращает массив с id файлов, соответствующий очередному шагу
	 * Возвращает false если номер шага некорректный
	 */
	public function getFilesIdInIblocksByStep($step)
	{
		if ($step < 1) {
			return false;
		}
		$filePropertiesForSelect = $this->getIblockPropertiesFiles(); // названия свойств для выборки
		// получение массива с названиями свойств в массиве результата:
		$filePropertiesInResult = array_map(function ($property) {
			return strtoupper($property) . '_VALUE';
		}, $filePropertiesForSelect);
		
		$arSelect = array_merge(array('ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE'), $filePropertiesForSelect);
		$elementsResult = CIBlockElement::GetList(array('ID' => 'ASC'), array(), false, array('nPageSize' => $this->elementsInStep, 'iNumPage' => $step), $arSelect);
		$this->setAnalysisSteps($elementsResult->NavPageCount);
		if ($step < $this->getAnalysisSteps()) {
			while ($element = $elementsResult->GetNext()) {
				$this->addFileId($element['PREVIEW_PICTURE']);
				$this->addFileId($element['DETAIL_PICTURE']);
				// добавление id файлов, прикрепленных к свойствам элемента, если они есть
				foreach ($filePropertiesInResult as $fileProperty) {
					if (is_array($element[$fileProperty])) {
						// для множественных свойств при хранении данных в отдельной таблице
						$this->addArrayToFileIds($element[$fileProperty]);
					} else {
						$this->addFileId($element[$fileProperty]);
					}
				}
			}
			return $this->getFileIds();
		} else {
			return false;
		}
	}
	
	/**
	 * Возвращает число файлов, обрабатываемых за один шаг
	 */
	public function getFilesInStep()
	{
		return $this->filesInStep;
	}
	
	/**
	 * Возвращает общее число файлов, происутствующих в таблице b_file
	 */
	public function getFilesCount()
	{
		global $DB;
		$arCount = $DB->Query("SELECT count(ID) FROM b_file WHERE MODULE_ID = '" . $this->MODULE_ID . "'")->Fetch();
		return (int) $arCount['count(ID)'];
	}
	
	/**
	 * Возвращает массив с путями до файлов, соответствующий очередному шагу
	 */
	public function getFilesListByStep($step)
	{
		if (is_null($step)) {
			throw new Exception('$step is not defined');
		}
		global $DB;
		$limit = $this->getFilesInStep();
		$offset = $step * $limit;
		$files = array();
		
		// используется обращение к базе напрямую, т.к. необходимо разбить выполнение скрипта на шаги, а CFile::GetList не позволяет делать выборку с использованием LIMIT и OFFSET
		$filesResult = $DB->Query("SELECT * FROM b_file JOIN (SELECT * FROM b_file LIMIT $limit OFFSET $offset) AS b_file_slice ON b_file_slice.ID = b_file.ID"); // Запрос оптимизирован по мотивам статьи https://habrahabr.ru/post/217521/
		while ($arFile = $filesResult->Fetch()) {
			$files[] = array(
				'ID' => $arFile['ID'],
				'SUBDIR' => str_replace('iblock/', '', $arFile['SUBDIR']),
				'FILE_NAME' => $arFile['FILE_NAME']
			);
		}
		return $files;
	}
}