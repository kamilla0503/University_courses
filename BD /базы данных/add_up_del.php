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

//for add and update output

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
	return $str;
}

function name_for_single_filed($conn,$id){
	$table_name=$_POST['table_name'];
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."'  AND NOT(COLUMN_KEY='PRI')  AND NOT(COLUMN_KEY='MUL')";
	$result = $conn->query($sql);
	$select_sql="SELECT ";
	while($row = mysqli_fetch_array($result)){
		$select_sql .= $row[0].", ";
	}
	$select_sql = substr($select_sql,0,-2);
	
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."'  AND COLUMN_KEY='PRI'";
	$result = $conn->query($sql);
	while($row = mysqli_fetch_array($result)){
		$select_sql .=" FROM ".$table_name." WHERE ".$row[0]."=".$id."";
	}
	$str='';
	$result = $conn->query($select_sql);
	while($row = mysqli_fetch_array($result,MYSQLI_NUM)){
		for($i = 0; $i < count($row); $i++){
			$str .="<td>".$row[$i]."</td>";
		}
	}
	return $str;
	
}
//add
function add_row($conn){
	$table_name=$_POST['table_name'];
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."' AND NOT(COLUMN_KEY='PRI')";
	$result = $conn->query($sql);
	//создаём запрос на insert
	$ar=array();
	$str="INSERT INTO ".$table_name."(";
	$val=") VALUE (";
	$tr="<tr>";
	$i=0;
	while($row = mysqli_fetch_array($result)){
		$str .= $row[0].', ';
		$val .= '"'.$_POST[$row[0]].'", ';
		//$tr .= '<td>'.$_POST[$row[0]].'</td>';
		if($i==0){
			$ind=$_POST[$row[0]];
		}
		$i+=1;
	}
	$str = substr($str,0,-2);
	$val = substr($val,0,-2);
	
	$str.=$val;
	$str .=")";
	
	$index=$conn->query($str);
	//drow tr
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."'  AND COLUMN_KEY='PRI'";
	$result = $conn->query($sql);
	while($row = mysqli_fetch_array($result)){
		$id_table=$row[0];
	}
	
	$sql="SELECT max(".$id_table.") FROM ".$table_name."";
	$result = $conn->query($sql);
	while($row = mysqli_fetch_array($result)){
		$tr .= name_for_mul_field($conn,$row[0]);
		$tr .= name_for_single_filed($conn,$row[0]);
		$tr .='<td><button class="btn btn-secondary"  data-toggle="modal" data-target=".modal_update" onClick="index_del_up(`'.$row[0].'`,`'.$table_name.'`)">&hellip;</button></td></tr>';
	}
	
	
	return $index.'@@@'.$tr;
	//return $str;
}

function del_row($conn){
	$table_name=$_POST['table_name'];
	$id=$_POST['id'];
	
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."'";
	$result = $conn->query($sql);
	
	while($row = mysqli_fetch_array($result)){
		$name=$row[0];
		break;
	}

	$sql="DELETE FROM ".$table_name." WHERE ".$name."='".$id."' ";
	return $conn->query($sql);
}

function update_row($conn){
	$table_name=$_POST['table_name'];
	$id=$_POST['id'];
	
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."' AND COLUMN_KEY='PRI'";
	$result = $conn->query($sql);
	
	while($row = mysqli_fetch_array($result)){
		$ind=$_POST[$row[0]];
		$name=$row[0];
	}
	
	//вывод названия стобцов таблицы
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."' AND NOT(COLUMN_KEY='PRI')";
	$result = $conn->query($sql);
	
	$str="UPDATE ".$table_name." SET ";
	$tr="<tr>";
	
	while($row = mysqli_fetch_array($result)){
		$str .= $row[0].' = "'.$_POST[$row[0]].'", ';
		//$val .= $_POST[$row[0]].', ';
	}
	$str = substr($str,0,-2);
	$str .= ' WHERE '.$name.'="'.$id.'"';
	//return $str;
	$index=$conn->query($str);
	
	//drow tr
	$tr .= name_for_mul_field($conn,$id);
	$tr .= name_for_single_filed($conn,$id);
	$tr .='<td><button class="btn btn-secondary"  data-toggle="modal" data-target=".modal_update" onClick="index_del_up(`'.$ind.'`,`'.$table_name.'`)">&hellip;</button></td></tr>';
	
	return $index.'@@@'.$tr;
}

