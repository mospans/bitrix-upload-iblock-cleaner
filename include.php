<?php
CModule::IncludeModule("mospans.upload.iblock.cleaner");

$arClasses = array(
    'CUploadIblockCleaner' => 'classes/general/СUploadIblockCleaner.php'
);

CModule::AddAutoloadClasses("mospans.upload.iblock.cleaner", $arClasses);