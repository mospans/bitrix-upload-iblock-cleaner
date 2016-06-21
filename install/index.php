<?
IncludeModuleLangFile(__FILE__);

Class mospans_uploadiblockcleaner extends CModule
{
	var $MODULE_ID = "mospans.uploadiblockcleaner";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	
	public function __construct()
	{
		global $DOCUMENT_ROOT;
		include $DOCUMENT_ROOT . "/bitrix/modules/" . $this->MODULE_ID . "/install/version.php";
		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->MODULE_NAME = GetMessage('NAME');
		$this->MODULE_DESCRIPTION = GetMessage('DESCRIPTION');
	}
	
	public function DoInstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		RegisterModule($this->MODULE_ID);
		$APPLICATION->IncludeAdminFile(GetMessage("INSTALL_TITLE"), $DOCUMENT_ROOT . "/bitrix/modules/" . $this->MODULE_ID . "/install/step.php");
	}

	public function DoUninstall()
	{
		global $DOCUMENT_ROOT, $APPLICATION;
		UnRegisterModule($this->MODULE_ID);
		$APPLICATION->IncludeAdminFile(GetMessage("UNINSTALL_TITLE"), $DOCUMENT_ROOT . "/bitrix/modules/" . $this->MODULE_ID . "/install/unstep.php");
	}
}