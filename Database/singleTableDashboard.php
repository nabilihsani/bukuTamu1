<?php 
	require_once('db_login.php');
    $db = new mysqli($db_host, $db_username, $db_password, $db_database);
    if ($db->connect_errno) {
    	die ("Could not connect to the database: <br />"). $db->connect_errno;
    }
    
    $query = "SELECT b.*, a.Nama, a.Email, a.Phone, a.Company, a.Lokasi FROM kunjungan AS b INNER JOIN tamu AS a ON a.idTamu = b.idTamu WHERE DATE(Masuk) = CURDATE() OR Status = 'Active'";
    $result = $db->query($query);
    $numRow = $result->num_rows;
    while ($row = mysqli_fetch_array($result)) {
        $dateIn = date_create($row['Masuk']);
    	$dateIn1 = date_format($dateIn, 'Y-m-d H:i:s');
    	if ($row['Keluar'] != '') {
    		$dateOut = date_create($row['Keluar']);
    		$dateOut1 = date_format($dateOut, 'Y-m-d H:i:s');
    	} else {
    		$dateOut1 = '-';
    	}
        if ($row['Code'] != '') {
            $code = $row['Code'];
        } else {
            $code = '-';
        }
        if ($row['Phone'] != '') {
            $telp = $row['Phone'];
        } else {
            $telp = '-';
        }
        if ($row['Lokasi'] != '') {
            $loc = $row['Lokasi'];
        } else {
            $loc = '-';
        }
    	echo '<tr>';
    	echo '<td class="d-none">'.$row['idKunjungan'].'</td>';
    	echo '<td>'.$row['idTamu'].'</td>';
        echo '<td>'.$code.'</td>';
    	echo '<td>'.$row['Nama'].'</td>';
        echo '<td>'.$row['Email'].'</td>';
        echo '<td>'.$telp.'</td>';
    	echo '<td>'.$row['Company'].'</td>';
        echo '<td>'.$loc.'</td>';
    	echo '<td>'.$row['Tujuan'].'</td>';
    	echo '<td>'.$row['Keperluan'].'</td>';
    	echo '<td>'.$dateIn1.'</td>';
    	echo '<td>'.$dateOut1.'</td>';
        echo '<td><div class="dropdown show">
                    <a class="btn btn-success dropdown-toggle text-white" type="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Action
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item" data-toggle="modal" data-target="#codeModal" href="#">Input Card</a>
                        <a class="dropdown-item" data-toggle="modal" data-target="#OutModalS" href="#">Check Out</a>
                        <a class="dropdown-item" data-toggle="modal" data-target="#DelModalS" href="#">Delete</a>
                    </div>
                </div>';
        echo '</tr>';
    }

    function testInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    if (isset($_POST["submitCode"])) {
        $code2 = testInput($_POST['staticCodeIn']);
        $code1 = testInput($_POST['inputCode']);
        $query2 = "SELECT * FROM kartu WHERE Code = '$code1'";
        $result2 = $db->query($query2);
        while ($row = mysqli_fetch_array($result2)) {
            $status = $row['status'];
        }
        if ($status == 'Available') {
            $query = "UPDATE kunjungan SET Code = '$code1' WHERE idKunjungan = '$code2'";
            $query1 = "UPDATE kartu SET id =  (SELECT idTamu FROM kunjungan WHERE idKunjungan = '$code2'), status = 'Unavailable' WHERE Code = '$code1'";
            $result = $db->query($query);
            $result1 = $db->query($query1);
            echo "<script type='text/javascript'>
            $(document).ready(function() {
            $('#codeModal').modal('hide');
            });
            </script>";
            echo "<meta http-equiv='refresh' content='0'>";
        }     
    }

    if (isset($_POST["submitDelS"])) {
        $code2 = testInput($_POST['staticCodeS']);
        $code1 = testInput($_POST['staticCodeS1']);
        $query2 = "DELETE FROM kunjungan WHERE idKunjungan = '$code2'";
        $result2 = $db->query($query2);
        $query = "DELETE FROM tamu WHERE idTamu = '$code1'";
        $result = $db->query($query);
            echo "<script type='text/javascript'>
            $(document).ready(function() {
            $('#DelModalS').modal('hide');
            });
            </script>";
            echo "<meta http-equiv='refresh' content='0'>";
    }     
    
    if (isset($_POST["submitOutS"])) {
        $code2 = testInput($_POST['staticCodeOutS']);
        $query2 = "UPDATE kunjungan SET Keluar = CURRENT_TIMESTAMP(), Code = NULL, Status = 'Passive' WHERE idKunjungan = '$code2'";
        $query1 = "UPDATE kartu SET id =  '-', status = 'Available' WHERE Code = (SELECT Code FROM kunjungan WHERE idKunjungan = '$code2')";
        $result = $db->query($query1);
        $result2 = $db->query($query2);
            echo "<script type='text/javascript'>
            $(document).ready(function() {
            $('#OutModalS').modal('hide');
            });
            </script>";
            echo "<meta http-equiv='refresh' content='0'>";
    }    

    $db->close();
 ?>