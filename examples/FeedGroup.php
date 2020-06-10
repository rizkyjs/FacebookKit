<?php  
require "../vendor/autoload.php";

use Riedayme\FacebookKit\FacebookFeedGroup;

$cookie = 'yourcookie';

$Feed = new FacebookFeedGroup();
$Feed->Auth($cookie,'cookie');

$results =$Feed->GetFeedGroupByToken([
  'groupid' => '1510469412367258',
  'limit' => 5
  ]);

echo "<pre>";
var_dump($results);
echo "</pre>";

/*
array(5) {
  [0]=>
  array(2) {
    ["userid"]=>
    string(15) "100000302451867"
    ["postid"]=>
    string(16) "3195385663814817"
  }
  [1]=>
  array(2) {
    ["userid"]=>
    string(15) "100019196373329"
    ["postid"]=>
    string(15) "589512871698587"
  }
  [2]=>
  array(2) {
    ["userid"]=>
    string(15) "100018001870770"
    ["postid"]=>
    string(15) "614990469111010"
  }
  [3]=>
  array(2) {
    ["userid"]=>
    string(15) "104309806571732"
    ["postid"]=>
    string(16) "1238076569861711"
  }
  [4]=>
  array(2) {
    ["userid"]=>
    string(15) "100038949079067"
    ["postid"]=>
    string(15) "247579619883672"
  }
}
*/