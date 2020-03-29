<?php
date_default_timezone_set('America/Denver');

//add class files
require 'classes/bootstrap4.php';
require 'classes/form4.php';
require 'classes/pdo_query.php';
require 'classes/fusioncharts.php';

$greenSpan = 'rgba(0, 255, 0, 0.6)';
$redSpan = 'rgba(255, 0, 0, 0.6)';
$yellowSpan = 'rgba(255, 255, 0, 0.6)';
$blackSpan = 'rgba(20,20,20,0.9)';
$highlight = 'rgba(0, 255, 0, 0.6)';
$highlightRed = 'rgba(255, 0, 0, 0.6)';

//white to green
$colorindexgreen = lineargradient(
    0, 255, 0,   // rgb of the last color
    255, 255, 255, // rgb of the first color
    100          // number of colors in your linear gradient
    , 0.6
);

//white to red
$colorindexRed = lineargradient(
    255, 0, 0,   // rgb of the last color
    255, 255, 255, // rgb of the first color
    100          // number of colors in your linear gradient
    , 0.6
);

/**
 * colorscale function. Takes a min, max and color array and creates a span indicating the % complete by color
 *
 * @access public
 * @param mixed $min
 * @param mixed $max
 * @param mixed $value
 * @param mixed $colorindex
 * @param bool  $cell
 * @return string
 */
function colorscale($min, $max, $value, $colorindex, $cell = true)
{
    if($value < 0 || empty($value)) {
        $value = 0;
    }

    $a = $max - $min;
    $b = $value - $min;

    $percentComplete = round(($b / $a) * count($colorindex));

    if($percentComplete > 100) {
        $percentComplete = 100;
    }

    if(isset($colorindex[$percentComplete - 1])) {
        $spanColor = $colorindex[$percentComplete - 1];
    }
    else {
        $spanColor = $colorindex[0];
    }

    if($cell != true) {
        return $spanColor;
    }
    else {

        // troubleshoot($value . "|" . $spanColor);

        return $value . "|" . $spanColor;
    }

}

/**
 * lineargradient function. Mark Leci's rgb linear gradient function with opacity
 *
 * @access public
 * @param mixed $ra
 * @param mixed $ga
 * @param mixed $ba
 * @param mixed $rz
 * @param mixed $gz
 * @param mixed $bz
 * @param mixed $iterationnr
 * @param int   $opacity (default: 1)
 * @return array
 */
function lineargradient($ra, $ga, $ba, $rz, $gz, $bz, $iterationnr, $opacity = 1)
{
    $colorindex = [];
    for($iterationc = 1; $iterationc <= $iterationnr; $iterationc++) {
        $iterationdiff = $iterationnr - $iterationc;

        $colorindex[] = "rgba(" .

            intval((($ra * $iterationc) + ($rz * $iterationdiff)) / $iterationnr) . ", " .
            intval((($ga * $iterationc) + ($gz * $iterationdiff)) / $iterationnr) . ", " .
            intval((($ba * $iterationc) + ($bz * $iterationdiff)) / $iterationnr) . ", " .
            $opacity
            . ")";
    }

    return $colorindex;
}



function errors($value=0)
{

    if($value == 1)
    {

        ini_set('display_errors', 1);

    }
    else
    {
        ini_set('display_errors', 0);

    }


}

function food_colour($name,$level)
{
    $greenSpan = 'rgba(0, 255, 0, 0.6)';
    $redSpan = 'rgba(255, 0, 0, 0.6)';
    $yellowSpan = 'rgba(255, 255, 0, 0.6)';
    $blackSpan = 'rgba(20,20,20,0.9)';


    if($level == 'Black')
    {
        $name .= "|$blackSpan";
    }
    else if($level == 'Green')
    {
        $name.= "|$greenSpan";
    }
    else if($level == 'Red')
    {
        $name .= "|$redSpan";
    }
    else if($level == 'Yellow')
    {
        $name .= "|$yellowSpan";
    }

    return $name;

}

function convertToHoursMins($time, $format = '%2dh %2dm') {
    if ($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);

    if($hours > 0 && $minutes > 0)
    {
        return sprintf($format, $hours, $minutes);

    }
    else if($hours == 0)
    {

        return sprintf('%2dm', $minutes);

    }
    else if($minutes == 0)
    {
        return sprintf('%2dh', $hours);

    }

    return sprintf($format, $hours, $minutes);
}

