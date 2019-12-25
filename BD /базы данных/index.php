<!DOCTYPE html>
<?php
session_start();
header("Content-type: text/html; charset=utf-8");
$servername = "localhost";
$username = "root";
$password = "";
$_SESSION["dbname"]="hotel";
// Create connection
$conn = new mysqli($servername, $username, $password, $_SESSION["dbname"]);
mysqli_query($conn,"SET NAMES utf8");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
	echo 'error';
} 

?>

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
	
  </head>
  <body>
	<div class='center_block'>
	<div class="btn-group" role="group" aria-label="Basic example">
	  <?php
	  //menu 
	  $sql = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."'";
	  $result = $conn->query($sql);
	
	  while($row = mysqli_fetch_array($result)){
		 echo'<input type="button" class="btn  btn-secondary" id="script" name="scriptbutton" value=" '.$row[0].' " onclick="printTable(\''.$row[0].'\',0,id)">';
		}
	  ?>
	</div>
	</div>

<?php
$date = new DateTime(date("Y-m-d"));
#echo $date->format("Y-m-d");
#echo $date->format("m/d/Y");
#echo '<input id="datepicker"  value="'.$date->format("m/d/Y").'"/><script>$("#datepicker").datepicker({uiLibrary: "bootstrap4"});</script>';
?>
	
    
<!--<div class="container">
        Start Date: <input id="startDate" />
        End Date: <input id="endDate" />
    </div>
    <script>
        var today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
        $('#startDate').datepicker({
            uiLibrary: 'bootstrap4',
            iconsLibrary: 'fontawesome',
            minDate: today,
            maxDate: function () {
                return $('#endDate').val();
            }
        });
        $('#endDate').datepicker({
            uiLibrary: 'bootstrap4',
            iconsLibrary: 'fontawesome',
            minDate: function () {
                return $('#startDate').val();
            }
        });
    </script>-->
	<div id="well_done"></div>
	<table id='view' class="table table-striped"></table>
	
    <?php
	

	//form text fields
	/*$table_name='j';
	
	$sql = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.columns WHERE TABLE_SCHEMA = '".$_SESSION["dbname"]."' AND TABLE_NAME='".$table_name."'";
	$result = $conn->query($sql);
	echo '<form id="form">';
	while($row = mysqli_fetch_array($result)){
		echo'<input type="text"  name="'.$row[0].'" placeholder="введите '.$row[0].' " ></input></br>';
	}
	echo '<input type="button" id="script" name="submit" value="submit", onclick="addRow(`'.$table_name.'`)"></form>';
	*/
	?>
	
	<!-- mulai coding modal
  <button type="button" class="btn" data-toggle="modal" data-target=".bs-example-modal-lg" style="border-radius:0px;">Large modal</button> -->

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

<div class="modal fade modal_update" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" >
    <div class="modal-content" style=" ">
		<form id="form_window_update" style='padding:20px;';>
		
		</form>
		<div style="display: inline-block;">
			<div id="ajax_button_up" style="padding:20px; display: inline-block;" ></div>
			<div id="ajax_button_del" style="padding:20px; display: inline-block;"></div>
		</div>
		
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
  </div>
</div>


<script type="text/javascript">

</script>

   
    <script charset="utf-8" type="text/javascript">
	//функция создания конопок удалить и обновить при нажатии значка подробно
		function index_del_up(id,table_name){
			$.post("add_up_del.php",{action:'3',table_name: table_name,id:id},onAjaxSuccess);
			//номер нажатой строки
			$('#view').find('tr').click( function(){
			ind=$(this).index();
			});
			
			function onAjaxSuccess(data){
				$('#form_window_update').html(data);
				$('#ajax_button_up').html('<button class="btn btn-primary" onclick="updateRow(`'+id+'`,`'+table_name+'`,'+ind+')">Обновить</button>');
				$('#ajax_button_del').html('<button class="btn btn-warning" onclick="delRow(`'+id+'`,`'+table_name+'`,'+ind+')">Удалить</button>');
			}
			
		}
		//печать таблицы
        function printTable(table_name,ind,name){
            $.post("table_and_form.php",{table_name: table_name,ind: ind,name: name},onAjaxSuccess);
			 
			function onAjaxSuccess(data){
			//alert(data);
			l = data.length;
			n = data.search('@@@');
			str1 = data.substr(0, n);
			str2 = data.substr(n+3,l);
			
			$('#view').show();
			$('#view').html(str1);
			
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
		
		function delRow(id,table_name,ind){
			$.post("add_up_del.php",{action:'1',table_name: table_name,id:id},onAjaxSuccess);

			function onAjaxSuccess(data){
				if(data==1){	
					$('#well_done').html('<div class="alert alert-warning" role="alert" style="text-align: center;">Запись удалена успешно!</div>');
					$('.modal_update').modal('hide');
					document.getElementById("view").deleteRow(ind);
					$("#well_done").show('slow'); setTimeout(function() { $("#well_done").hide('slow'); }, 800);
				} else{
					alert('Невозможно удалить запись.');
					
				}
			}
		}
		
		function updateRow(id,table_name,ind){
			var str = $( "#form_window_update").serialize();
			str = str + '&table_name=' + table_name;
			str = str + '&action=' + '2';
			str = str + '&id=' + id;
			//alert(str);
			$.post("add_up_del.php",str,onAjaxSuccess);
			
			function onAjaxSuccess(data){
				l = data.length;
				n = data.search('@@@');
				str1 = data.substr(0, n);
				str2 = data.substr(n+3,l);
				//alert(data);
				if(str1==1){
					//alert(data);
					document.getElementById("view").rows[ind].innerHTML=str2;
					$('.modal_update').modal('hide');
					$('#well_done').html('<div class="alert alert-info" role="alert" style="text-align: center;">Запись обновлена успешно!</div>');
					$("#well_done").show('slow'); setTimeout(function() { $("#well_done").hide('slow'); }, 800);
				}
				
			}
		}
    </script>
  </body>
</html>