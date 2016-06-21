<?
if (!check_bitrix_sessid()) {
	return;
}
IncludeModuleLangFile(__FILE__);
echo CAdminMessage::ShowNote(GetMessage('UNINSTALL_COMPLETE'));