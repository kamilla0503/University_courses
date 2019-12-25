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

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
	echo 'error';
} 

//table name
function print_table($conn){
	$sort=$_POST['ind'];
	$sort_field_name=$_POST['name'];
	$table_name=$_POST['table_name'];
	echo "<table class='table'>";
	
	//header th mul fields
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."' AND COLUMN_KEY='MUL'";
	
	$result = $conn->query($sql);
	while($row = mysqli_fetch_array($result)){
		echo "
		<th scope='col' style='text-align: center;'>
			<div class='btn-group' role='group'>
				<button id='".$row[0]."' type='button' class='btn btn-secondary dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>".$row[0]."</button>
				<div class='dropdown-menu' aria-labelledby='".$row[0]."'>
				  <a class='dropdown-item' style='text-align:center;' href='#' onclick='printTable(\"".$table_name."\",1,\"".$row[0]."\")'>sort up</a>
				  <a class='dropdown-item' style='text-align:center;' href='#' onclick='printTable(\"".$table_name."\",-1,\"".$row[0]."\")'>sort down</a>
				</div>
			</div></th>";
	}
	
	//header th single fields
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."' AND NOT(COLUMN_KEY='PRI') AND NOT(COLUMN_KEY='MUL')";
	
	$result = $conn->query($sql);
	while($row = mysqli_fetch_array($result)){
		
		echo "
		<th scope='col' style='text-align: center;'>
			<div class='btn-group' role='group'>
				<button id='".$row[0]."' type='button' class='btn btn-secondary dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>".$row[0]."</button>
				<div class='dropdown-menu' aria-labelledby='".$row[0]."'>
				  <a class='dropdown-item' style='text-align:center;' href='#' onclick='printTable(\"".$table_name."\",1,\"".$row[0]."\")'>sort up</a>
				  <a class='dropdown-item' style='text-align:center;' href='#' onclick='printTable(\"".$table_name."\",-1,\"".$row[0]."\")'>sort down</a>
				</div>
			</div></th>";
	}
	
	echo '<th scope="col" style="text-align: center; "><button class="btn btn-secondary"  data-toggle="modal" data-target=".bs-example-modal-lg" ><b>+</b></button></th>';

	if($sort==0){
		$sql = "SELECT * FROM ".$table_name."";
	}
	if($sort==1){
		$sql = "SELECT * FROM ".$table_name." ORDER BY ".$sort_field_name."";
	}
	if($sort==-1){
		$sql = "SELECT * FROM ".$table_name." ORDER BY ".$sort_field_name." DESC";
	}
	

	$result = $conn->query($sql);
	if ($result->num_rows > 0){
		while($row = mysqli_fetch_array($result)) {
			echo '<tr>';
			
			echo name_for_mul_field($conn,$row[0]);
			
			echo name_for_single_filed($conn,$row[0]);
			
			
			/*for($i = 1; $i < count($row)/2; $i++){
				echo "<td>".$row[$i]."</td>";
			}
			//echo '<td>'.$row[0].'</td></tr>';*/
			echo '<td><button class="btn btn-secondary"  data-toggle="modal" data-target=".modal_update" onClick="index_del_up(`'.$row[0].'`,`'.$table_name.'`)">&hellip;</button></td></tr>';
		}
	} else {
		echo "0 results";
	}
	return '';
}

function name_for_mul_field($conn,$id){
	$table_name=$_POST['table_name'];
	//id 
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."'  AND COLUMN_KEY='PRI'";
	$result = $conn->query($sql);
	while($row = mysqli_fetch_array($result)){
		$id_table=$row[0];
	}
	
	
	//mul fields
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."' AND COLUMN_KEY='MUL'";
	$result = $conn->query($sql);
	$str='';
	if ($result->num_rows > 0){
		while($row = mysqli_fetch_array($result)){
			//find id filed in another table
			$sql="SELECT ".$row[0]." FROM ".$table_name." WHERE ".$id_table."=".$id."";
			$res_id = $conn->query($sql);
			while($row_id = mysqli_fetch_array($res_id,MYSQLI_NUM)){
				$id_field = $row_id[0];
			}	
			
			
			$mul_table_name=substr($row[0], 3);
			$select_sql="SELECT * FROM ".$mul_table_name." WHERE ".$row[0]."=".$id_field."";
			
			$select_res = $conn->query($select_sql);
			//return $select_res;
			while($row_td = mysqli_fetch_array($select_res,MYSQLI_NUM)){
				$str .= "<td>".$row_td[1]."</td>";
			}	
		}
	}
	return $str;
}

function name_for_single_filed($conn,$id){
	$table_name=$_POST['table_name'];
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."'  AND NOT(COLUMN_KEY='PRI')  AND NOT(COLUMN_KEY='MUL')";
	$result = $conn->query($sql);
	$select_sql="SELECT ";
	$str='';
	if ($result->num_rows > 0){
		while($row = mysqli_fetch_array($result)){
			$select_sql .= $row[0].", ";
		}
		$select_sql = substr($select_sql,0,-2);
		
		$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."'  AND COLUMN_KEY='PRI'";
		$result = $conn->query($sql);
		while($row = mysqli_fetch_array($result)){
			$select_sql .=" FROM ".$table_name." WHERE ".$row[0]."=".$id."";
		}
		
		$result = $conn->query($select_sql);
		while($row = mysqli_fetch_array($result,MYSQLI_NUM)){
			for($i = 0; $i < count($row); $i++){
				$str .="<td>".$row[$i]."</td>";
			}
		}
	}
	return $str;
	
}



