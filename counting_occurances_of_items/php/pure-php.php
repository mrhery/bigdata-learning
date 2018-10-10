<?php
header("Content-Type: text/plain");

/* Please comment this line with double slash // to enable download csv 
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="sample.csv"');
//*/

#setting
$csv_file = "sample.csv";

$f = fopen($csv_file, "r");
$datas = array();
while(! feof($f)){
	$datas[] = fgetcsv($f);
}
fclose($f);

array_splice($datas, 0, 1);

$subjects = array();
$pdata = array();
$xdata = array();

for($i = 0; $i < count($datas); $i++){
	$data = $datas[$i];
	$odate = $data[2];
	$date = date_create_from_format ("d/m/Y H:i:s", $odate);
	$ndate = date_format($date, "d-M-Y");
	$datas[$i] = array("subject" => $data[0], "item" => $data[1], "timestamp" => $data[2], "date" => $ndate);
	$data = $datas[$i];
	$sub = $data["subject"];
	
	@$subjects[$sub] += 1;
}
$datas = json_decode(json_encode($datas));

foreach($subjects as $sub => $val){
	$idates = array();
	$tdate = array();
	
	foreach($datas as $data){
		if($data->subject == $sub){
			if(!in_array($data->date, $idates)){
				array_push($idates, $data->date);
			}
		}
	}
	
	foreach($idates as $idate){
		$item = array();
		foreach($datas as $data){
			if($data->subject == $sub AND $data->date == $idate){
				$item[] = $data->item;
			}
		}
		$tdate[] = $item;
	}
	$tdate = json_decode(json_encode($tdate));
	$pdata = array_merge($pdata, recursive($tdate, count($tdate)));
}

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

asort($pdata);
$data = array_count_values($pdata);
$n = 1;
echo "#, \tFrom, \tTo, \t#Occurance \n";
foreach($data as $d => $val){
	$dx = explode(",", $d);
	echo $n++ . ",\t" . $dx[0] . ",\t" . $dx[1] . ",\t" . $val . "\n";
}
?>