function curl_build($url, $headers = 0)
    //build a curl request with or without headers
{
    //construct curl request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if($headers == 1) {
        curl_setopt($ch, CURLOPT_HEADER, 1);
    }
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    $request = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    $return_ar = [$code, $request];

    return $return_ar;

    curl_close($ch);
}


/**
 * sorts items by type, then earned date, then earned
 * @param $a
 * @param $b
 * @return int
 */
function typesort($a, $b)
{

    //var_dump($a);
    //exit;

    if($a->fromUser->earned == $b->fromUser->earned && $a->trophyType != $b->trophyType)
    {
        //both earned or neither earned, sort by type

        if($a->trophyType=='platinum')
        {
            $x = 'a';

        }
        else if ($a->trophyType=='gold')
        {

            $x = 'b';

        }
        else if ($a->trophyType=='silver')
        {

            $x = 'c';

        }
        else if ($a->trophyType=='bronze')
        {

            $x = 'd';

        }
        else if ($a->trophyType=='')
        {

            $x = 'e';
        }

        if($b->trophyType=='platinum')
        {
            $y = 'a';

        }
        else if ($b->trophyType=='gold')
        {

            $y = 'b';

        }
        else if ($b->trophyType=='silver')
        {

            $y = 'c';

        }
        else if ($b->trophyType=='bronze')
        {

            $y = 'd';

        }
        else if ($b->trophyType=='')
        {

            $y = 'e';
        }

        return strcmp($x, $y);


    }
    else if ($a->fromUser->earned == $b->fromUser->earned && $a->trophyType == $b->trophyType)
    {
        //earned and type are the same, sort by date if earned and name if not earned

        if($a->fromUser->earned == '')
        {
            //both unearned
            return strcmp($a->trophyName, $b->trophyName);


        }
        else
        {
            //both earned
            return strcmp($b->fromUser->earnedDate, $a->fromUser->earnedDate);


        }


    }

    else
    {

        if($a->fromUser->earned == '')
        {
            $x = 0;


        }
        else
        {

            $x = 1;
        }
        if ($b->fromUser->earned == '')
        {

            $y = 0;

        }
        else
        {

            $y = 1;

        }

        return $y-$x;


    }


}

/**
 * sorts items by type, then earned date, then earned
 * @param $a
 * @param $b
 * @return int
 */
function earnsort($a, $b)
{

    //var_dump($a);
    //exit;

    if($a->trophyType <> $b->trophyType)
    {
        //both earned or neither earned, sort by type

        if($a->trophyType=='platinum')
        {
            $x = 'a';

        }
        else if ($a->trophyType=='gold')
        {

            $x = 'b';

        }
        else if ($a->trophyType=='silver')
        {

            $x = 'c';

        }
        else if ($a->trophyType=='bronze')
        {

            $x = 'd';

        }
        else if ($a->trophyType=='')
        {

            $x = 'e';
        }

        if($b->trophyType=='platinum')
        {
            $y = 'a';

        }
        else if ($b->trophyType=='gold')
        {

            $y = 'b';

        }
        else if ($b->trophyType=='silver')
        {

            $y = 'c';

        }
        else if ($b->trophyType=='bronze')
        {

            $y = 'd';

        }
        else if ($b->trophyType=='')
        {

            $y = 'e';
        }

        return strcmp($x, $y);


    }
    else if ($a->fromUser->earned != $b->fromUser->earned)
    {
        //types are the same, sort by earned

        $x = ($a->fromUser->earned == 1) ? 1 : 0;
        $y = ($b->fromUser->earned == 1) ? 1 : 0;


        return $y-$x;
    }
    else
    {
        if($a->fromUser->earned == '')
        {
            //both unearned
            return strcmp($a->trophyName, $b->trophyName);


        }
        else
        {
            //both earned
            return strcmp($b->fromUser->earnedDate, $a->fromUser->earnedDate);


        }


    }






}


function curl_dl($url)
{
    set_time_limit(0);
    //This is the file where we save the    information
    $fp = fopen(dirname(__FILE__) . '/localfile.tmp', 'w+');
    //Here is the file we are downloading, replace spaces with %20
    $ch = curl_init(str_replace(" ", "%20", $url));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    // get curl response
    curl_exec($ch);
    curl_close($ch);
    fclose($fp);

}

/**
 * @param $value
 * @return string
 */
