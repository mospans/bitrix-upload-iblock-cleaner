<?
IncludeModuleLangFile(__FILE__);

Class mospans_uploadiblockcleaner extends CModule
{
	var $MODULE_ID = 'mospans.uploadiblockcleaner';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	
	public function __construct()
	{
		global $DOCUMENT_ROOT;
		include $DOCUMENT_ROOT . '/bitrix/modules/' . $this->MODULE_ID . '/install/version.php';
		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->MODULE_NAME = GetMessage('NAME');
		$this->MODULE_DESCRIPTION = GetMessage('DESCRIPTION');
		$this->PARTNER_NAME = GetMessage('PARTNER_NAME');
	}
	
	public function DoInstall()
	{
		global $DOCUMENT_ROOT;
		CopyDirFiles($DOCUMENT_ROOT . '/bitrix/modules/' . $this->MODULE_ID . '/install/admin/', $DOCUMENT_ROOT . '/bitrix/admin/', true, true);
		CopyDirFiles($DOCUMENT_ROOT . '/bitrix/modules/' . $this->MODULE_ID . '/install/js/', $DOCUMENT_ROOT . '/bitrix/js/', true, true);
		CopyDirFiles($DOCUMENT_ROOT . '/bitrix/modules/' . $this->MODULE_ID . '/install/panel/', $DOCUMENT_ROOT . '/bitrix/panel/', true, true);
		RegisterModule($this->MODULE_ID);
	}

	public function DoUninstall()
	{
		global $DOCUMENT_ROOT;
		DeleteDirFiles($DOCUMENT_ROOT . '/bitrix/modules/' . $this->MODULE_ID . '/install/admin/', $DOCUMENT_ROOT . '/bitrix/admin/');
		DeleteDirFilesEx('/bitrix/js/' . $this->MODULE_ID . '/');
		DeleteDirFilesEx('/bitrix/panel/' . $this->MODULE_ID . '/');
		UnRegisterModule($this->MODULE_ID);
	}
}