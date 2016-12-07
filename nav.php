<?php

$north = floatval($_GET['north']);
$east = floatval($_GET['east']);
$south = floatval($_GET['south']);
$west = floatval($_GET['west']);
$type = !empty($_GET['type']) ? $_GET['type'] : "all";
$nocache = intval($_GET['nocache']);

if ($north <= $south || $west >= $east) {
  echo "Wrong dimensions\n";
  die();
}
if (!file_exists("images/$north-$east-$south-$west.png") || $nocache) {

  include('db-connect.php');

  $im     = imagecreatetruecolor(256,256);
  imagealphablending($im, true);
  $bg = imagecolorallocatealpha($im, 255, 255, 255, 75);
  //$transp = imagecolorallocatealpha($im, 0, 0,0, 127);
  imagefill($im, 0, 0, $bg);
  imagesavealpha($im, true);

  $noir = imagecolorallocatealpha($im, 0, 0, 0, 0);


  $westbound = $west - 0.1;
  $eastbound = $east + 0.1;
  $northbound = $north + 0.1;
  $southbound = $south - 0.1;
  $query = "SELECT * FROM $table WHERE lat BETWEEN $southbound AND $northbound "
  . " AND lon BETWEEN $westbound AND $eastbound";
  if ($type != "all") {
    $type = $db->escape_string($type);
    $query .= " AND type = '$type'";
  }

  $result = $db->query($query);

  while ($line = $result->fetch_assoc()) {
    makeNav($line['type'], $line["lat"], $line["lon"], $line['name']);
  }

  imagepng($im, "images/$north-$east-$south-$west.png");
  imagedestroy($im);

  $db->close();
}

header("Content-type: image/png");
readfile("images/$north-$east-$south-$west.png");

function makeNav($type, $lat, $lon, $name) {
  global $im, $north, $south, $west, $east, $noir, $bg;

  $posx = intval(($lon - $west) / ( $east - $west) * 256);
  $posy = intval(($north - $lat) / ($north - $south) * 256);
  $textx     = $posx - 7 * strlen($name) / 2;

  switch($type) {
    case "APT":
    imageellipse($im, $posx, $posy, 10, 10, $noir);
    break;
    case "FIX":
    imagepolygon($im, array($posx-3, $posy+2, $posx, $posy-3, $posx+3, $posy+2), 3, $noir );
    break;
    case "VOR":
    imagepolygon($im, array(
      $posx-6, $posy+3,
      $posx-6, $posy-3,
      $posx, $posy-7,
      $posx+6, $posy-3,
      $posx+6, $posy+3,
      $posx, $posy+7
    ), 6, $noir );
    imagefilledellipse($im, $posx, $posy, 4, 4, $noir);
    break;
    case "DME":
    imagepolygon($im, array(
      $posx-6, $posy+7,
      $posx-6, $posy-7,
      $posx+6, $posy-7,
      $posx+6, $posy+7,
    ), 4, $noir );
    imagefilledellipse($im, $posx, $posy, 4, 4, $noir);
    break;
    case "NDB":
    imageellipse($im, $posx, $posy, 5, 5, $noir);
    $arr = array(
      $posx-6, $posy+3,
      $posx-6, $posy,
      $posx-6, $posy-3,
      $posx-3, $posy-5,
      $posx  , $posy-7,
      $posx+3, $posy-5,
      $posx+6, $posy-3,
      $posx+6, $posy,
      $posx+6, $posy+3,
      $posx+3, $posy+5,
      $posx  , $posy+7,
      $posx-3, $posy+5,

      $posx  , $posy-4,
      $posx+4, $posy-2,
      $posx+4, $posy+2,
      $posx  , $posy+4,
      $posx-4, $posy+2,
      $posx-4, $posy-2
    );
    for($i = 0; $i < 18; $i++) {
      imagesetpixel($im, $arr[2*$i], $arr[2*$i+1], $noir);
    }
    break;
  }

  imagestring($im, 3, $textx, $posy + 6, $name, $noir);
  imagesavealpha($im, true);

}
