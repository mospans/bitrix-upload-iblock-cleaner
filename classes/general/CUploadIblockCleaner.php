<?php
class CUploadIblockCleaner {
	private $MODULE_ID = 'iblock'; // id модуля из таблицы b_file, файлы которого будут чиститься
	private $filesInStep = 100; // число файлов, обрабатываемых за один шаг
	
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
	
	public function makeStep()
	{
		
	}
}