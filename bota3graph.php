<?php

require_once('SVGGraph/SVGGraph.php');
require_once('bota3datastripped.php');

error_reporting(E_ALL);

ob_start();

$airlines = array();
$min = 0;
$max;
foreach ($data as $d) {
  $time = strtotime($d[0]);
  if ($min == 0) $min = $time;
  $max = $time;
  if (!array_key_exists($d[1], $airlines)) $airlines[$d[1]] = array();
  $airlines[$d[1]][] = array($time, $d[2]);
  if (!array_key_exists($d[3], $airlines)) $airlines[$d[3]] = array();
  $airlines[$d[3]][] = array($time, $d[4]);
  if (count($d) == 5) continue;
  if (!array_key_exists($d[5], $airlines)) $airlines[$d[5]] = array();
  $airlines[$d[5]][] = array($time, $d[6]);
}

$lastindex = array();
$airlinenames = array();
foreach ($airlines as $k => $v) {
  $lastindex[$k] = 0;
  $airlinenames[] = $k;
}

function lerp($a, $b, $c) { return round($a + ($b - $a) * $x); }

$values = array();
foreach ($airlines as $name => $a) {
  $z = array();
  foreach ($a as $d) {
    $z[$d[0] - $min] = $d[1] * 1000;
  }
  $values[] = $z;
}
print_r($values);
 
$settings = array(
  'back_colour'       => '#eee',    'stroke_colour'      => '#000',
  'back_stroke_width' => 0,         'back_stroke_colour' => '#eee',
  'axis_colour'       => '#333',    'axis_overlap'       => 2,
  'axis_font'         => 'Tahoma',  'axis_font_size'     => 10,
  'grid_colour'       => '#666',    'label_colour'       => '#000',
  'pad_right'         => 10,        'pad_left'           => 10,
  'fill_under'        => true,
  'marker_size'       => 0,
  'line_stroke_width' => 1,
  'graph_title' => 'airlines earnings buring BOTA 3',
  'graph_title_position' => 'top',
  'graph_title_font_weight' => 'bold',
  'legend_entries' => $airlinenames,
  'legend_position' => 'top left 4 4',
  'legend_back_colour' => 'rgba(204,204,204,0.6)',
  'legend_colour' => '#800',
  'label_x' => 'Battle Of The Airlines 3 (Apr 30 - Jun 2 2018)',
  'legend_round' => 5,
  //'grid_division_h' => ($max - $min) / 10,
  'grid_division_h' => ($max - $min),
  'grid_show_subdivisions' => true,
  'show_axis_text_h' => false,
);

 
$graph = new SVGGraph(720, 300, $settings);
//$graph->colours = array('blue');
 
$graph->Values($values);
$graph->Render('MultiLineGraph');

$d = ob_get_flush();
$d = preg_replace('@<script.*</script>@is', '', $d);
file_put_contents('bota3.svg', $d);
