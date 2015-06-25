<?php
// Attempt MySQL server connection.
$link = mysqli_connect("host", "user", "password", "db");
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

//display address book
function fetch(){
	global $link;
	$sql = "SELECT contacts.P_ID, contacts.LastName, contacts.FirstName, address.Address_ID, address.Street, address.City, address.State, address.Zip
	FROM contacts
	JOIN address
	ON contacts.Address_ID=address.Address_ID
	ORDER BY address.Address_ID;";
	$result = $link->query($sql);
		$results=array();
		while($row = $result->fetch_assoc()) {
			$results[] = $row;
		}
		echo json_encode($results);
}

//update existing contact
function update(){
	global $link;
	foreach($_POST as $name => $value){
		$$name = mysqli_real_escape_string($link, $value);
	}
	
	$sql0 = "SELECT Address_ID FROM address 
		WHERE Street='$Street' 
		AND City='$City' 
		AND State='$State' 
		AND Zip='$Zip'";
	
	$sql3 = "UPDATE contact 
		SET Address_ID=$addressID
		WHERE P_ID=$contact_ID";
	
	$sql4 = "UPDATE contacts
		 SET LastName='$LastName', FirstName='$FirstName'
		 WHERE P_ID=$contact_ID";
	
	$sql5 = "INSERT INTO address (Street, City, State, Zip) 
		VALUES ('$Street', '$City', '$State', '$Zip')";
	
	$sql6 = "UPDATE address 
		SET Street='$Street', City='$City', State='$State', Zip='$Zip' WHERE Address_ID=$address_ID";
		
	$sql7 = "SELECT (P_ID) from contacts 
		WHERE Address_ID=$address_ID";
	
//update contact name
	if (mysqli_query($link, $sql4)) {
		echo "Name updated successfully";
	} else {
		echo "Error updating record: " . mysqli_error($link);
	}
	
//check if update address existing
	$addressCheck = $link->query($sql0);
	
	if ($addressCheck->num_rows > 0) {
		$row = $results->fetch_assoc();
		$addressID = $row['Address_ID'];
//update address id for contact if address existing		
		if(mysqli_query($link, $sql3)){
			echo "address updated successfully.";
		} else{
			echo "ERROR: Was not able to execute $sql3. " . mysqli_error($link);
		}
		
	} else {
		if ($addressCheck->num_rows == 0) {
//insert new address if address not existing
			if (mysqli_query($link, $sql5)){
				$addressID = mysqli_insert_id($link);
//update address id for contact
				if(mysqli_query($link, $sql3)){
					echo "address updated successfully.";
				} else{
					echo "ERROR: Was not able to execute $sql3. " . mysqli_error($link);
				}
			}
		}
	}
	
	
}

//create new contact
function create(){ 
	global $link;
	foreach($_POST as $name => $value){
		$$name = mysqli_real_escape_string($link, $value);
	}
	
	$sql2 = "INSERT INTO address (Street, City, State, Zip) VALUES ('$Street', '$City', '$State', '$Zip')";

	$sql0 = "SELECT Address_ID FROM address WHERE Street='$Street' AND City='$City' AND State='$State' AND Zip='$Zip'";

//determine if address existing	
	$results = $link->query($sql0);

	if ($results->num_rows > 0) {
		$row = $results->fetch_assoc();
		$addressID = $row['Address_ID'];
	}
//add address if it does not exist
	else { 
		if (mysqli_query($link, $sql2)){
			echo "Address added successfully.";
		} else {
			echo "ERROR: Was not able to execute $sql2. " . mysqli_error($link);
		}

		$addressID = mysqli_insert_id($link);
	}
	$sql = "INSERT INTO contacts (FirstName, LastName, Address_ID) VALUES ('$FirstName', '$LastName', $addressID)";
//add contact
	if(mysqli_query($link, $sql)){
		echo "Contact added successfully.";
	} else{
		echo "ERROR: Was not able to execute $sql. " . mysqli_error($link);
	}

}

switch($_POST["action_type"]){
	case "create":
	create();
	break;
	case "update";
	update();
	break;
	case "fetch":
	fetch();
	break;
	default: fetch();
	break;
}
 
// close connection
mysqli_close($link);
?>