function rarity($value)
{

    //ultra rare
    //very rare
    //rare
    //uncommon
    //common
    if ($value == 0)
    {
        return "Ultra rare";

    }
    else if ($value == 1)
    {
        return "Very rare";

    }
    else if ($value == 2)
    {
        return "Rare";

    }
    else if ($value == 3)
    {
        return "Uncommon";

    }
    else
    {
        return "Common";

    }



}


/**
 * @param $b
 * @param $a
 * @return int
 */
function date_sort_rev($b, $a) {

    $a = date('Y-m-d', strtotime($a));
    $b = date('Y-m-d', strtotime($b));

    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? -1 : 1;

    //return strtotime($a) - strtotime($b);
}

/**
 * @param $a
 * @param $b
 * @return int
 */
function tsort($a, $b)
{
    return strcmp($a->trophyTitleName, $b->trophyTitleName);

}


/**
 * @param        $host - database host eg localhost
 * @param        $database - name
 * @param        $username
 * @param        $password
 * @param int    $cutoff - number of days before a new backup is needed
 * @param string $filename - filename mask
 * @param string $directory - path to search
 * @param string $format
 */
function backup_db($host, $database, $username, $password, $cutoff, $filename, $directory, $format='Y-m-d')
{

    /*
     * example
     * backup_db('localhost', 'baseball','root','zcr980ml',30
    ,'baseball_','/Volumes/Data/Users/markleci/Dropbox/Websites/localhost/baseball/DB' );
     *
     */

    //first check if a backup exists
    $fileList = scandir($directory);


    if($fileList) {
        foreach($fileList as $backupFile) {

            //check the files against the name pattern
            if(strpos($backupFile, $filename) !== false) {

                //we've found a file with the same name pattern, now check its
                // date against the cutoff

                $intervalList[] = date("Y-m-d",filemtime($directory."/".$backupFile));


            }

        }


        //sort array newest first and check the first one only
        usort($intervalList, "date_sort_rev");


        $dateObj = new DateTime($intervalList[0]);
        $today = new DateTime();

        $interval = $dateObj->diff($today);

        $newdate = $today->format($format);
        $interval = $interval->format('%a');


        if($interval >= $cutoff) {
            //the backup is older than the cutoff date, we need to create a new backup

            $filePath = $directory . "/" . $filename . $newdate . ".sql";

            $filePath = escapeshellarg($filePath);
            $username = escapeshellarg($username);
            $password = escapeshellarg($password);
            $host = escapeshellarg($host);
            $database = escapeshellarg($database);

            $command = "/usr/local/Cellar/mysql/5.7.21/bin/mysqldump --log-error='error.txt' --user=$username --password=$password --single-transaction --host=$host --databases $database --result-file=$filePath";

            //display($command);

            exec($command);
            //echo "Database backed up<br/>";
            return;
        }



    }



    return;



}


function lock_by_ip($allowedList)
{

    $client = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP)) {


        foreach($allowedList as $ip_address) {

            if($client == $ip_address) {
                return true;
            }


        }


    }

    exit;


}

/**
 * display function.
 *
 * @access public
 * @param mixed  $variable
 * @param string $heading (default: '')
 * @return void
 */
function display($variable, $heading = '')
{
    if(!isset($variable)) {

    }
    else {
        if
        (is_array($variable)) {
            echo "<br/>";
            if($heading <> '') {
                Bootstrap4::heading($heading, 3);

            }
            echo "<pre>";
            print_r($variable);
            echo "</pre>";
            echo "<br/><br/>";
        }
        else {
            if(is_object($variable)) {
                echo "<br/>";
                if($heading <> '') {
                    Bootstrap4::heading($heading, 3);

                }
                echo "<pre>";
                var_dump($variable);
                echo "</pre>";
                echo "<br/><br/>";
            }
            else {
                if($heading <> '') {
                    echo "<br/>";
                    Bootstrap4::heading($heading, 3);

                }
                echo "<br/>";
                echo $variable;
                echo "<br/><br/>";
            }
        }
    }

}

function sortFilesByModified($path,$extension='csv')
{
    $results = array();

    $handler = opendir($path);

    while ($file = readdir($handler)) {

        $info = new SplFileInfo($file);
        if($info->getExtension() == $extension)
        {

            $results[] = array('file' => $file, 'time' => filemtime($file));


        }

    }

    closedir($handler);

    uasort($results, function($file1, $file2) {
        if ( $file1['time'] == $file2['time'] )
            return 0;
        return $file1['time'] < $file2['time'] ? -1 : 1;
    });

    return $results;

}

