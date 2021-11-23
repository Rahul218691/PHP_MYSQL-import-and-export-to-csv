<?php
// Load the database configuration file
include_once 'dbConfig.php';

if(isset($_POST['importSubmit'])){
    
    // Allowed mime types
    $csvMimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');
    
    // Validate whether selected file is a CSV file
    if(!empty($_FILES['file']['name']) && in_array($_FILES['file']['type'], $csvMimes)){
        
        // If the file is uploaded
        if(is_uploaded_file($_FILES['file']['tmp_name'])){
            
            // Open uploaded CSV file with read-only mode
            $csvFile = fopen($_FILES['file']['tmp_name'], 'r');
            
            // Skip the first line
            fgetcsv($csvFile);
            
            // Parse data from CSV file line by line
            while(($line = fgetcsv($csvFile)) !== FALSE){
                // Get row data
                $fname = $line[1];
                $lname = $line[2];
                $email  = $line[3];
                $gender  = $line[4];
                $country = $line[5];
                $created = $line[6];
                $status = $line[7];

                if($status == "Active"){
                    $status = 1;
                }else{
                    $status = 0;
                }

                
                // Check whether member already exists in the database with the same email
                $prevQuery = "SELECT id FROM members WHERE email = '$email'";
                $prevResult = $db->query($prevQuery);
                // echo $prevResult->num_rows;
                if($prevResult->num_rows > 0){
                    // Update member data in the database
                }else{
                    // Insert member data in the database
                    $sql = "INSERT INTO members(first_name,last_name, email, gender, created, status, country) VALUES('$fname','$lname','$email','$gender','$created','$status','$country')";
                    // echo $sql;
                    $run = mysqli_query($db,$sql);
                }
            }
            
            // Close opened CSV file
            fclose($csvFile);
            
            $qstring = '?status=succ';
        }else{
            $qstring = '?status=err';
        }
    }else{
        $qstring = '?status=invalid_file';
    }
}

// Redirect to the listing page
header("Location: index.php".$qstring);