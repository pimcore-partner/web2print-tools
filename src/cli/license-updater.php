<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

$workingDirectory = getcwd();
chdir(__DIR__);
include_once('../../../pimcore/cli/startup.php');
chdir($workingDirectory);

function processPHPContent($fileContent, $license)
{
    //remove phpstorm header
    $regex = '#^<\?php\s*\/\**\*\s*\**\s*Created by.*PhpStorm\.\s*\**\s*User[\s\S]*\*\/#U';

    if (preg_match($regex, $fileContent) === 1) {
        $fileContent = preg_replace($regex, '<?php', $fileContent, 1);
    }

    //remove old license
    $regex = '#^<\?php\s*\/\**\*\s*\**\s*Pimcore[\s\S]*\*\/#U';

    if (preg_match($regex, $fileContent) === 1) {
        $fileContent = preg_replace($regex, '<?php', $fileContent, 1);
    }

    //apply new license
    $regex = '#^<\?php[\n\s]*#';
    if (preg_match($regex, $fileContent) === 1) {
        $fileContent = preg_replace($regex, "<?php\n" . $license . "\n\n", $fileContent, 1);
    } else {
        $fileContent = "<?php\n" . $license . "?>\n\n" . $fileContent;
    }

    return $fileContent;
}

function processTEXTContent($fileContent, $license)
{
    //remove phpstorm header
    $regex = '#^\s*\/\**\*\s*\**\s*Created by JetBrains PhpStorm\.\s*\**\s*User[\s\S]*\*\/\s*#U';

    if (preg_match($regex, $fileContent) === 1) {
        $fileContent = preg_replace($regex, '', $fileContent, 1);
    }

    //remove old license
    $regex = '#^\s*\/\**\*\s*\**\s*Pimcore[\s\S]*\*\/#U';

    if (preg_match($regex, $fileContent) === 1) {
        $fileContent = preg_replace($regex, '', $fileContent, 1);
    }

    //apply new license
    $regex = '#^[\n\s]*#';
    if (preg_match($regex, $fileContent) === 1) {
        $fileContent = preg_replace($regex, $license . "\n\n", $fileContent, 1);
    } else {
        $fileContent = $license . "\n\n" . $fileContent;
    }

    return $fileContent;
}

$rootPath = '../';
$excludedDirectories = [
    '../install',
    '../doc',
    '../Resources/config',
    '../Resources/public/vendor',
];

$license =
'/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Enterprise License (PEL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 * @license    http://www.pimcore.org/license     GPLv3 and PEL
 */
';

$excludePatterns = [];
foreach ($excludedDirectories as $dir) {
    $excludePatterns[] = '(^' . str_replace('/', '\/', $dir) . ')';
}
$excludePatterns_flattened = '/'. implode('|', $excludePatterns) .'/';
echo $excludePatterns_flattened;

$files = [];

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::SELF_FIRST);

foreach ($iterator as $path) {

    /**
     * @var $path SplFileInfo
     */
    if (preg_match($excludePatterns_flattened, $path, $matches) === 1) {
        print $path->__toString() . ' -> exclude' . PHP_EOL;
    } else {
        print $path->__toString() . ' -> include' . PHP_EOL;
        if (!$path->isDir()) {
            $files[$path->getExtension()][] = $path->getPath() . '/' . $path->getFilename();
        }
    }
}

//php files
foreach ($files['php'] as $file) {
    echo 'process file ' . $file . '...';
    $fileContent = file_get_contents($file);
    $fileContent = processPHPContent($fileContent, $license);
    //    echo $fileContent; die();
    file_put_contents($file, $fileContent);
    echo "done\n";
}

//css files
foreach ($files['css'] as $file) {
    echo 'process file ' . $file . '...';
    $fileContent = file_get_contents($file);
    $fileContent = processTEXTContent($fileContent, $license);
    //    echo $fileContent; die();
    file_put_contents($file, $fileContent);
    echo "done\n";
}

//txt files
foreach ($files['txt'] as $file) {
    echo 'process file ' . $file . '...';
    $fileContent = file_get_contents($file);
    $fileContent = processTEXTContent($fileContent, $license);
    //    echo $fileContent; die();
    file_put_contents($file, $fileContent);
    echo "done\n";
}

//js files
foreach ($files['js'] as $file) {
    echo 'process file ' . $file . '...';
    $fileContent = file_get_contents($file);
    $fileContent = processTEXTContent($fileContent, $license);
    //    echo $fileContent; die();
    file_put_contents($file, $fileContent);
    echo "done\n";
}

die("done.\n\n");
