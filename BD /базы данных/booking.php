<html>
  <head>    
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
  
  <link rel="stylesheet" href="css/index.css">  
  
	<script src="http://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous" charset="utf-8" type="text/javascript"></script>
  <!--bootstrap-->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
	
	 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
	<style>
	
	body {
  background-color: #eee;
}
 table {
      border-collapse: collapse;
	  color: #ccc;
    }

    td,
    th {
      border: 1px solid #fff;
      padding: 3px;
	  color: #17a2b8;
      text-align: center;
    }
	td{
		color: #0071e9;
	}

    th {
      font-weight: bold;
	  color: #17a2b8;
      background-color: #fff;
	  border-bottom: 1px solid #ccc;
    }

	</style>
	

  </head>
  <body width="1000" style="margin: 5%;">
<h1>Hotel</h1><button class="btn btn-primary"  data-toggle="modal" data-target=".bs-example-modal-lg" style="margin-right:10px; display: inline-block;" onClick="printTable('customers',0,id)">Добавить нового клиента</button>
<div id="well_done"></div>


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
function date_chech($conn,$date_start,$date_end,$room){
	$sql="SELECT booking_start_date,booking_end_date FROM `booking` WHERE id_room=".$room." AND booking_start_date>'".$date_start."' AND booking_end_date<'".$date_end."'";
	$result = $conn->query($sql);
	$ar=array();
	while($row_date = mysqli_fetch_array($result,MYSQLI_NUM)){
		for ($i = (int)substr($row_date[0],8); $i <= (int)substr($row_date[1],8); $i++) {
			array_push($ar, $i);	
		}
	}
return $ar;
}
//echo implode(date_chech($conn,'2019-09-01','2019-10-01')," ")
?>
<script>
        function printTable(table_name,ind,name){
            $.post("table_and_form.php",{table_name: table_name,ind: ind,name: name},onAjaxSuccess);

			function onAjaxSuccess(data){
			//alert(data);
			l = data.length;
			n = data.search('@@@');
			str1 = data.substr(0, n);
			str2 = data.substr(n+3,l);
			
			//$('#view').show();
			//$('#view').html(str1);
			
			$('#form_window').show();
			$('#form_window').html(str2);
			
			}
        }
		

function addRow(table_name){
			var str = $( "#form_window").serialize();
			str=str + '&table_name=' + table_name;
			str=str + '&action=' + '0';
			//alert(str);
			$.post("add_up_del.php",str,onAjaxSuccess);
			 
			function onAjaxSuccess(data){
				//alert(data);
				l = data.length;
				n = data.search('@@@');
				str1 = data.substr(0, n);
				str2 = data.substr(n+3,l);
				if(str1==1){
					$('#view tr:first').after(str2);
					$('.bs-example-modal-lg').modal('hide');
					$('#well_done').html('<div class="alert alert-success" role="alert" style="text-align: center;">Запись добавлена успешно!</div>');
					
					$("#well_done").show('slow'); setTimeout(function() { $("#well_done").hide('slow'); }, 800);
				}else{
					//alert(str1);
					//alert(str2);
					alert('запись уже есть');
				}
			}
		
		}
function createCalendar(elem, year, month,indexs) {
      let mon = month - 1; // месяцы в JS идут от 0 до 11, а не от 1 до 12
      let d = new Date(year, mon);
		//alert(indexs);
      let table = '<table><tr><th>пн</th><th>вт</th><th>ср</th><th>чт</th><th>пт</th><th>сб</th><th>вс</th></tr><tr>';
      // пробелы для первого ряда
      // с понедельника до первого дня месяца
      // * * * 1  2  3  4
      for (let i = 0; i < getDay(d); i++) {
        table += '<td></td>';
      }

      // <td> ячейки календаря с датами
      while (d.getMonth() == mon) {
		date=d.getDate();
		if(indexs.includes(date)){
			table += '<td style="background-color: #ccc; color: #ccc;">' + date + '</td>';
		}else{
			table += '<td>' + date + '</td>';
		}
        

        if (getDay(d) % 7 == 6) { // вс, последний день - перевод строки
          table += '</tr><tr>';
        }

        d.setDate(d.getDate() + 1);
      }

      // добить таблицу пустыми ячейками, если нужно
      // 29 30 31 * * * *
      if (getDay(d) != 0) {
        for (let i = getDay(d); i < 7; i++) {
          table += '<td></td>';
        }
      }

      // закрыть таблицу
      table += '</tr></table>';

      elem.innerHTML = table;
    }

    function getDay(date) { // получить номер дня недели, от 0 (пн) до 6 (вс)
      let day = date.getDay();
      if (day == 0) day = 7; // сделать воскресенье (0) последним днем
      return day - 1;
    }
</script>
<?php

function facilities($conn,$room){
	$str = "";
	$sql = "SELECT id_object FROM facilities WHERE id_room='".$room."'";
	$result = $conn->query($sql);
	while($row = mysqli_fetch_array($result)){
		$sql = "SELECT 	object_name FROM object WHERE id_object='".$row[0]."'";
		$result_1 = $conn->query($sql);
		while($row_1= mysqli_fetch_array($result_1)){
			$str .= $row_1[0].', ';
		}
	}
	
	return substr($str,0,-2).'.';
}


