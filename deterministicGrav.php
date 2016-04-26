<?php
include_once('src2\Map.php');
  include_once('src2\MapTypeId.php');
  include_once ('src2\Helper\MapHelper.php');
  include_once 'src2\Overlays\Animation.php';
  include_once 'src2\Overlays\Marker.php';
  include_once 'src2\Events\MouseEvent.php';
  include_once 'src2\Services\Directions\Directions.php';
  include_once 'Widop\HttpAdapter\CurlHttpAdapter.php';
  include_once 'src2\Services\Directions\DirectionsRequest.php';
  include_once 'src2\Services\Base\TravelMode.php';
  include_once 'src2\Services\Base\UnitSystem.php';

ob_start();
register_shutdown_function('shutdownFunction');
function shutDownFunction() { 
    $error = error_get_last();
    if ($error['type'] == 1) {
	ob_end_clean();
        echo "Parking not found!!! Please start again.";   
    } 
}
$myarr=array();
function getNorthPull($node,$da,$tim,$conge){
try{
global $myarr;
$n= substr($node,0,4);
include('dbinfo.inc');
// Search the rows in the markers table
//$query = "SELECT A.`node_id`, A.`node_name`, A.`latitude`, A.`longitude`, B.`distance_miles` FROM `nodes` A, `adjacenct_nodes` B WHERE A.`node_id`IN (SELECT `adj_node_id` from `adjacenct_nodes` where `node_id`= '7010') and A.`node_id` = B.`adj_node_id`";
$query = "SELECT `Adj. Node`, `Distance` from `table 5` where `Node`= '$n'"; //distances table
$result = mysqli_query($con,$query) or die("Invalid query1: " . mysql_error());


$result1 = array();
$force= array();
 while($row = mysqli_fetch_assoc($result)) 
 {
 
 $result1[]= $row;
//$node_id =$row['node_id'];
//$node_name =$row['node_name'];
//$lat =$row['latitude'];
//$lng =$row['longitude'];
//$dist =$row['distance_miles'];
 
}
foreach ($result1 as $key => $value) { // Loops 4 times because there are 4 columns
        //echo $value['node_id'];
		//echo $value['distance_miles'];
		$block= array();
		$x= $value['Adj. Node'];
		if(substr($x,4,1)=='S')
		{
		$x='NULL';
		}
		else {
		$x= $value['Adj. Node'];}
		if(substr($x,4,1)=='S')
		{
		$z='NULL';
		}
		else {
		$z= substr($x,0,4); }
		$y= $value['Distance'];
		//echo $z;
		
     
	  $query1= "SELECT `block_id` from `geolocations` where (node1='$n' and node2= '$z') or (node1='$z' and node2= '$n')"; 
	   
	   $res= mysqli_query($con,$query1) or die("Invalid query: " . mysql_error());
	   
	while($row1 = mysqli_fetch_array($res))
 {
   $block[] = $row1;
}
if(isset($block[0]['block_id'])){
$a= $block[0]['block_id'];}
else{
$a='NULL';}

if(isset($block[1]['block_id']))
{
$b= $block[1]['block_id'];
}
else{ $b= 'NULL';}
//echo $a;
//echo $b;
//print_r($block);

$query2= "SELECT avg(`available`)*'$conge' as `available_slots` from `availability` where `block_id` IN ('$a','$b') and DATE_FORMAT(timestamp,'%H:%i:%s') Like '$tim' and DATE_FORMAT(timestamp,'%e %b %y') = '$da'";
$rest= mysqli_query($con,$query2) or die("Invalid query1: " . mysql_error());


while($row2 = mysqli_fetch_assoc($rest)) {

 $c= $row2['available_slots'];
 }
  if($c=='NULL' || $c=='' || $c<1)
{
$c=0;
}

//echo $c;


 $force[$x]= $c/ ($y*$y);



print("\r\n");
unset($block); 
	//break;   
    //} 
	
	}
//print_r($force);
arsort($force);
$temp=key($force);
if(array_values($force)[0]==0)
{
//echo $n;
//return $thullu;  //////////////////////////////////////make changes here....
$query = "SELECT `Adj. Node`, `Distance` from `table 5` where `Node`= '$n'"; //distances table
$result = mysqli_query($con,$query) or die("Invalid query1: " . mysql_error());
while($row = mysqli_fetch_assoc($result)) 
 {
 $result1[]= $row;
 }
$m= array();
//print_r($result1);
foreach($result1 as $key => $value){

$j= $value['Adj. Node'];
if(substr($j,4,1)=='S'){
$m[$j]=1000;
}
else{
$m[$j]= $value['Distance'];
}
}
//print_r($m);
asort($m);
$t= key($m);
//echo $t;
$myarr[]=$t;
//return $t;
$sub=substr($t,4,1);
if($sub=='N'){
	$re= getNorthPull($t,$da,$tim,$conge);
	//echo $re;
	$myarr[]=$re;
	//return $re;
	}
	if($sub=='W'){
	$re= getWestPull($t,$da,$tim,$conge);
	//echo $re;
//return $re;
	$myarr[]=$re;
	}

	if($sub=='E'){
	$re= getEastPull($t,$da,$tim,$conge);
	//echo $re;
//return $re;
	$myarr[]=$re;
	}
	if($sub=='S'){
	$re= getSouthPull($t,$da,$tim,$conge);
	//echo $re;
	//return $re;
	$myarr[]=$re;
	}

return $re;

	}
	else{
	$myarr[]=$temp;
return $temp;
}
}
catch (Exception $e) {
        die("Parking not found!!!");
    }
}
function getSouthPull($node,$da,$tim,$conge){
try{
global $myarr;
$n= substr($node,0,4);
include('dbinfo.inc');
// Search the rows in the markers table
//$query = "SELECT A.`node_id`, A.`node_name`, A.`latitude`, A.`longitude`, B.`distance_miles` FROM `nodes` A, `adjacenct_nodes` B WHERE A.`node_id`IN (SELECT `adj_node_id` from `adjacenct_nodes` where `node_id`= '7010') and A.`node_id` = B.`adj_node_id`";
$query = "SELECT `Adj. Node`, `Distance` from `table 5` where `Node`= '$n'"; //distances table
$result = mysqli_query($con,$query) or die("Invalid query1: " . mysql_error());


$result1 = array();
$force= array();
 while($row = mysqli_fetch_assoc($result)) 
 {
 
 $result1[]= $row;
//$node_id =$row['node_id'];
//$node_name =$row['node_name'];
//$lat =$row['latitude'];
//$lng =$row['longitude'];
//$dist =$row['distance_miles'];
 
}
foreach ($result1 as $key => $value) { // Loops 4 times because there are 4 columns
        //echo $value['node_id'];
		//echo $value['distance_miles'];
		$block= array();
		$x= $value['Adj. Node'];
		if(substr($x,4,1)=='N')
		{
		$x='NULL';
		}
		else {
		$x= $value['Adj. Node'];}
		if(substr($x,4,1)=='N')
		{
		$z='NULL';
		}
		else {
		$z= substr($x,0,4); }
		$y= $value['Distance'];
		//echo $z;
		
     
	  $query1= "SELECT `block_id` from `geolocations` where (node1='$n' and node2= '$z') or (node1='$z' and node2= '$n')"; 
	   
	   $res= mysqli_query($con,$query1) or die("Invalid query: " . mysql_error());
	   
	while($row1 = mysqli_fetch_array($res))
 {
   $block[] = $row1;
}
if(isset($block[0]['block_id'])){
$a= $block[0]['block_id'];}
else{
$a='NULL';}

if(isset($block[1]['block_id']))
{
$b= $block[1]['block_id'];
}
else{ $b= 'NULL';}
//echo $a;
//echo $b;
//print_r($block);

$query2= "SELECT avg(`available`)*'$conge' as `available_slots` from `availability` where `block_id` IN ('$a','$b') and DATE_FORMAT(timestamp,'%H:%i:%s') Like '$tim' and DATE_FORMAT(timestamp,'%e %b %y') = '$da'";
$rest= mysqli_query($con,$query2) or die("Invalid query1: " . mysql_error());


while($row2 = mysqli_fetch_assoc($rest)) {

 $c= $row2['available_slots'];
 }
  if($c=='NULL' || $c=='' || $c<1)
{
$c=0;
}

//echo $c;


 $force[$x]= $c/ ($y*$y);



print("\r\n");
unset($block); 
	//break;   
    //} 
	
	}
//print_r($force);
arsort($force);
$temp=key($force);
//echo $temp;
if(array_values($force)[0]==0)
{
//echo $n;
$result11= array();
//return $thullu;  //////////////////////////////////////make changes here....
$query = "SELECT `Adj. Node`, `Distance` from `table 5` where `Node`= '$n'"; //distances table
$result = mysqli_query($con,$query) or die("Invalid query1: " . mysql_error());
while($row = mysqli_fetch_assoc($result)) 
 {
 $result11[]= $row;
 }
$m= array();
//print_r($result11);
foreach($result11 as $key => $value){
$j= $value['Adj. Node'];
if(substr($j,4,1)=='N'){
$m[$j]=1000;
}
else{
$m[$j]= $value['Distance'];
}
}
asort($m);
$t= key($m);
//print_r($m);
//echo $t;
$myarr[]=$t;
//return $t;
$sub=substr($t,4,1);
if($sub=='N'){
	$re= getNorthPull($t,$da,$tim,$conge);
	//echo $re;
	$myarr[]=$re;
	//return $re;
	}
	if($sub=='W'){
	$re= getWestPull($t,$da,$tim,$conge);
	//echo $re;
//return $re;
	$myarr[]=$re;

	}

	if($sub=='E'){
	$re= getEastPull($t,$da,$tim,$conge);
	//echo $re;
//return $re;
	$myarr[]=$re;
	}
	if($sub=='S'){
	$re= getSouthPull($t,$da,$tim,$conge);
	//echo $re;
	//return $re;
	$myarr[]=$re;
	}

return $re;

	}
	else{
	$myarr[]=$temp;
return $temp;
}
}
catch(Exception $e) {
        die("Parking not found!!!");
    }
}

