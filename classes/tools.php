<?php
/**
 * Created by PhpStorm.
 * User: markleci
 * Date: 2019-02-01
 * Time: 8:20 PM
 */

namespace tools;

//require_once 'connect.php';
//require_once 'pdo_query.php';

$host = "127.0.0.1";
$dbname = "tools";
$user = "root";
$pass = "zcr980ml";

class tools
{

public $rowLimit = 20; //default rowlimit
public $percentCutoff = 10; //above this value avg size won't be shown
public $recordId;
public $limit;
public $pdo;
public $filePath;
public $folderCount;
public $pathInfo;
public $pathList;
public $files;
public $maxFiles;
public $subFiles;
public $percentComplete;
public $percent;
public $keepPercent;
public $size;
public $totalSize;
public $totalFiles;
public $avgSize;
public $path;

private $colorindex2;

//queries
private $sqlGetPathById = "SELECT path FROM sys_paths WHERE id = ? AND active = 1";
private $sqlGetSubfolders = "SELECT path, id
FROM sys_paths sp 
WHERE 
path LIKE ?
AND path <> ?
AND active = 1";
private $sqlGetFolderCount = "SELECT count(1) count FROM sys_paths WHERE active = 1";

//functions
public function __construct() {

    $this->pdo = new simple_pdo("127.0.0.1", "tools", "root", "zcr980ml");

    $this->colorindex2 = lineargradient(
        0, 255, 0,   // rgb of the last color
        255, 255, 255, // rgb of the first color
        100          // number of colors in your linear gradient
        , 0.6
    );

}




    public function set_limit()
{

    if($this->limit == 0) {
        //0 = no limit
        $this->limit = 99999;
    }
    else {
        if(empty($this->limit)) {
            $this->limit = $this->rowLimit;

        }
    }

}

public function get_filepath()
{

    //get the actual path from the id
    $this->pdo->query($this->sqlGetPathById, ['binds' => [$this->filePath], 'fetch' => 'one']);
    $this->pathInfo = $this->pdo->result;

    $this->filePath = $this->pathInfo['path'];


}

public function count_folders()
{

    $this->pdo->query($this->sqlGetFolderCount, ['fetch' => 'one']);
    $this->folderCount = $this->pdo->result['count'];

    $this->pathList = array();



}

public function get_active_subfolders()
{

    $this->pdo->query($this->sqlGetSubfolders, ['binds' => ["$this->filePath%", $this->filePath]]);
    $pathListAr = $this->pdo->result;

    $this->folderCount = count($pathListAr);
    $this->limit = $this->folderCount;

    foreach($pathListAr as $path) {

        $this->pathList[] = $path['path'];

    }


}

public function percent_row()
{

    $this->percent = colorscale(0, 100, $this->percentComplete, $this->colorindex2);

}

public function percent_complete()
{

    $this->percentComplete = ($this->maxFiles - $this->files) / $this->maxFiles;
    $this->percentComplete = round(($this->percentComplete * 100), 1);
    $this->percentComplete = number_format((float)$this->percentComplete, 1, '.', '');



}

public function keep_percent()
{

    if($this->subFiles > 0 && ($this->maxFiles - $this->files) > 0) {

        $netFiles = $this->maxFiles - $this->files;

        if($netFiles < $this->subFiles)
        {
            $this->keepPercent = 100;

        }
        else
        {

            $this->keepPercent = round(100 * $this->subFiles / $netFiles, 1);

        }



        $this->keepPercent = colorscale(10, 50, $this->keepPercent, $this->colorindex2);



    }
    else
    {
        unset($this->keepPercent);


    }


}


public function format_path()
{

    $this->path = str_ireplace('/Volumes/Data/Users/markleci/Music/audio music apps/', ''
        , $this->path);



}

public function avg_size()
{

    if($this->files > 0 && $this->percentComplete < $this->percentCutoff) {
        $this->avgSize = $this->size / $this->files;

        $this->avgSize = formatBytes($this->avgSize);

    }
    else {
        $this->avgSize = 'N/A';
    }


}


}


?>