function form_with_values($conn){
	
	$table_name=$_POST['table_name'];
	$id=$_POST['id'];
	
	//вывод названия id
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."'";
	$result = $conn->query($sql);
	
	while($row = mysqli_fetch_array($result)){
		$name=$row[0];
		break;
	}
	//вывод значений по id
	$ar=array();
	$sql = "SELECT * FROM ".$table_name." WHERE ".$name."='".$id."'";
	$result = $conn->query($sql);
	//MYSQLI_ASSOC второй вариант
	while($row = mysqli_fetch_array($result,MYSQLI_NUM)){
		$ar=$row;
	}
	//получаем id 
	
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."' AND COLUMN_KEY='PRI'" ;
	$result = $conn->query($sql);
	while($row = mysqli_fetch_array($result)){
		$id_name=$row[0];
	}
	//рисуем форму
	$str= '<form id="form">';
	
	//рисуем select
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."' AND COLUMN_KEY='MUL'";
	$result = $conn->query($sql);
	$str='';
	while($row = mysqli_fetch_array($result)){
		$str .='<div class="form-group"><label for="'.$row[0].'">'.$row[0].'</label><select name="'.$row[0].'" class="form-control" id="'.$row[0].'">';
		$table_name_val=substr($row[0], 3);
		$select_sql="SELECT * FROM ".$table_name_val."";
		$select_res = $conn->query($select_sql);
		
		// вставляем значения 
		$val_sql = "SELECT ".$row[0]." FROM ".$table_name." WHERE ".$name."='".$id."'";
		$val = $conn->query($val_sql);
		while($row = mysqli_fetch_array($val,MYSQLI_NUM)){
			$val_ind=$row[0];
		}
		
		while($row = mysqli_fetch_array($select_res,MYSQLI_NUM)){
			if($val_ind==$row[0]){
				$str .= '<option value="'.$row[0].'" selected>'.$row[1].'</option>';
			}else{
				$str .= '<option value="'.$row[0].'">'.$row[1].'</option>';
			}
			
			
		}
		//$str .= $select_sql;
		$str .='</select></div>';
	}
	
	
	//рисуем input
	
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."' AND NOT(COLUMN_KEY='PRI') AND NOT(COLUMN_KEY='MUL') AND NOT(DATA_TYPE='date') AND NOT(DATA_TYPE='datetime')" ;
	$result = $conn->query($sql);
	
	//$str .= '<div class="form-group"><label>'.$row[0].'</label><input type="date" name="'.$id_name.'" max="3000-12-31" min="1000-01-01" value="'.array_shift($ar).'" class="form-control"></div>';
	
	$str .='<input type="hidden" name="'.$id_name.'" value="'.array_shift($ar).'"></input>';
	$str .=data($conn);
	$str .=datatime($conn);
	while($row = mysqli_fetch_array($result)){
		//$str .='<input type="text"  id="'.$row[0].'"   name="'.$row[0].'" placeholder="введите '.$row[0].' " ></input></br>';
		$val_sql = "SELECT ".$row[0]." FROM ".$table_name." WHERE ".$name."='".$id."'";
		$val = $conn->query($val_sql);
		while($row1 = mysqli_fetch_array($val,MYSQLI_NUM)){
			$val_ind=$row1[0];
		}
		
		
		$str .='<div class="form-group"><label for="'.$row[0].'">'.$row[0].'</label>
		<input type="text" class="form-control"  id="'.$row[0].'"   name="'.$row[0].'" placeholder="введите '.$row[0].'" value="'.$val_ind.'"">
		<small id="emailHelp" class="form-text text-muted"></small></div>';
	}
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

function data($conn){
	$table_name=$_POST['table_name'];
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."' AND DATA_TYPE='date'";
	$result = $conn->query($sql);
	$str='';
	while($row = mysqli_fetch_array($result)){
		//$str .=value($conn,$row[0]);
		//$str .= '<input type="hidden" name="'.$row[0].'" value="'.$d.'">';
		//$str .= '';
		$date = new DateTime(date("Y-m-d"));
		#$str .= '<label >'.$row[0].'</label><input id="'.$row[0].'_datapicker"  value="'.$date->format("m/d/Y").'"/>';
		$str .= '<div class="form-group"><label>'.$row[0].'</label><input type="date" name="'.$row[0].'" max="3000-12-31" min="1000-01-01" value="'.value($conn,$row[0]).'" class="form-control"></div>';
	}
	return $str;
}

function value($conn,$name){
	$id=$_POST['id'];
	$table_name=$_POST['table_name'];
	$sql="SELECT ".$name." FROM booking WHERE id_booking=".$id."";
	#echo $sql;
	$result = $conn->query($sql);
	$str = '';
	while($row_value = mysqli_fetch_array($result)){
		$str .=$row_value[0];
	}
	#echo $str;
	return $str;
	
}

function datatime($conn){
	$table_name=$_POST['table_name'];
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."' AND DATA_TYPE='datetime'";
	$result = $conn->query($sql);
	$str='';
	while($row = mysqli_fetch_array($result)){
		//$str .=value($conn,$row[0]);
		$str .= '<input type="hidden" name="'.$row[0].'" value="'.value($conn,$row[0]).'">';
		//$str .= '';
		//$date = new DateTime(date("Y-m-d"));
		#$str .= '<label >'.$row[0].'</label><input id="'.$row[0].'_datapicker"  value="'.$date->format("m/d/Y").'"/>';
		//$str .= '<div class="form-group"><label>'.$row[0].'</label><input type="date" name="'.$row[0].'" max="3000-12-31" min="1000-01-01" value="'.value($conn,$row[0]).'" class="form-control"></div>';
	}
	return $str;
}


if($_POST['action']=='0'){
	echo add_row($conn);
	
}elseif($_POST['action']=='1'){
	echo del_row($conn);
	
}elseif($_POST['action']=='2'){
	echo update_row($conn);
	
}elseif($_POST['action']=='3'){
	echo form_with_values($conn);
	
}elseif($_POST['action']=='4'){
	
	
}

$conn->close();
?>