function exec_time($startTime, $line, $round = 1)
{

    $curTime = microtime(true);
    $execution_time = round($curTime - $startTime, $round);

    echo "<br/><br/><b>Total Execution Time:</b> as of line $line is $execution_time Seconds<br/><br/>";

}

function array_flatten($array, $keyColumn, $valueColumn='')
{

    foreach($array as $key => $row)
    {

        if($valueColumn <> '')
        {
            $returnArray[$row[$keyColumn]] = $row[$valueColumn];
        }
        else
        {
            $returnArray[$row[$keyColumn]] = $row[$keyColumn];
        }



    }

    return $returnArray;

}

function array_orderby()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
                $tmp[$key] = $row[$field];
            $args[$n] = $tmp;
        }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}


/**
 * tumblr_content function.
 *
 * @access public
 * @param mixed $post
 * @return void
 */
function tumblr_content($post)
{

    $content = array();

    //display($post);

    if($post->type == 'text') {
        if(strlen($post->body) > 0) {
            $content[0]['body'] = $post->body;

        }
        else {
            //if no text, use title
            $content[0]['body'] = $post->title;
        }


        $content[0]['url'] = $post->post_url;

    }
    else {
        if($post->type == 'chat') {

            foreach($post->dialogue as $chatItem) {

                $content[0]['body'] = $post->phrase;
                $content[0]['body'] .= "<br/><br/>";


            }


            $content[0]['url'] = $post->post_url;

            //is this needed?
            $content[0]['body'] .= $post->body;


        }
        else {
            if($post->type == 'answer') {


                $content[0]['url'] = $post->post_url;
                $content[0]['body'] = $post->question;
                $content[0]['body'] .= "<br/><br/>";
                $content[0]['body'] .= $post->answer;


            }
            else {
                if($post->type == 'photo') {

                    //display(count($post->photos), 'item count');


                    //handle multiple photo posts
                    if(is_array($post->photos)) {

                        foreach($post->photos as $key => $photo) {
                            //will need to adjust this for multiple photo posts

                            if(is_object($photo->alt_sizes[4])) {
                                //get 100px image
                                $thumb = $photo->alt_sizes[4]->url;

                            }
                            else {
                                if(is_object($photo->alt_sizes[3])) {
                                    $thumb = $photo->alt_sizes[3]->url;

                                }
                                else {
                                    if(is_object($photo->alt_sizes[2])) {
                                        $thumb = $photo->alt_sizes[2]->url;

                                    }
                                    else {


                                    }
                                }
                            }

                            if($photo->original_size->url <> '' && !empty($photo->original_size->url)) {
                                $content[$key]['url'] = $photo->original_size->url;
                                $content[$key]['thumb'] = $thumb;

                                //array_unshift($content, $contentReturn);

                            }

                        }
                    }
                    else {
                        if($post->photos[0]->original_size->url <> '' && !empty($post->photos[0]->original_size->url)) {

                            if(is_object($post->photos[0]->alt_sizes[4])) {
                                //get 100px image
                                $thumb = $post->photos[0]->alt_sizes[4]->url;

                            }
                            else {
                                if(is_object($post->photos[0]->alt_sizes[3])) {
                                    $thumb = $post->photos[0]->alt_sizes[3]->url;

                                }
                                else {
                                    if(is_object($post->photos[0]->alt_sizes[2])) {
                                        $thumb = $post->photos[0]->alt_sizes[2]->url;

                                    }
                                    else {


                                    }
                                }
                            }


                            $content[0]['url'] = $post->photos[0]->original_size->url;
                            $content[0]['thumb'] = $thumb;


                            //array_unshift($content, $contentReturn);
                        }
                    }


                }
                else {
                    if($post->type == 'quote') {
                        //$content = $post->text;

                    }
                    else {
                        if($post->type == 'audio') {
                            //call another function that gets the audio url
                            //display($post);

                            $content[0]['url'] = $post->audio_source_url;
                            $content[0]['body'] = $post->summary;


                        }
                        else {
                            if($post->type == 'video') {
                                //display('video found');
                                //write the same function for video
                                $content[0]['thumb'] = $post->thumbnail_url;
                                $content[0]['url'] = $post->video_url;

                            }
                            else {
                                if($post->type == 'link') {
                                    //display($post);
                                    $content[0]['url'] = $post->post_url;
                                    $content[0]['body'] = $post->summary;
                                    $content[0]['body'] .= "<br/><br/>";
                                    $content[0]['body'] .= $post->excerpt;
                                    $content[0]['body'] .= "<br/><br/>";
                                    $content[0]['body'] .= $post->url;
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    //display($content);

    return $content;


}


function curl_exists($url)
{

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // $retcode >= 400 -> not found, $retcode = 200, found.
    curl_close($ch);


    return $retcode;

}


/**
 * build_link function.
 *
 * @access public
 * @param mixed $type
 * @param mixed $content
 * @param mixed $url
 * @return void
 */
function build_link($type, $content, $url)
{


    if($type == 'text' || $type == 'answer' || $type == 'chat') {
        echo "<div class='col-md-3'>";
        echo "<div class='text-clip thumbnail'>";
        echo "<a href='$url' target='_blank'>Permalink</a><br/>";
        echo $content['body'];
        echo "</div>";
        echo "</div>";

    }
    else {
        if($type == 'video') {


            echo "<div class='col-md-3'>";
            echo "<div class='thumb-clip'>";
            echo "<video controls src='$url'></video>";
            echo "</div>";
            echo "</div>";


        }
        else {
            if($type == 'photo') {
                echo "<div class='col-md-3'>";
                echo "<div class='thumbnail thumb-clip'>";
                echo "<a href='$url' target='_blank'>";
                echo "<img class='img-responsive center-block img-thumbnail' width='180px' src='$content'/><a/>";
                echo "</div>";
                echo "</div>";

            }
            else {
                if($type == 'audio') {
                    echo "<div class='col-md-3'>";
                    echo "<div class='thumbnail thumb-clip'>";
                    echo "<label>$content";
                    echo "<audio controls src='$url'></audio></label>";
                    echo "</div>";
                    echo "</div>";


                }
            }
        }
    }


}


function get_attribute($url, $attribute)
{

    //this line shouldn't be needed, but otherwise the & gets url encoded
    $url = str_replace('&amp;', '&', $url);

    $parts = parse_url($url);
    parse_str($parts['query'], $query);

    return $query[$attribute];


}

function build_zip($blog, $label, $content, $requestId)
{
    //$ext = mt_rand(0,999999);
    //mkdir('tmpdl'.$ext,0770, true);

    $zipname = $blog . "_" . $label . ".zip";

    $zip = new ZipArchive;
    $zip->open($zipname, ZipArchive::CREATE);
    foreach($content as $file) {
        set_time_limit(60);

        //var_dump($file);
        if($file['text']) {
            $breaks = array("<br />", "<br>", "<br/>");
            $file['text'] = str_ireplace($breaks, "\r\n", $file['text']);
            $file['text'] = html_entity_decode($file['text']);
            $file['text'] = strip_tags($file['text']);
            $zip->addFromString(basename($file['url']) . '.txt', $file['text']);

        }
        else {
            if($file['image']) {
                $fileBin = file_get_contents($file['image']);

                if($fileBin !== false) {
                    if(filesize($file['image']) > 10 * 1024 * 1024) {
                        //echo $file['image']." skipped <br/>";
                    }
                    else {

                        $zip->addFromString(basename($file['image']), $fileBin);
                    }
                }

            }
            else {
                if($file['audio']) {
                    $fileBin = file_get_contents($file['audio']);

                    if(filesize($fileBin) > 10 * 1024 * 1024) {
                        echo $file['audio'] . " skipped <br/>";
                    }
                    else {
                        if($fileBin !== false) {

                            $zip->addFromString(basename($file['audio']), $fileBin);
                        }
                        else {
                            //echo filesize($fileBin)." ".$file['audio']." skipped due to size<br/>";

                        }
                    }


                }
                else {
                    if($file['video']) {
                        //do nothing


                    }
                }
            }
        }
        //$file = file_get_contents($file);
        //echo basename($file)."<br/>";

        //$zip->addFile($file);

    }
    $zip->close();


    if(file_exists($zipname)) {
        $filesize = round(filesize($zipname) / 1024 / 1024, 1);
        //echo "$zipname created filesize $filesize MB";

        $pdo = new _pdoQuery;
        $sqlUpdateRequest = "UPDATE request SET file_size = ? WHERE id = ?";
        $pdo->query($sqlUpdateRequest, ['binds' => [$filesize, $requestId], 'type' => 'update']);

        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename=' . $zipname);
        header('Content-Length: ' . filesize($zipname));
        readfile($zipname);

        //remove temporary file
        unlink($zipname);


    }
    else {

        //echo "Error: File does not exist<br/>";
    }


}

/**
 * troubleshoot function. Display a variable and exit to prevent any database activity
 *
 * @access public
 * @param mixed $variable
 * @return void
 */
function troubleshoot($variable)
{

    display($variable);

    exit;


}

/**
 * get_date function.
 *
 * @access public
 * @param mixed  $format
 * @param string $date (default: '')
 * @return string
 */
function get_date($format, $date = '')
{

    if($date == '')
    {
        $dateObj = new DateTime();

    }
    else
    {
        $dateObj = new DateTime($date);
    }



    $date = $dateObj->format($format);

    return $date;

}

function parse_ingredient($ingredient)
{
    $ingredient = strtolower($ingredient);

    $pattern = '/,.*$/';
    $ingredient = preg_replace($pattern,'',$ingredient);

    $pattern = '/[0-9]\/?[0-9]? cups?/i';
    //$ingredient = preg_replace($pattern,'',$ingredient);

    $pattern = '/[0-9]\/?[0-9]? tsps?/i';
    //$ingredient = preg_replace($pattern,'',$ingredient);

    $pattern = '/[0-9]\/?[0-9]? teaspoons?/i';
    //$ingredient = preg_replace($pattern,'',$ingredient);

    $pattern = '/[0-9]\/?[0-9]? tbsps?/i';
    //$ingredient = preg_replace($pattern,'',$ingredient);

    $pattern = '/[0-9]\/?[0-9]? tablespoons?/i';
    //$ingredient = preg_replace($pattern,'',$ingredient);

    $pattern = '/[0-9]*.ounce/i';
    //$ingredient = preg_replace($pattern,'',$ingredient);

    $pattern = '/[0-9]*.oz/i';
    //$ingredient = preg_replace($pattern,'',$ingredient);

    $pattern = '/[0-9]*.ml/i';
    //$ingredient = preg_replace($pattern,'',$ingredient);

    $pattern = '/[0-9]+.g /';
    //$ingredient = preg_replace($pattern,'',$ingredient);

    $pattern = '/[0-9]* quarts?/i';
    //$ingredient = preg_replace($pattern,'',$ingredient);

    $pattern = '/[0-9]* dash /i';
    //$ingredient = preg_replace($pattern,'',$ingredient);

    $pattern = '/ can /i';
    //$ingredient = preg_replace($pattern,'',$ingredient);

    $pattern = '/[^a-zA-Z ]/';
    $ingredient = preg_replace($pattern,'',$ingredient);

    $pattern = '/ {2,}/';
    $ingredient = preg_replace($pattern,' ',$ingredient);

    $ingredient = trim($ingredient);
    $ingredient = ucwords($ingredient);

    return $ingredient;


}

function datediff($date)
{

    $dateObj = new DateTime($date);
    $today = new DateTime();

    $interval = $dateObj->diff($today);
    $interval = $interval->format('%a');

    return $interval;

}

class MyDateTime extends DateTime
{

    public function addMonth($num = 1)
    {
        $date = $this->format('Y-n-j');
        list($y, $m, $d) = explode('-', $date);

        $m += $num;
        while ($m > 12)
        {
            $m -= 12;
            $y++;
        }

        $last_day = date('t', strtotime("$y-$m-1"));
        if ($d > $last_day)
        {
            $d = $last_day;
        }

        $this->setDate($y, $m, $d);
    }

}

function check_month($thisMonth, $month1, $month2)
{

    if($thisMonth >= $month1 && $thisMonth <= $month2) {
//regular order 1-12
        //echo "Regular " . $seasonDatum['name'] . "<br/>";

        return true;

    }
    else if ($thisMonth >= $month1 && $month2 < $month1)
    {
        //irregular order
        //echo "Unregular " . $seasonDatum['name'] . "<br/>";

        return true;

    }
    else if($thisMonth < $month1 && $month2 < $month1
        && $thisMonth <= $month2)
    {
        //echo "other side " . $seasonDatum['name'] . "<br/>";

        return true;

    }
    else
    {
        return false;

    }

}

function sizesort($a, $b) {

    if ($a['total'] == $b['total']) {
        return 0;
    }
    return ($a['total'] < $b['total']) ? -1 : 1;


}

?>