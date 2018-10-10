<?php
$conn = mysqli_connect("127.0.0.1", "mrhery_hery", "hery@1234567890", "mrhery_bigdata");

/*
$q = mysqli_query("SELECT * FROM sample");
while($r = mysqli_fetch_object($q)){
	$id = $r->id;
	$date = date("d-M-Y", strtotime($r->timestamp));
	
	mysqli_query("UPDATE sample SET date = '{$date}' WHERE id = '{$id}'");
}
*/

header("Content-Type: text/plain");

/* Please comment this line with double slahs // to enable download csv 
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="sample.csv"');
//*/

//
//Recursive function
//Main function to manipulate data, sorting into occurances counting
//
function recursive($sets, $total, $index = 0, $data = array()){
	if(count($sets) > 0 AND ($total != $index)){
		foreach($sets[$index] as $a){
			$loop = $total - 1;
			$void = array();
			
			if($index > 0){
				for($ix = 0; $ix < $index; $ix++){
					$void = array_merge($void, $sets[$ix]);
				}
			}
			for($i = 1; $i <= $loop; $i++){
				if(isset($sets[$index + $i])){
					foreach($sets[$index + $i] as $b){
						$m = $a . "," . $b;
						if(!in_array($m, $data) AND $a != $b AND !in_array($b, $sets[$index]) AND !in_array($b, $void)){
							array_push($data, $m);
						}
					}
				}
			}
		}
		$index++;
		return recursive($sets, $total, $index, $data);
	}else{
		return $data;
	}
}

$data = array();
$q = mysqli_query($conn, "SELECT DISTINCT subject as subject FROM sample");
while($subject = mysqli_fetch_object($q)){
	$sets = array();
	$sub = $subject->subject;
	$qx = mysqli_query($conn, "SELECT date as date FROM sample WHERE subject = '{$sub}' GROUP BY date ORDER BY time");
	
	$i = 0;
	while($date = mysqli_fetch_object($qx)){
		$dt = $date->date;
		$sets[$i] = array();
		$qy = mysqli_query($conn, "SELECT * FROM sample WHERE date = '{$dt}'");
		
		while($item = mysqli_fetch_object($qy)){
			array_push($sets[$i], $item->item);
		}
		$i++;
	}
	$data = array_merge($data, recursive($sets, count($sets)));
}
asort($data);
$data = array_count_values($data);
$n = 1;
echo "#, \tFrom, \tTo, \t#Occurance \n";
foreach($data as $d => $val){
	$dx = explode(",", $d);
	echo $n++ . ",\t" . $dx[0] . ",\t" . $dx[1] . ",\t" . $val . "\n";
} 
?>