/*function form($conn){
	$table_name=$_POST['table_name'];
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."'";
	$result = $conn->query($sql);
	$str= '<form id="form">';
	while($row = mysqli_fetch_array($result)){

		
		//$str .='<input type="text"  id="'.$row[0].'"   name="'.$row[0].'" placeholder="введите '.$row[0].' " ></input></br>';
		$str .='<div class="form-group"><label for="'.$row[0].'">'.$row[0].'</label>
		<input type="text" class="form-control"  id="'.$row[0].'"   name="'.$row[0].'" placeholder="введите '.$row[0].' ">
		<small id="emailHelp" class="form-text text-muted"></small></div>';
	}
	$str .= '<input type="button" class="btn btn-primary" id="script" name="submit" value="Добавить Запись", onclick="addRow(`'.$table_name.'`)">';
	return $str;
}*/

function form($conn){
	$table_name=$_POST['table_name'];
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."' AND NOT(COLUMN_KEY='PRI') AND NOT(COLUMN_KEY='MUL') AND NOT(DATA_TYPE='date') AND NOT(DATA_TYPE='datetime')";
	$result = $conn->query($sql);
	$str= '<form id="form">';
	$str .=id_table($conn);
	$str .=select($conn);
	$str .=data($conn);
	$str .=data_time($conn);
	
	while($row = mysqli_fetch_array($result)){
		
		//$str .='<input type="text"  id="'.$row[0].'"   name="'.$row[0].'" placeholder="введите '.$row[0].' " ></input></br>';
		$str .='<div class="form-group"><label for="'.$row[0].'">'.$row[0].'</label>
		<input type="text" class="form-control"  id="'.$row[0].'"   name="'.$row[0].'" placeholder="введите '.$row[0].' ">
		<small id="emailHelp" class="form-text text-muted"></small></div>';
	}
	$str .= '<input type="button" class="btn btn-primary" id="script" name="submit" value="Добавить Запись", onclick="addRow(`'.$table_name.'`)">';
	return $str;
}

function select($conn){
	$table_name=$_POST['table_name'];
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."' AND COLUMN_KEY='MUL'";
	$result = $conn->query($sql);
	$str='';
	while($row = mysqli_fetch_array($result)){
		$str .='<div class="form-group"><label for="'.$row[0].'">'.$row[0].'</label><select name="'.$row[0].'" class="form-control" id="'.$row[0].'">';
		$table_name=substr($row[0], 3);
		$select_sql="SELECT * FROM ".$table_name."";
		$select_res = $conn->query($select_sql);
		while($row = mysqli_fetch_array($select_res,MYSQLI_NUM)){
			$str .= '<option value="'.$row[0].'">'.$row[1].'</option>';	
		}
		//$str .= $select_sql;
		$str .='</select></div>';
	}
		
	return $str;
}

function id_table($conn){
	$table_name=$_POST['table_name'];
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."' AND COLUMN_KEY='PRI'";
	$result = $conn->query($sql);
	$str='';
	while($row = mysqli_fetch_array($result)){
		$str .= '<input type="hidden" name="'.$row[0].'"></input>';
	}
	return $str;
}
function data($conn){
	$table_name=$_POST['table_name'];
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."' AND DATA_TYPE='date'";
	$result = $conn->query($sql);
	$str='';
	while($row = mysqli_fetch_array($result)){
		$d=date("Y-m-d");
		//$str .= '<input type="hidden" name="'.$row[0].'" value="'.$d.'">';
		//$str .= '';
		$date = new DateTime(date("Y-m-d"));
		#$str .= '<label >'.$row[0].'</label><input id="'.$row[0].'_datapicker"  value="'.$date->format("m/d/Y").'"/>';
		$str .= '<div class="form-group"><label>'.$row[0].'</label><input type="date" name="'.$row[0].'" max="3000-12-31" min="1000-01-01" value="'.$date->format("Y-m-d").'" class="form-control"></div>';
	}
	return $str;
}
function data_time($conn){
	$table_name=$_POST['table_name'];
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."' AND DATA_TYPE='datetime'";
	$result = $conn->query($sql);
	$str='';
	while($row = mysqli_fetch_array($result)){
		$d=date("Y-m-d H:i:s");
		$str .= '<input type="hidden" name="'.$row[0].'" value="'.$d.'">';
		//$str .= '';
		#$date = new DateTime(date("Y-m-d"));
		#$str .= '<label >'.$row[0].'</label><input id="'.$row[0].'_datapicker"  value="'.$date->format("m/d/Y").'"/>';
		#$str .= '<div class="form-group"><label>'.$row[0].'</label><input type="date" name="'.$row[0].'" max="3000-12-31" min="1000-01-01" value="'.$date->format("Y-m-d").'" class="form-control"></div>';
	}
	return $str;
}


echo print_table($conn).'@@@'.form($conn);


$conn->close();
?>