function getEastPull($node,$da,$tim,$conge){
try{
global $myarr;
$n= substr($node,0,4);
include('dbinfo.inc');
// Search the rows in the markers table
//$query = "SELECT A.`node_id`, A.`node_name`, A.`latitude`, A.`longitude`, B.`distance_miles` FROM `nodes` A, `adjacenct_nodes` B WHERE A.`node_id`IN (SELECT `adj_node_id` from `adjacenct_nodes` where `node_id`= '7010') and A.`node_id` = B.`adj_node_id`";
$query = "SELECT `Adj. Node`, `Distance` from `table 5` where `Node`= '$n'"; //distances table
$result = mysqli_query($con,$query) or die("Invalid query1: " . mysql_error());


$result1 = array();
$force= array();
 while($row = mysqli_fetch_assoc($result)) 
 {
 
 $result1[]= $row;
//$node_id =$row['node_id'];
//$node_name =$row['node_name'];
//$lat =$row['latitude'];
//$lng =$row['longitude'];
//$dist =$row['distance_miles'];
 
}
foreach ($result1 as $key => $value) { // Loops 4 times because there are 4 columns
        //echo $value['node_id'];
		//echo $value['distance_miles'];
		$block= array();
		$x= $value['Adj. Node'];
		if(substr($x,4,1)=='W')
		{
		$x='NULL';
		}
		else {
		$x= $value['Adj. Node'];}
		if(substr($x,4,1)=='W')
		{
		$z='NULL';
		}
		else {
		$z= substr($x,0,4); }
		$y= $value['Distance'];
		//echo $z;
		
     
	  $query1= "SELECT `block_id` from `geolocations` where (node1='$n' and node2= '$z') or (node1='$z' and node2= '$n')"; 
	   
	   $res= mysqli_query($con,$query1) or die("Invalid query: " . mysql_error());
	   
	while($row1 = mysqli_fetch_array($res))
 {
   $block[] = $row1;
}
if(isset($block[0]['block_id'])){
$a= $block[0]['block_id'];}
else{
$a='NULL';}

if(isset($block[1]['block_id']))
{
$b= $block[1]['block_id'];
}
else{ $b= 'NULL';}
//echo $a;
//echo $b;
//print_r($block);

$query2= "SELECT avg(`available`)*'$conge' as `available_slots` from `availability` where `block_id` IN ('$a','$b') and DATE_FORMAT(timestamp,'%H:%i:%s') Like '$tim' and DATE_FORMAT(timestamp,'%e %b %y') = '$da'";
$rest= mysqli_query($con,$query2) or die("Invalid query1: " . mysql_error());


while($row2 = mysqli_fetch_assoc($rest)) {

 $c= $row2['available_slots'];
 }
  if($c=='NULL' || $c=='' || $c<1)
{
$c=0;
}

//echo $c;


 $force[$x]= $c/ ($y*$y);



print("\r\n");
unset($block); 
	//break;   
    //} 
	
	}
//print_r($force);
arsort($force);
$temp=key($force);
if(array_values($force)[0]==0)
{
//return $thullu;  //////////////////////////////////////make changes here....
$query = "SELECT `Adj. Node`, `Distance` from `table 5` where `Node`= '$n'"; //distances table
$result = mysqli_query($con,$query) or die("Invalid query1: " . mysql_error());
while($row = mysqli_fetch_assoc($result)) 
 {
 $result1[]= $row;
 }
$m= array();
//print_r($result1);
foreach($result1 as $key => $value){
$j= $value['Adj. Node'];
if(substr($j,4,1)=='W'){
$m[$j]=1000;
}
else{
$m[$j]= $value['Distance'];
}
}
asort($m);
$t= key($m);
//echo $t;
$myarr[]=$t;
//return $t;
$sub=substr($t,4,1);
if($sub=='N'){
	$re= getNorthPull($t,$da,$tim,$conge);
	//echo $re;
	$myarr[]=$re;
	//return $re;
	}
	if($sub=='W'){
	$re= getWestPull($t,$da,$tim,$conge);
	//echo $re;
//return $re;
	$myarr[]=$re;
	}

	if($sub=='E'){
	$re= getEastPull($t,$da,$tim,$conge);
	//echo $re;
//return $re;
	$myarr[]=$re;
	}
	if($sub=='S'){
	$re= getSouthPull($t,$da,$tim,$conge);
	//echo $re;
	//return $re;
	$myarr[]=$re;
	}

return $re;

	}
	else{
	$myarr[]=$temp;
return $temp;
}
}
catch (Exception $e) {
        die("Parking not found!!!");
    }
}
function getWestPull($node,$da,$tim,$conge){
try{
global $myarr;
$n= substr($node,0,4);
include('dbinfo.inc');
// Search the rows in the markers table
//$query = "SELECT A.`node_id`, A.`node_name`, A.`latitude`, A.`longitude`, B.`distance_miles` FROM `nodes` A, `adjacenct_nodes` B WHERE A.`node_id`IN (SELECT `adj_node_id` from `adjacenct_nodes` where `node_id`= '7010') and A.`node_id` = B.`adj_node_id`";
$query = "SELECT `Adj. Node`, `Distance` from `table 5` where `Node`= '$n'"; //distances table
$result = mysqli_query($con,$query) or die("Invalid query1: " . mysql_error());


$result1 = array();
$force= array();
 while($row = mysqli_fetch_assoc($result)) 
 {
 
 $result1[]= $row;
//$node_id =$row['node_id'];
//$node_name =$row['node_name'];
//$lat =$row['latitude'];
//$lng =$row['longitude'];
//$dist =$row['distance_miles'];
 
}
foreach ($result1 as $key => $value) { // Loops 4 times because there are 4 columns
        //echo $value['node_id'];
		//echo $value['distance_miles'];
		$block= array();
		$x= $value['Adj. Node'];
		if(substr($x,4,1)=='E')
		{
		$x='NULL';
		}
		else {
		$x= $value['Adj. Node'];}
		if(substr($x,4,1)=='E')
		{
		$z='NULL';
		}
		else {
		$z= substr($x,0,4); }
		$y= $value['Distance'];
		//echo $z;
		
     
	  $query1= "SELECT `block_id` from `geolocations` where (node1='$n' and node2= '$z') or (node1='$z' and node2= '$n')"; 
	   
	   $res= mysqli_query($con,$query1) or die("Invalid query: " . mysql_error());
	   
	while($row1 = mysqli_fetch_array($res))
 {
   $block[] = $row1;
}
if(isset($block[0]['block_id'])){
$a= $block[0]['block_id'];}
else{
$a='NULL';}

if(isset($block[1]['block_id']))
{
$b= $block[1]['block_id'];
}
else{ $b= 'NULL';}
//echo $a;
//echo $b;
//print_r($block);

$query2= "SELECT avg(`available`)*'$conge' as `available_slots` from `availability` where `block_id` IN ('$a','$b') and DATE_FORMAT(timestamp,'%H:%i:%s') Like '$tim' and DATE_FORMAT(timestamp,'%e %b %y') = '$da'";
$rest= mysqli_query($con,$query2) or die("Invalid query1: " . mysql_error());


while($row2 = mysqli_fetch_assoc($rest)) {

 $c= $row2['available_slots'];
 }
  if($c=='NULL' || $c=='' || $c<1)
{
$c=0;
}

//echo $c;


 $force[$x]= $c/ ($y*$y);



print("\r\n");
unset($block); 
	//break;   
    //} 
	
	}
//print_r($force);
arsort($force);
$temp=key($force);
if(array_values($force)[0]==0)
{
//return $thullu;  //////////////////////////////////////make changes here....
$query = "SELECT `Adj. Node`, `Distance` from `table 5` where `Node`= '$n'"; //distances table
$result = mysqli_query($con,$query) or die("Invalid query1: " . mysql_error());
while($row = mysqli_fetch_assoc($result)) 
 {
 $result1[]= $row;
 }
$m= array();
//print_r($result1);
foreach($result1 as $key => $value){
$j= $value['Adj. Node'];
if(substr($j,4,1)=='W'){
$m[$j]=1000;
}
else{
$m[$j]= $value['Distance'];}
}
asort($m);
$t= key($m);
//echo $t;
$myarr[]=$t;
//return $t;
$sub=substr($t,4,1);
if($sub=='N'){
	$re= getNorthPull($t,$da,$tim,$conge);
	//echo $re;
	$myarr[]=$re;
	//return $re;
	}
	if($sub=='W'){
	$re= getWestPull($t,$da,$tim,$conge);
	//echo $re;
//return $re;
	$myarr[]=$re;
	}

	if($sub=='E'){
	$re= getEastPull($t,$da,$tim,$conge);
	//echo $re;
//return $re;
	$myarr[]=$re;
	}
	if($sub=='S'){
	$re= getSouthPull($t,$da,$tim,$conge);
	//echo $re;
	//return $re;
	$myarr[]=$re;
	}

return $re;

	}
	else{
	$myarr[]=$temp;
return $temp;
}
} catch (Exception $e) {
        die("Parking not found!!!");
    }
}

