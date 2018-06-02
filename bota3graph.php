<?php

require_once('SVGGraph/SVGGraph.php');
require_once('bota3datastripped.php');

error_reporting(E_ALL);

ob_start();

$airlines = array();
$min = 0;
$max;
function _d($a, $b, $c) {
  global $airlines;
  if (!array_key_exists($a[$b], $airlines)) $airlines[$a[$b]] = array();
  $airlines[$a[$b]][] = array($c, $a[$b+1]);
}
foreach ($data as $d) {
  $time = strtotime($d[0]);
  if ($min == 0) $min = $time;
  $max = $time;
  _d($d, 1, $time);
  _d($d, 3, $time);
  if (count($d) == 5) continue;
  _d($d, 5, $time);
}

// incomplete data doesn't go that well
unset($airlines['Lufthansa']);
unset($airlines['Lightish']);

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
 
$settings = array(
  'back_colour'       => '#eee',    'stroke_colour'      => '#000',
  'back_stroke_width' => 0,         'back_stroke_colour' => '#eee',
  'axis_colour'       => '#333',    'axis_overlap'       => 2,
  'axis_font'         => 'Tahoma',  'axis_font_size'     => 10,
  'grid_colour'       => '#666',    'label_colour'       => '#000',
  'pad_right'         => 10,        'pad_left'           => 10,
  'fill_under'        => true,      'fill_opacity'       => .3,
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
