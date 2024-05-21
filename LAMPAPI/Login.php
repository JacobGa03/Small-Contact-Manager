<?php

	require 'common.php';

	$required = ['username', 'password'];
	$inData = getRequestParams($required);
	
	$id = 0;
	$FirstName = "";
	$LastName = "";

	$conn = getDbConnection(); 	
	if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
		//This query works for selecting Users that are ALREADY in the database
		$stmt = $conn->prepare("SELECT ID,FirstName,LastName FROM Users WHERE Login=? AND Password =?");
		//Bind the login and password to the '?' in our query
		$stmt->bind_param("ss", $inData["username"], $inData["password"]);
		$stmt->execute();
		$result = $stmt->get_result();

		//Return the full name of the user along with their assigned ID
		if( $row = $result->fetch_assoc()  )
		{
			returnWithInfo( $row['FirstName'], $row['LastName'], $row['ID'] );
		}
		//User isn't in the database
		else
		{
			returnError(CODE_NOT_FOUND, "No Records Found");
		}

		$stmt->close();
		$conn->close();
	}
	
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"ID":0,"FirstName":"","LastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $firstName, $lastName, $id )
	{
		$retValue = '{"ID":' . $id . ',"FirstName":"' . $firstName . '","LastName":"' . $lastName . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>