//*************************************************************

function main($n,$d,$ti,$co){
//$node= '7026S';

//$n= substr($node,0,4);
try{
global $myarr;
include('dbinfo.inc');
// Search the rows in the markers table
//$query = "SELECT A.`node_id`, A.`node_name`, A.`latitude`, A.`longitude`, B.`distance_miles` FROM `nodes` A, `adjacenct_nodes` B WHERE A.`node_id`IN (SELECT `adj_node_id` from `adjacenct_nodes` where `node_id`= '7010') and A.`node_id` = B.`adj_node_id`";
$query = "SELECT `Adj. Node`, `Distance` from `table 5` where `Node`= '$n'"; //distances table
$result = mysqli_query($con,$query) or die("Invalid query1: " . mysql_error());


$result1 = array();
$force= array();
 while($row = mysqli_fetch_assoc($result)) 
 {
 
 $result1[]= $row;
//$node_id =$row['node_id'];
//$node_name =$row['node_name'];
//$lat =$row['latitude'];
//$lng =$row['longitude'];
//$dist =$row['distance_miles'];
 
}
foreach ($result1 as $key => $value) { // Loops 4 times because there are 4 columns
        //echo $value['node_id'];
		//echo $value['distance_miles'];
		$block= array();
		//$v= $value['Adj. Node'];
		$x= $value['Adj. Node'];
		
		if(substr($x,4,1)=='W')
		{
		$x='NULL';
		}
		else {
		$x= $value['Adj. Node'];}
		if(substr($x,4,1)=='W')
		{
		$z='NULL';
		}
		else {
		$z= substr($x,0,4); }
		$y= $value['Distance'];
		//echo $z;
		
    
	  $query1= "SELECT `block_id` from `geolocations` where (node1='$n' and node2= '$z') or (node1='$z' and node2= '$n')"; 
	   
	   $res= mysqli_query($con,$query1) or die("Invalid query: " . mysql_error());
	   
	while($row1 = mysqli_fetch_array($res))
 {
   $block[] = $row1;
}
if(isset($block[0]['block_id'])){
$a= $block[0]['block_id'];}
else{
$a='NULL';}

if(isset($block[1]['block_id']))
{
$b= $block[1]['block_id'];
}
else{ $b= 'NULL';}
//echo $a;
//echo $b;
//print_r($block);

//if(isset($a) && isset($b)){
$query2= "SELECT avg(`available`)*'$co' as `available_slots` from `availability` where `block_id` IN ('$a','$b') and DATE_FORMAT(timestamp,'%H:%i:%s') Like '$ti' and DATE_FORMAT(timestamp,'%e %b %y') = '$d'";

$rest= mysqli_query($con,$query2) or die("Invalid query1: " . mysql_error());




while($row2 = mysqli_fetch_assoc($rest)) {

 $c= $row2['available_slots'];
 }
 
 
  if($c=='NULL' || $c=='' || $c<1)
{
$c=0;
}

//echo $c;
//else{

$force[$x]= $c/ ($y*$y);
 //}


print("\r\n");
unset($block); 
	//break;   
    //} 
	
	}
//print_r($force);
arsort($force);
$temp=key($force);
//echo $temp;
//$thullu=0;
if(array_values($force)[0]==0)
{
//echo $n;
//return $thullu;  //////////////////////////////////////make changes here....
$query = "SELECT `Adj. Node`, `Distance` from `table 5` where `Node`= '$n'"; //distances table
$result = mysqli_query($con,$query) or die("Invalid query1: " . mysql_error());
while($row = mysqli_fetch_assoc($result)) 
 {
 $result1[]= $row;
 }
$m= array();
//print_r($result1);
foreach($result1 as $key => $value){
$j= $value['Adj. Node'];
$m[$j]= $value['Distance'];
}
asort($m);
$t= key($m);
//echo $t;
$myarr[]=$t;
$cont=substr($t,4,1);
if($cont=='N'){
	$re= getNorthPull($t,$d,$ti,$co);
	//echo $re;
	$myarr[]=$re;
	//return $re;
	}
	if($cont=='W'){
	$re= getWestPull($t,$d,$ti,$co);
	//echo $re;
//return $re;
	$myarr[]=$re;
	}

	if($cont=='E'){
	$re= getEastPull($t,$d,$ti,$co);
	//echo $re;
//return $re;
	$myarr[]=$re;
	}
	if($cont=='S'){
	$re= getSouthPull($t,$d,$ti,$co);
	//echo $re;
	//return $re;
	$myarr[]=$re;
	}

return $re;
}
else{
$myarr[]=$temp;
return $temp;
}
}
catch(Exception $e) {
        die("Parking not found!!!");
    }
}
if(isset($_POST['save']))
{ 

$_node= $_POST['node'];
$_date= $_POST['date'];
$_time= $_POST['time'];
$_cong= $_POST['cong'];
$myarr[]=$_node;
if($_cong=='0%'){
$_co= 1;
}
else if($_cong=='30%'){
$_co= 0.7;
}
else if($_cong=='50%'){
$_co= 0.5;
}
else if($_cong=='70%'){
$_co= 0.3;
}
$arr= array();

$arr= explode(":",$_time);
$_ti= $arr[0].'%';

$cc= main($_node,$_date,$_ti,$_co);
//echo $cc;
$newarr= array();
$newarr= array_unique($myarr);
//print_r($newarr);
$finarr=array();
foreach($newarr as $s){
$fin= substr($s,0,4);
$finarr[]=$fin;
}
//print_r($finarr);////////////////////////////final array with nodes!!!!!!
$len =count($finarr);
  $map = new Map();
  
  $map->setPrefixJavascriptVariable('map_');
  $map->setHtmlContainerId('map_canvas');
  
  $map->setAsync(false);
  $map->setAutoZoom(false);
  
  $map->setCenter(37.806381, -122.415468, true);
  $map->setMapOption('zoom', 16);
  
  $map->setBound(-2.1, -3.9, 2.6, 1.4, true, true);
  
  $map->setMapOption('mapTypeId', MapTypeId::ROADMAP);
  $map->setMapOption('mapTypeId', 'roadmap');
  
  $map->setMapOption('disableDefaultUI', true);
  $map->setMapOption('disableDoubleClickZoom', true);
  $map->setMapOptions(array(
      'disableDefaultUI'       => true,
      'disableDoubleClickZoom' => true,
  ));
  
  $map->setStylesheetOption('width', '600px');
  $map->setStylesheetOption('height', '600px');
  $map->setStylesheetOptions(array(
      'width'  => '1000px',
      'height' => '800px',
  ));
  
  $map->setLanguage('en');
for($i=0;$i<$len-1;$i++){
	//echo $finarr[$i];
	if($i==0){
  $org = array();
  $org = getCoord($finarr[$i]);
  //echo $org[0]['latitude'];
  //echo $org[0]['longitude'];
  $des= array();
  $des= getCoord($finarr[$i+1]);
  //echo $des[0]['latitude'];
  //echo $des[0]['longitude'];
 
//creating marker
	$marker = new Marker();
   $marker->setPrefixJavascriptVariable('marker_');
  $marker->setPosition($org[0]['latitude'],$org[0]['longitude'], true);
  $marker->setAnimation(Animation::DROP);
  
  $marker->setOption('clickable', true);
  $marker->setOption('flat', true);
  $marker->setOptions(array(
      'clickable' => true,
      'flat'      => true,
  ));
  $map->addMarker($marker);
 $marker2 = new Marker();
  $marker2->setPrefixJavascriptVariable('marker_two');
  $marker2->setPosition($des[0]['latitude'],$des[0]['longitude'], true);
  $marker2->setAnimation(Animation::DROP);
  
  $marker2->setOption('clickable', true);
  $marker2->setOption('flat', true);
  $marker2->setOptions(array(
      'clickable' => true,
      'flat'      => true,
  ));
  $map->addMarker($marker2);
  // trying directions
  $directions = new Directions(new CurlHttpAdapter());
  $request = new DirectionsRequest();
  // Set your origin
  $request->setOrigin($marker->getPosition(), true);
  
  // Set your destination
  $request->setDestination($marker2->getPosition(), true);
  $response = $directions->route($request);
  // Get the routes
  $routes = $response->getRoutes();
  // Iterate each routes
  foreach ($routes as $route) {
  // Get the bound
  $bound = $route->getBound();
  // Get the copyrights
  $copyrights = $route->getCopyrights();
  // Get the legs
  $legs = $route->getLegs();
  
  // Get the summary
  //$summary = $route->getSummary();
  //print_r ($summary);
  foreach ($legs as $leg) {
  // Gets the distance
  $distance = $leg->getDistance();
  //$dist = (String)$distance;
  print_r ($distance); 
  // Gets the duration
  $duration = $leg->getDuration();
  print_r ($duration);
  //summary
  //$summary = $leg->getSummary();
  //print_r ($summary);
  // Gets the directions steps.
  $steps = $leg->getSteps();
  // Iterate each step
  foreach ($steps as $step) {
  // Gets the encoded polyline.
  $encodedPolyline = $step->getEncodedPolyline();
  $map->addEncodedPolyline($encodedPolyline);
  }
  }
  }// end of 1st iteration. found time and distance

  //getMarker($org[0]['latitude'],$org[0]['longitude'],$des[0]['latitude'],$des[0]['longitude']);
  }
  if($i==1){
  $org = array();
  $org = getCoord($finarr[$i]);
  //echo $org[0]['latitude'];
  //echo $org[0]['longitude'];
  $des= array();
  $des= getCoord($finarr[$i+1]);
  //echo $des[0]['latitude'];
  //echo $des[0]['longitude'];
  $marker3 = new Marker();
   $marker3->setPrefixJavascriptVariable('marker_');
  $marker3->setPosition($org[0]['latitude'],$org[0]['longitude'], true);
  $marker3->setAnimation(Animation::DROP);
  
  $marker3->setOption('clickable', true);
  $marker3->setOption('flat', true);
  $marker3->setOptions(array(
      'clickable' => true,
      'flat'      => true,
  ));
  $map->addMarker($marker);
 $marker4 = new Marker();
  $marker4->setPrefixJavascriptVariable('marker_two');
  $marker4->setPosition($des[0]['latitude'],$des[0]['longitude'], true);
  $marker4->setAnimation(Animation::DROP);
  
  $marker4->setOption('clickable', true);
  $marker4->setOption('flat', true);
  $marker4->setOptions(array(
      'clickable' => true,
      'flat'      => true,
  ));
  $map->addMarker($marker4);
  // trying directions
  $directions = new Directions(new CurlHttpAdapter());
  $request = new DirectionsRequest();
  // Set your origin
  $request->setOrigin($marker3->getPosition(), true);
  
  // Set your destination
  $request->setDestination($marker4->getPosition(), true);
  $response = $directions->route($request);
  // Get the routes
  $routes = $response->getRoutes();
  // Iterate each routes
  foreach ($routes as $route) {
  // Get the bound
  $bound = $route->getBound();
  // Get the copyrights
  $copyrights = $route->getCopyrights();
  // Get the legs
  $legs = $route->getLegs();
  
  // Get the summary
  //$summary = $route->getSummary();
  //print_r ($summary);
  foreach ($legs as $leg) {
  // Gets the distance
  $distance = $leg->getDistance();
  //$dist = (String)$distance;
  print_r ($distance); 
  // Gets the duration
  $duration = $leg->getDuration();
  print_r ($duration);
  //summary
  //$summary = $leg->getSummary();
  //print_r ($summary);
  // Gets the directions steps.
  $steps = $leg->getSteps();
  // Iterate each step
  foreach ($steps as $step) {
  // Gets the encoded polyline.
  $encodedPolyline = $step->getEncodedPolyline();
  $map->addEncodedPolyline($encodedPolyline);
  
  }
  }
  }// end of 1st iteration. found time and distance
  //getMarker2($org[0]['latitude'],$org[0]['longitude'],$des[0]['latitude'],$des[0]['longitude']);
  }

	//producemarker($finarr[$i], $finarr[$i+1])
	if($i==2){
  $org = array();
  $org = getCoord($finarr[$i]);
  //echo $org[0]['latitude'];
  //echo $org[0]['longitude'];
  $des= array();
  $des= getCoord($finarr[$i+1]);
  //echo $des[0]['latitude'];
  //echo $des[0]['longitude'];
  $marker3 = new Marker();
   $marker3->setPrefixJavascriptVariable('marker_');
  $marker3->setPosition($org[0]['latitude'],$org[0]['longitude'], true);
  $marker3->setAnimation(Animation::DROP);
  
  $marker3->setOption('clickable', true);
  $marker3->setOption('flat', true);
  $marker3->setOptions(array(
      'clickable' => true,
      'flat'      => true,
  ));
  $map->addMarker($marker);
 $marker4 = new Marker();
  $marker4->setPrefixJavascriptVariable('marker_two');
  $marker4->setPosition($des[0]['latitude'],$des[0]['longitude'], true);
  $marker4->setAnimation(Animation::DROP);
  
  $marker4->setOption('clickable', true);
  $marker4->setOption('flat', true);
  $marker4->setOptions(array(
      'clickable' => true,
      'flat'      => true,
  ));
  $map->addMarker($marker4);
  // trying directions
  $directions = new Directions(new CurlHttpAdapter());
  $request = new DirectionsRequest();
  // Set your origin
  $request->setOrigin($marker3->getPosition(), true);
  // Set your destination
  $request->setDestination($marker4->getPosition(), true);
  $response = $directions->route($request);
  // Get the routes
  $routes = $response->getRoutes();
  // Iterate each routes
  foreach ($routes as $route) {
  // Get the bound
  $bound = $route->getBound();
  // Get the copyrights
  $copyrights = $route->getCopyrights();
  // Get the legs
  $legs = $route->getLegs();
  // Get the summary
  foreach ($legs as $leg) {
  // Gets the distance
  $distance = $leg->getDistance();
  //$dist = (String)$distance;
  print_r ($distance); 
  // Gets the duration
  $duration = $leg->getDuration();
  print_r ($duration);
  //summary
  //$summary = $leg->getSummary();
  //print_r ($summary);
  // Gets the directions steps.
  $steps = $leg->getSteps();
  // Iterate each step
  foreach ($steps as $step) {
  // Gets the encoded polyline.
  $encodedPolyline = $step->getEncodedPolyline();
  $map->addEncodedPolyline($encodedPolyline);
  
  }
  }
  }// end of 1st iteration. found time and distance
  }//end of 3rd if
  if($i==3){
  $org = array();
  $org = getCoord($finarr[$i]);
  //echo $org[0]['latitude'];
  //echo $org[0]['longitude'];
  $des= array();
  $des= getCoord($finarr[$i+1]);
  //echo $des[0]['latitude'];
  //echo $des[0]['longitude'];
  $marker3 = new Marker();
   $marker3->setPrefixJavascriptVariable('marker_');
  $marker3->setPosition($org[0]['latitude'],$org[0]['longitude'], true);
  $marker3->setAnimation(Animation::DROP);
  
  $marker3->setOption('clickable', true);
  $marker3->setOption('flat', true);
  $marker3->setOptions(array(
      'clickable' => true,
      'flat'      => true,
  ));
  $map->addMarker($marker);
 $marker4 = new Marker();
  $marker4->setPrefixJavascriptVariable('marker_two');
  $marker4->setPosition($des[0]['latitude'],$des[0]['longitude'], true);
  $marker4->setAnimation(Animation::DROP);
  
  $marker4->setOption('clickable', true);
  $marker4->setOption('flat', true);
  $marker4->setOptions(array(
      'clickable' => true,
      'flat'      => true,
  ));
  $map->addMarker($marker4);
  // trying directions
  $directions = new Directions(new CurlHttpAdapter());
  $request = new DirectionsRequest();
  // Set your origin
  $request->setOrigin($marker3->getPosition(), true);
  // Set your destination
  $request->setDestination($marker4->getPosition(), true);
  $response = $directions->route($request);
  // Get the routes
  $routes = $response->getRoutes();
  // Iterate each routes
  foreach ($routes as $route) {
  // Get the bound
  $bound = $route->getBound();
  // Get the copyrights
  $copyrights = $route->getCopyrights();
  // Get the legs
  $legs = $route->getLegs();
  // Get the summary
  foreach ($legs as $leg) {
  // Gets the distance
  $distance = $leg->getDistance();
  //$dist = (String)$distance;
  print_r ($distance); 
  // Gets the duration
  $duration = $leg->getDuration();
  print_r ($duration);
  //summary
  //$summary = $leg->getSummary();
  //print_r ($summary);
  // Gets the directions steps.
  $steps = $leg->getSteps();
  // Iterate each step
  foreach ($steps as $step) {
  // Gets the encoded polyline.
  $encodedPolyline = $step->getEncodedPolyline();
  $map->addEncodedPolyline($encodedPolyline);
  
  }
  }
  }// end of 1st iteration. found time and distance
  }//end of 4th if
  if($i==4){
  $org = array();
  $org = getCoord($finarr[$i]);
  //echo $org[0]['latitude'];
  //echo $org[0]['longitude'];
  $des= array();
  $des= getCoord($finarr[$i+1]);
  //echo $des[0]['latitude'];
  //echo $des[0]['longitude'];
  $marker3 = new Marker();
   $marker3->setPrefixJavascriptVariable('marker_');
  $marker3->setPosition($org[0]['latitude'],$org[0]['longitude'], true);
  $marker3->setAnimation(Animation::DROP);
  
  $marker3->setOption('clickable', true);
  $marker3->setOption('flat', true);
  $marker3->setOptions(array(
      'clickable' => true,
      'flat'      => true,
  ));
  $map->addMarker($marker);
 $marker4 = new Marker();
  $marker4->setPrefixJavascriptVariable('marker_two');
  $marker4->setPosition($des[0]['latitude'],$des[0]['longitude'], true);
  $marker4->setAnimation(Animation::DROP);
  
  $marker4->setOption('clickable', true);
  $marker4->setOption('flat', true);
  $marker4->setOptions(array(
      'clickable' => true,
      'flat'      => true,
  ));
  $map->addMarker($marker4);
  // trying directions
  $directions = new Directions(new CurlHttpAdapter());
  $request = new DirectionsRequest();
  // Set your origin
  $request->setOrigin($marker3->getPosition(), true);
  // Set your destination
  $request->setDestination($marker4->getPosition(), true);
  $response = $directions->route($request);
  // Get the routes
  $routes = $response->getRoutes();
  // Iterate each routes
  foreach ($routes as $route) {
  // Get the bound
  $bound = $route->getBound();
  // Get the copyrights
  $copyrights = $route->getCopyrights();
  // Get the legs
  $legs = $route->getLegs();
  // Get the summary
  foreach ($legs as $leg) {
  // Gets the distance
  $distance = $leg->getDistance();
  //$dist = (String)$distance;
  print_r ($distance); 
  // Gets the duration
  $duration = $leg->getDuration();
  print_r ($duration);
  //summary
  //$summary = $leg->getSummary();
  //print_r ($summary);
  // Gets the directions steps.
  $steps = $leg->getSteps();
  // Iterate each step
  foreach ($steps as $step) {
  // Gets the encoded polyline.
  $encodedPolyline = $step->getEncodedPolyline();
  $map->addEncodedPolyline($encodedPolyline);
  
  }
  }
  }// end of 1st iteration. found time and distance
  }
  if($i==5){
  $org = array();
  $org = getCoord($finarr[$i]);
  //echo $org[0]['latitude'];
  //echo $org[0]['longitude'];
  $des= array();
  $des= getCoord($finarr[$i+1]);
  //echo $des[0]['latitude'];
  //echo $des[0]['longitude'];
  $marker3 = new Marker();
   $marker3->setPrefixJavascriptVariable('marker_');
  $marker3->setPosition($org[0]['latitude'],$org[0]['longitude'], true);
  $marker3->setAnimation(Animation::DROP);
  
  $marker3->setOption('clickable', true);
  $marker3->setOption('flat', true);
  $marker3->setOptions(array(
      'clickable' => true,
      'flat'      => true,
  ));
  $map->addMarker($marker);
 $marker4 = new Marker();
  $marker4->setPrefixJavascriptVariable('marker_two');
  $marker4->setPosition($des[0]['latitude'],$des[0]['longitude'], true);
  $marker4->setAnimation(Animation::DROP);
  
  $marker4->setOption('clickable', true);
  $marker4->setOption('flat', true);
  $marker4->setOptions(array(
      'clickable' => true,
      'flat'      => true,
  ));
  $map->addMarker($marker4);
  // trying directions
  $directions = new Directions(new CurlHttpAdapter());
  $request = new DirectionsRequest();
  // Set your origin
  $request->setOrigin($marker3->getPosition(), true);
  // Set your destination
  $request->setDestination($marker4->getPosition(), true);
  $response = $directions->route($request);
  // Get the routes
  $routes = $response->getRoutes();
  // Iterate each routes
  foreach ($routes as $route) {
  // Get the bound
  $bound = $route->getBound();
  // Get the copyrights
  $copyrights = $route->getCopyrights();
  // Get the legs
  $legs = $route->getLegs();
  // Get the summary
  foreach ($legs as $leg) {
  // Gets the distance
  $distance = $leg->getDistance();
  //$dist = (String)$distance;
  print_r ($distance); 
  // Gets the duration
  $duration = $leg->getDuration();
  print_r ($duration);
  //summary
  //$summary = $leg->getSummary();
  //print_r ($summary);
  // Gets the directions steps.
  $steps = $leg->getSteps();
  // Iterate each step
  foreach ($steps as $step) {
  // Gets the encoded polyline.
  $encodedPolyline = $step->getEncodedPolyline();
  $map->addEncodedPolyline($encodedPolyline);
  
  }
  }
  }// end of 1st iteration. found time and distance
  }
  if($i==6){
  $org = array();
  $org = getCoord($finarr[$i]);
  //echo $org[0]['latitude'];
  //echo $org[0]['longitude'];
  $des= array();
  $des= getCoord($finarr[$i+1]);
  //echo $des[0]['latitude'];
  //echo $des[0]['longitude'];
  $marker3 = new Marker();
   $marker3->setPrefixJavascriptVariable('marker_');
  $marker3->setPosition($org[0]['latitude'],$org[0]['longitude'], true);
  $marker3->setAnimation(Animation::DROP);
  
  $marker3->setOption('clickable', true);
  $marker3->setOption('flat', true);
  $marker3->setOptions(array(
      'clickable' => true,
      'flat'      => true,
  ));
  $map->addMarker($marker);
 $marker4 = new Marker();
  $marker4->setPrefixJavascriptVariable('marker_two');
  $marker4->setPosition($des[0]['latitude'],$des[0]['longitude'], true);
  $marker4->setAnimation(Animation::DROP);
  
  $marker4->setOption('clickable', true);
  $marker4->setOption('flat', true);
  $marker4->setOptions(array(
      'clickable' => true,
      'flat'      => true,
  ));
  $map->addMarker($marker4);
  // trying directions
  $directions = new Directions(new CurlHttpAdapter());
  $request = new DirectionsRequest();
  // Set your origin
  $request->setOrigin($marker3->getPosition(), true);
  // Set your destination
  $request->setDestination($marker4->getPosition(), true);
  $response = $directions->route($request);
  // Get the routes
  $routes = $response->getRoutes();
  // Iterate each routes
  foreach ($routes as $route) {
  // Get the bound
  $bound = $route->getBound();
  // Get the copyrights
  $copyrights = $route->getCopyrights();
  // Get the legs
  $legs = $route->getLegs();
  // Get the summary
  foreach ($legs as $leg) {
  // Gets the distance
  $distance = $leg->getDistance();
  //$dist = (String)$distance;
  print_r ($distance); 
  // Gets the duration
  $duration = $leg->getDuration();
  print_r ($duration);
  //summary
  //$summary = $leg->getSummary();
  //print_r ($summary);
  // Gets the directions steps.
  $steps = $leg->getSteps();
  // Iterate each step
  foreach ($steps as $step) {
  // Gets the encoded polyline.
  $encodedPolyline = $step->getEncodedPolyline();
  $map->addEncodedPolyline($encodedPolyline);
  
  }
  }
  }// end of 1st iteration. found time and distance
  }
  if($i==7){
  $org = array();
  $org = getCoord($finarr[$i]);
  //echo $org[0]['latitude'];
  //echo $org[0]['longitude'];
  $des= array();
  $des= getCoord($finarr[$i+1]);
  //echo $des[0]['latitude'];
  //echo $des[0]['longitude'];
  $marker3 = new Marker();
   $marker3->setPrefixJavascriptVariable('marker_');
  $marker3->setPosition($org[0]['latitude'],$org[0]['longitude'], true);
  $marker3->setAnimation(Animation::DROP);
  
  $marker3->setOption('clickable', true);
  $marker3->setOption('flat', true);
  $marker3->setOptions(array(
      'clickable' => true,
      'flat'      => true,
  ));
  $map->addMarker($marker);
 $marker4 = new Marker();
  $marker4->setPrefixJavascriptVariable('marker_two');
  $marker4->setPosition($des[0]['latitude'],$des[0]['longitude'], true);
  $marker4->setAnimation(Animation::DROP);
  
  $marker4->setOption('clickable', true);
  $marker4->setOption('flat', true);
  $marker4->setOptions(array(
      'clickable' => true,
      'flat'      => true,
  ));
  $map->addMarker($marker4);
  // trying directions
  $directions = new Directions(new CurlHttpAdapter());
  $request = new DirectionsRequest();
  // Set your origin
  $request->setOrigin($marker3->getPosition(), true);
  // Set your destination
  $request->setDestination($marker4->getPosition(), true);
  $response = $directions->route($request);
  // Get the routes
  $routes = $response->getRoutes();
  // Iterate each routes
  foreach ($routes as $route) {
  // Get the bound
  $bound = $route->getBound();
  // Get the copyrights
  $copyrights = $route->getCopyrights();
  // Get the legs
  $legs = $route->getLegs();
  // Get the summary
  foreach ($legs as $leg) {
  // Gets the distance
  $distance = $leg->getDistance();
  //$dist = (String)$distance;
  print_r ($distance); 
  // Gets the duration
  $duration = $leg->getDuration();
  print_r ($duration);
  //summary
  //$summary = $leg->getSummary();
  //print_r ($summary);
  // Gets the directions steps.
  $steps = $leg->getSteps();
  // Iterate each step
  foreach ($steps as $step) {
  // Gets the encoded polyline.
  $encodedPolyline = $step->getEncodedPolyline();
  $map->addEncodedPolyline($encodedPolyline);
  
  }
  }
  }// end of 1st iteration. found time and distance
  }
}//end of for
$mapHelper = new MapHelper();
  echo $mapHelper->renderHtmlContainer($map);
  echo $mapHelper->renderJavascripts($map);
}// submit close
function getCoord($node){
include('dbinfo.inc');
$rest= array();
 $query3= "SELECT `latitude`, `longitude` from `nodes` where `node_id`='$node'"; 	   
	   $res3= mysqli_query($con,$query3) or die("Invalid query: " . mysql_error());
	   while($row = mysqli_fetch_assoc($res3)) 
		{
			$rest[]= $row;
		}	
return $rest;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Deterministic Gravitational Pull</title>
<script type="text/javascript">

function validator()
{
	
	
 var a=document.getElementById("node").value;
 var b=document.getElementById("cong").value;
 var c=document.getElementById("date").value;
 var d=document.getElementById("time").value;
 
 
if (a=="" || a=="null")
  {
  alert("Please specify the node.");
  return false;
  } 
if (c=="" || c=="null")
  {
  alert("Please specify the date.");
  return false;
  }
if (d=="" || d=="null")
  {
  alert("Please specify the time.");
  return false;
  }
if (b=="" || b=="null")
  {
  alert("Please specify the congestion level.");
  return false;
  }
  
}
</script>
</head>

<body>
<dd><form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" onSubmit=" return validator();">
<h1 align="center">Deterministic Gravitational Pull</h1>
<h2>Please enter the following information:</h2>
 <dd><table width="100%" border="0">
  <tr>
    <td width="145" align="left" ><b> Starting Node:</b></td>
    <td width="722"><select name="node" id="node">
      <option value="null" selected="selected"></option>
	  <option>7002</option>
      <option>7003</option>
      <option>7004</option>
	   <option>7005</option>
	    <option>7006</option>
		 <option>7007</option>
		  <option>7008</option>
		   <option>7009</option>
		    <option>7010</option>
			 <option>7011</option>
			  <option>7012</option>
			   <option>7013</option>
			    <option>7014</option>
				 <option>7015</option>
				  <option>7016</option>
				   <option>7017</option>
				    <option>7018</option>
					 <option>7019</option>
					  <option>7020</option>
					   <option>7021</option>
					    <option>7022</option>
						 <option>7023</option>
						  <option>7024</option>
						   <option>7025</option>
						    <option>7026</option>
							 <option>7027</option>
							  <option>7028</option>
							   <option>7029</option>
							    <option>7030</option>
								 <option>7031</option>
								  <option>7032</option>
								   <option>7033</option>
								    <option>7034</option>
									 <option>7035</option>
									  <option>7036</option>
									   <option>7037</option>
									    <option>70238</option>
										 <option>7039</option>
										  <option>7040</option>
    </select></td>
  </tr>
  <tr>
    <td align="left" ><b>Date: </b></td>
    <td><select name="date" id="date">
      <option value="null" selected="selected"></option>
	  <option>6 Apr 12</option>
	    <option>7 Apr 12</option>
		  <option>8 Apr 12</option>
		    <option>9 Apr 12</option>
			  <option>10 Apr 12</option>
			    <option>12 Apr 12</option>
				  <option>13 Apr 12</option>
				  
      <option>14 Apr 12</option>
      <option>17 Apr 12</option>
	  <option>21 Apr 12</option>
	  <option>25 Apr 12</option>
    </select></td>
  </tr>
  <tr>
    <td align="left" ><b>Time:</b></td>
    <td><select name="time" id="time">
      <option value="null" selected="selected"></option>
	  <option>00:00:00</option>
	    <option>01:25:25</option> 
		 <option>02:25:25</option>
		  <option>03:25:25</option>
		   <option>04:25:25</option>
		    <option>05:25:25</option>
			 <option>06:25:25</option>
			  <option>07:25:25</option>
			   <option>08:25:25</option>
			    <option>09:25:25</option>
				 <option>10:25:25</option>
				  <option>11:25:25</option>
				   <option>12:25:25</option>
				    <option>13:25:25</option>
					 <option>14:25:25</option> 
					  <option>15:25:25</option>
					   <option>16:25:25</option>
					    <option>17:25:25</option>
						 <option>18:25:25</option>
						  <option>19:25:25</option>
						   <option>20:25:25</option>
	  <option>21:25:25</option>
      <option>22:30:05</option>
      <option>23:55:00</option>
    </select></td>
  </tr>
    <tr>
    <td align="left" ><b>Congestion Level:</b></td>
    <td><select name="cong" id="cong">
      <option value="null" selected="selected"></option>
	  <option>0%</option>
      <option>30%</option>
      <option>50%</option>
	  <option>70%</option>
    </select></td>
  </tr>
  </table></dd>
      <p>&nbsp;</p>  
 <p align="center">
  <input type="submit" value="Simulate" name="save" style="height:30px; width:150px; font:bold; font-size:20px"/>
</p> 
</form><dd>
</body>
</html>