$sql = "SELECT * FROM room";
$result = $conn->query($sql);
$str='<div class="row">';
$i=0;
while($row = mysqli_fetch_array($result)){
	if($i%2==0){
		$str .='</div><br><div class="row">';
	}
	
	$sql = "SELECT * FROM type WHERE id_type=".$row[2]."";
	
	$result_type = $conn->query($sql);
	while($row_type = mysqli_fetch_array($result_type)){
		$type=$row_type[1];
	}
	
	$sql = "SELECT * FROM price WHERE id_price=".$row[3]."";
	
	$result_price = $conn->query($sql);
	while($row_price = mysqli_fetch_array($result_price)){
		$price=$row_price[1];
	}
	
	$str .= ' <div class="col-sm-6" >
    <div class="card" >
      <div class="card-body">
        <h5 class="card-title">'.$row[1].'</h5>
        <p class="card-text">Цена за ночь '.$price.' | '.$type.'</p>
		
        <button class="btn btn-primary"  data-toggle="modal" data-target=".bs-example-modal-lg" style="margin-right:10px; display: inline-block;" onClick="printTable(\'booking\',0,id)">Забронировать</button>
		<button class="btn btn-info"  data-toggle="modal" data-target=".bs-example-modal-lg"  style="margin-left:10px; display: inline-block;" onClick="printTable(\'payment\',0,id)">Оплатить</button>
		 <div id="calendar_'.$i.'" style="margin-top:-120px; margin-right:-300px;"></div>
		<script>createCalendar(calendar_'.$i.', 2019, 9,['.implode(date_chech($conn,'2019-09-01','2019-10-01',$row[0]),",").']);</script>
		<div>'.facilities($conn,$row[0]).'</div>
      </div>
	 
    </div>
  </div>';
  $i+=1;
	//$str .= '<div class="form-group"><label>'.$row[0].'</label><input type="date" name="'.$row[0].'" max="3000-12-31" min="1000-01-01" value="'.$date->format("Y-m-d").'" class="form-control"></div>';
	}
	
echo $str;
?>


<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" >
    <div class="modal-content">
		<form id="form_window" style='padding:20px;';></form>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
  </div>
</div>


<script>
//печать таблицы
        function printTable(table_name,ind,name){
            $.post("table_and_form.php",{table_name: table_name,ind: ind,name: name},onAjaxSuccess);

			function onAjaxSuccess(data){
			//alert(data);
			l = data.length;
			n = data.search('@@@');
			str1 = data.substr(0, n);
			str2 = data.substr(n+3,l);
			
			//$('#view').show();
			//$('#view').html(str1);
			
			$('#form_window').show();
			$('#form_window').html(str2);
			
			}
        }
		

function addRow(table_name){
			var str = $( "#form_window").serialize();
			str=str + '&table_name=' + table_name;
			str=str + '&action=' + '0';
			//alert(str);
			$.post("add_up_del.php",str,onAjaxSuccess);
			 
			function onAjaxSuccess(data){
				//alert(data);
				l = data.length;
				n = data.search('@@@');
				str1 = data.substr(0, n);
				str2 = data.substr(n+3,l);
				if(str1==1){
					$('#view tr:first').after(str2);
					$('.bs-example-modal-lg').modal('hide');
					$('#well_done').html('<div class="alert alert-success" role="alert" style="text-align: center;">Запись добавлена успешно!</div>');
					
					$("#well_done").show('slow'); setTimeout(function() { $("#well_done").hide('slow'); }, 800);
				}else{
					//alert(str1);
					//alert(str2);
					alert('запись уже есть');
				}
			}
		
		}
function createCalendar(elem, year, month) {

      let mon = month - 1; // месяцы в JS идут от 0 до 11, а не от 1 до 12
      let d = new Date(year, mon);

      let table = '<table><tr><th>пн</th><th>вт</th><th>ср</th><th>чт</th><th>пт</th><th>сб</th><th>вс</th></tr><tr>';

      // пробелы для первого ряда
      // с понедельника до первого дня месяца
      // * * * 1  2  3  4
      for (let i = 0; i < getDay(d); i++) {
        table += '<td></td>';
      }

      // <td> ячейки календаря с датами
      while (d.getMonth() == mon) {
        table += '<td>' + d.getDate() + '</td>';

        if (getDay(d) % 7 == 6) { // вс, последний день - перевод строки
          table += '</tr><tr>';
        }

        d.setDate(d.getDate() + 1);
      }

      // добить таблицу пустыми ячейками, если нужно
      // 29 30 31 * * * *
      if (getDay(d) != 0) {
        for (let i = getDay(d); i < 7; i++) {
          table += '<td></td>';
        }
      }

      // закрыть таблицу
      table += '</tr></table>';

      elem.innerHTML = table;
    }

    function getDay(date) { // получить номер дня недели, от 0 (пн) до 6 (вс)
      let day = date.getDay();
      if (day == 0) day = 7; // сделать воскресенье (0) последним днем
      return day - 1;
    }

	
		

//var busy='<?php echo implode(date_chech($conn,'2019-09-01','2019-10-01')," ")?>'.split(' ')





</script>




</body>

