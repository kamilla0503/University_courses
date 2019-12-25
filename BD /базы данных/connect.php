<?php
session_start();
header("Content-type: text/html; charset=utf-8");
$servername = "localhost";
$username = "root";
$password = "";
$dbname=$_SESSION["dbname"];
// Create connection
$conn = new mysqli($servername, $username, $password,$dbname);
mysqli_query($conn,"SET NAMES utf8");


$table_name='j';
	$id='J1';
	$ar=array();
	$name='number_of_item';
	$sql = "SELECT * FROM ".$table_name." WHERE ".$name."='".$id."'";
	$result = $conn->query($sql);
	
	while($row = mysqli_fetch_array($result,MYSQLI_NUM)){
		$ar=$row;
	}
	echo implode($ar);
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."'";
	$result = $conn->query($sql);
	$str= '';
	while($row = mysqli_fetch_array($result)){
		//$str .='<input type="text"  id="'.$row[0].'"   name="'.$row[0].'" placeholder="введите '.$row[0].' " ></input></br>';
		$str .='<div class="form-group"><label for="'.$row[0].'">'.$row[0].'</label>
		<input type="text" class="form-control"  id="'.$row[0].'"   name="'.$row[0].'" placeholder="введите '.$row[0].'" value="'.array_shift($ar).'"">
		<small id="emailHelp" class="form-text text-muted"></small></div>';
	}
	$str .= '<input type="button" class="btn btn-primary" id="script" name="submit" value="submit", onclick="addRow(`'.$table_name.'`)">';

echo $str;

$conn->close();
?>