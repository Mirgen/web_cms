<?php

require __DIR__ . "/../../classes/DirectoryHelper.php";

/*****************************************************************************
 * Variables
 *****************************************************************************/
$argc = 0;
$argv = array();

/**
 * Usage from command line:
 */
if(isset($_SERVER['argv'])){
    $argc = count($_SERVER['argv']);
    $argv = $_SERVER['argv'];
} 
/**
 * Usage through GET parameters:
 */
else if($_GET){
    // simulate argv:
    $argv[0] = __FILE__;
    if(isset($_GET['new'])){
        $argv[1] = $_GET['new'];
    }
    if(isset($_GET['from_module'])){
        $argv[2] = $_GET['from_module'];
    }
    $argc = count($argv);
}

$helpText = "<pre>"
            . "\n\n"
            . "HELP\n\n"
            . "createNewModule.php script\n"
            . "==========================\n"
            . "Script which helps you to create new module. Creates all the necessary files for new module.\n" 
            . "These files you will edit to create module you want. In parameter you will set name of the new module.\n"
            . "\n"
            . "What you need: \n"
            . "--------------\n"
            . "\tInterpreter for PHP language. To be able to run PHP script.\n"
            . "\n"
            . "How to use it in command line: \n"
            . "------------------------------\n"
            . "\tphp createNewModule.php -h\n"
            . "\t\tto see this help\n"
            . "\tphp createNewModule.php newModuleName [module-from]\n"
            . "\t\tto create new module, where\n"
            . "\n"
            . "\t\tcreateNewModule.php ... is name of this script\n"
            . "\t\tnewModuleName ... is name of new module\n"
            . "\t\tmodule-from ... optional parameter, determines from which existing module the new module will be created, \n"
            . "default value for [module-from] is 'Empty'"
            . "\n\n"
            . "How to use it on webhosting: \n"
            . "------------------------------\n"
            . "We have to do 2 steps to use it, because main script dor this is in directory not accesible from 'outside'.\n\n"
            . "1. Create file 'createNewModule.php' in your www folder.\n"
            . "2. Put this line of code into it: \n";
            if(isset($_SERVER['argv'])){
                $helpText .= "\t<?php include __DIR__ . '/../app/utilities/CreateNewModule/createNewModule.php'; ?>\n";
            } else {
                $helpText .= "\t&lt;?php include __DIR__ . '/../app/utilities/CreateNewModule/createNewModule.php'; ?>\n";
            }
            $helpText .= 
              "3. You can now use it. Use these command:\n"
            . "\t- to print this help:\n"
            . "\t\twww.your-web-site.com/createNewModule.php\n"
            . "\t- to create new module:\n"
            . "\t\twww.your-web-site.com/createNewModule.php?new={NewModuleName}\n"
            . "\n"
            . "\t\t{NewModuleName} ... is name of new module you want to create\n"
            . "\t- to create new module and set from witch module it will be cloned:\n"
            . "\t\twww.your-web-site.com/createNewModule.php?new={NewModuleName}&from_module={FromModule}\n"
            . "\n"
            . "\t\t{NewModuleName} ... is name of new module you want to create\n"
            . "\t\t{FromModule} ... is name of module from witch the new one will be cloned\n"
            . "\n\n"
            . "</pre>";
$modulesFolder = __DIR__ . "/../../ModulesModule/";

$moduleToCreateFromName = "Empty";
$moduleToCreateFromPath = $modulesFolder . $moduleToCreateFromName . "/";

$pathToNewModule = "";
$newModuleName = "";

/*****************************************************************************
 * Test input attributes
 *****************************************************************************/
if($argc < 2 || $argc > 3){
    echo $helpText;
    exit (-1);
}

if($argv[1] == "-help" || $argv[1] == "--help" || $argv[1] == "--h" || $argv[1] == "-h"){
    echo $helpText;
    exit(1);
}

