<?php
/*
Created by Mark Leci - 2020-06-24

*/

//https://www.w3schools.com/php/php_ajax_livesearch.asp
//replace the Xml with a query search of existing ingredients

//$xmlDoc=new DOMDocument();
//$xmlDoc->load("links.xml");

//$x=$xmlDoc->getElementsByTagName('link');
//error_reporting(E_ALL);


//var_dump($_GET);

$host = "markleci.com";
$dbname = "markleci_food";
$user = "markleci_food";
$pass = "v%r0qC0dM2*2";

require_once '../functions.php';
require_once '../qrs.php';

error_reporting(0);

$sqlGetSpices = "SELECT spice.id, spice_jar.id record_id, spice.name spice, jar.name container
     , spice_jar.percentage amount, spice_jar.size, ct.name category, quantity
FROM spice 
left outer join spice_jar on spice.id = spice_jar.spice_id
left outer join jar on jar.id = spice_jar.jar_id
left outer join category ct on ct.id = spice_jar.category_id
WHERE 1=1
ORDER BY ct.name, spice.name";

$element = new SimpleXMLElement('<pages></pages>');
$elementChild = $element->addChild('link');

function array_walk_simplexml(&$value, $key, &$sx) {
    $sx->addChild($key, $value);
}

use \tools\simple_pdo;
$pdo = new simple_pdo($host, $dbname, $user, $pass);
$pdo->query($sqlGetSpices, ['fetch'=>'all']);
$itemList = $pdo->result;

//var_dump($itemList);

while ($row = $itemList) {
    $sx_cr = $elementChild->addChild('title');
    array_walk($row, 'array_walk_simplexml', $sx_cr);
}
$dom_sxe = dom_import_simplexml($element);
$dom = new DOMDocument('1.0');
$dom->formatOutput = true;
$dom_sxe = $dom->importNode($dom_sxe, true);
$dom_sxe = $dom->appendChild($dom_sxe);

var_dump($dom->saveXML());

//echo $dom->saveXML();
//echo $sxe->asXML();

//troubleshoot($itemList);

//get the q parameter from URL
$q=$_GET["q"];

//lookup all links from the xml file if length of q>0
if (strlen($q)>0) {
    $hint="";
    for($i=0; $i<($x->length); $i++) {
        $y=$x->item($i)->getElementsByTagName('td');
        $z=$x->item($i)->getElementsByTagName('url');
        if ($y->item(0)->nodeType==1) {
            //find a link matching the search text
            if (stristr($y->item(0)->childNodes->item(0)->nodeValue,$q)) {
                if ($hint=="") {
                    $hint="<a href='" .
                        $z->item(0)->childNodes->item(0)->nodeValue .
                        "' target='_blank'>" .
                        $y->item(0)->childNodes->item(0)->nodeValue . "</a>";
                } else {
                    $hint=$hint . "<br /><a href='" .
                        $z->item(0)->childNodes->item(0)->nodeValue .
                        "' target='_blank'>" .
                        $y->item(0)->childNodes->item(0)->nodeValue . "</a>";
                }
            }
        }
    }
}

// Set output to "no suggestion" if no hint was found
// or to the correct values
if ($hint=="") {
    $response="no suggestion";
} else {
    $response=$hint;
}

//output the response
echo $response;

?>