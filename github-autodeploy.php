<?php
/*
 *  GitHub Deploy
 *  https://afaan.ml/github-autodeploy
 *
 *  Automatically deploy your web app repositories on "git push" or any other hook.  
 *
 *  Author: Afaan Bilal
 *  Author URL: https://google.com/+AfaanBilal
 *  
 *  - No Shell Access Required
 *  - Best for Shared Hosting Platforms
 *  - Only public repositories   
 * 
 *  (c) 2016 Afaan Bilal
 *
 */
 
// GitHub Repo (MUST BE PUBLICLY ACCESSIBLE)
define('GIT_REPO','https://github.com/[USERNAME]/[REPO]');

// Deploy Directory (RELATIVE TO THIS FILE)
define('DEPLOY_DIR', '[DEPLOY_DIR]');

// LOG ? (FALSE or 'filename')
define('LOGFILE', 'github-autodeploy.log');

// TimeZone (for LOG)
date_default_timezone_set("Asia/Kolkata");

function writeLog($data)
{
    if (LOGFILE == FALSE)
        return;
    
    if (!file_exists(LOGFILE))
    {
        $logFile = fopen(LOGFILE, "a+");
        fwrite($fh, "--------------------------------------------------------\n");
        fwrite($fh, "|   PHP GitHub AUTO-DEPLOY                             |\n");
        fwrite($fh, "|   https://afaan.ml/github-autodeploy                 |\n");
        fwrite($fh, "|   (c) Afaan Bilal ( https://google.com/+AfaanBilal ) |\n");
        fwrite($fh, "--------------------------------------------------------\n");
        fwrite($fh, "\n\n");
        fclose($logFile);
    }
    
    $fh = fopen(LOGFILE, "a+");
    fwrite($fh, "\nTimestamp: ".date("d-m-Y h:i:s a"));
    fwrite($fh, "\n\n {$data}");
    fwrite($fh, "\n\n");
    fclose($fh);
}

function ExtractZip($zipFile, $extractTo)
{    
    $zip = new ZipArchive;
    
    if ($zip->open($zipFile) === TRUE) 
    {
        $zip->extractTo($extractTo);
        $zip->close();
        return true;
    }
    else
        return false;
}

function recursiveRemoveDirectory($directory)
{
    foreach(glob("{$directory}/*") as $file)
    {
        if(is_dir($file))
            recursiveRemoveDirectory($file);
        else
            unlink($file);
    }
    
    rmdir($directory);
}

function copyr($source, $dest) 
{ 
    // Simple copy for a file 
    if (is_file($source)) 
    {
        chmod($dest, 0777);
        return copy($source, $dest); 
    } 

    // Make destination directory 
    if (!is_dir($dest))  
        mkdir($dest);

    chmod($dest, 0777);

    // Loop through the folder 
    $dir = dir($source); 
    while (FALSE !== $entry = $dir->read()) 
    { 
        // Skip pointers 
        if ($entry == '.' || $entry == '..')
            continue;

        // Deep copy directories 
        if ($dest !== "$source/$entry")
            copyr("$source/$entry", "$dest/$entry");
    }

    // Clean up 
    $dir->close(); 
    return TRUE; 
}

function recursiveMoveDirectory($src, $dest)
{
    if (copyr($src, $dest))
    {
        recursiveRemoveDirectory($src);
        return TRUE;
    }
    
    return FALSE;
}

echo "Deploying...<br>";

$zipRepo  = rtrim(GIT_REPO, '/') . '/archive/master.zip';
$repoName = explode('/', rtrim(GIT_REPO, '/'))[count(explode('/', rtrim(GIT_REPO, '/'))) - 1];
$zipLocal = $repoName . ".zip";

if (!file_exists( DEPLOY_DIR ))
{
    mkdir( DEPLOY_DIR );
}
else 
{
    chmod(DEPLOY_DIR, 0777);
    recursiveRemoveDirectory( DEPLOY_DIR );
    mkdir( DEPLOY_DIR );
}

// Download the latest zip
copy($zipRepo, $zipLocal);

$tempDir = uniqid("autodeploytemp");
$logStr = "";

// Deploy
if (ExtractZip($zipLocal, $tempDir))
{
    if (rename(rtrim($tempDir, '/') . "/" . $repoName . "-master", DEPLOY_DIR)
     || recursiveMoveDirectory(rtrim($tempDir, '/') . "/" . $repoName . "-master", DEPLOY_DIR))
        $logStr = "Deployed ".GIT_REPO." to ".DEPLOY_DIR;
    else
        $logStr = "Error: Failed on RENAME/MOVE";
}
else
    $logStr = "Error: Failed on ExtractZip";

writeLog($logStr);
echo $logStr;

// Clean up
unlink ($zipLocal);
recursiveRemoveDirectory($tempDir);

?>