if(isset($argv[1])){
    $newModuleName = $argv[1];
    $pathToNewModule = $modulesFolder . $newModuleName . "/";
}
if(isset($argv[2])){
    $moduleToCreateFromName  = $argv[2];
    $moduleToCreateFromPath = $modulesFolder . $moduleToCreateFromName . "/";
}

if (file_exists($pathToNewModule) && is_dir($pathToNewModule)) {
    echo "ERROR: module with name '$newModuleName' already exists. Use different name for new module. Use '-h' for help.\n";
    exit (-1);
}

if (!file_exists($moduleToCreateFromPath) && !is_dir($moduleToCreateFromPath)) {
    echo "ERROR: module '$moduleToCreateFromName' doesnt exists. Please use another module as the image. Use '-h' for help.\n";
    exit (-1);
}

/*****************************************************************************
 * Code itself
 *****************************************************************************/

/**
 * Copy code of image module to new module.
 */
DirectoryHelper::recurseCopy($moduleToCreateFromPath, $pathToNewModule);

/**
 * Renaming files. Renaming classes. Renaming folder.
 */
$adminPresenters = $pathToNewModule . "AdminModule/presenters/";
$frontPresenters = $pathToNewModule . "FrontModule/presenters/";
$oldClassName = "Module" . $moduleToCreateFromName ."Presenter";
$newClassName = "Module" . $newModuleName ."Presenter";
$adminPresenter = $adminPresenters . $newClassName . ".php";
$frontPresenter = $frontPresenters . $newClassName . ".php";
$newModuleFile = $pathToNewModule . "model/Module" . $newModuleName . ".php";

if(file_exists($adminPresenters . $oldClassName . ".php")){
    rename($adminPresenters . $oldClassName . ".php", $adminPresenter);
} else {
    echo "ERROR: file '" . $adminPresenters . $oldClassName . ".php' does not exists. Check it.\n";
}
if(file_exists($frontPresenters . $oldClassName . ".php")){
    rename($frontPresenters . $oldClassName . ".php", $frontPresenter);
} else {
    echo "ERROR: file '" . $frontPresenters . $oldClassName . ".php' does not exists. Check it.\n";
}
// rename model class
if(file_exists($pathToNewModule . "model/Module" . $moduleToCreateFromName . ".php")){
    rename($pathToNewModule . "model/Module" . $moduleToCreateFromName . ".php", $newModuleFile);
} else {
    echo "ERROR: file '" . $pathToNewModule ."model/Module" . $moduleToCreateFromName . ".php' does not exists. Check it.\n";
}
// rename one template folder
if(file_exists($pathToNewModule . "FrontModule/templates/" . $moduleToCreateFromName)){
    rename($pathToNewModule . "FrontModule/templates/" . $moduleToCreateFromName, $pathToNewModule . "FrontModule/templates/" . $newModuleName);
} else {
    echo "WARNING: file '" . "FrontModule/templates/" . $moduleToCreateFromName . "' does not exists. Check it.\n";
}

// load current files contents
$adminPresenterContent = file_get_contents($adminPresenter);
$frontPresenterContent = file_get_contents($frontPresenter);
$modelContent = file_get_contents($newModuleFile);
$installSQLContent = file_get_contents($pathToNewModule . "install.sql");

// replace current class name with new class name
$adminPresenterContent = str_replace ($oldClassName, $newClassName, $adminPresenterContent);
$frontPresenterContent = str_replace ($oldClassName, $newClassName, $frontPresenterContent);
$modelContent = str_replace ($moduleToCreateFromName, $newModuleName, $modelContent);
$installSQLContent = str_replace ("Module" . $moduleToCreateFromName, "Module" . $newModuleName, $installSQLContent);

// put new content to presenters
file_put_contents($adminPresenter, $adminPresenterContent);
file_put_contents($frontPresenter, $frontPresenterContent);
file_put_contents($newModuleFile, $modelContent);
file_put_contents($pathToNewModule . "install.sql", $installSQLContent);

// if we reached this line everything is probably OK: 
echo "Done. Module '$newModuleName' was created from module '$moduleToCreateFromName'.\n";

exit;