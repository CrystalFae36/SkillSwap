<?php
    require_once 'DatabaseConnection.php';

    class User{
        //Fields
        public $id = 0;
        public $username = "";
        public $password = "";
        public $displayname = "";
        public $email = "";
        public $age = 0;
        public $state_id = 0;
        public $isadmin = 0;
        public $createdate;

        function populate($p_user_id){

            //Create new connection. 
            $db = new DatabaseConnection();

            // Prepare the SELECT statement with a placeholder for the user ID
            $sql = "SELECT * FROM skillswap.user where user_id=?;";
            $stmt = $db->connection->prepare($sql);

            // Check if the statement preparation was successful
            if ($stmt === false) {
                die("Error preparing statement: " . $db->connection->error);
            }
            //Bind parameters to sql statement 
            $stmt->bind_param("i", $p_user_id);
            //Execute query
            $stmt->execute();
            // get the mysqli result
            $result = $stmt->get_result(); 


            if($row = $result->fetch_assoc()){
                $this->id = $p_user_id; 
                $this->username = $row['user_username'];
                $this->password = $row['user_password'];
                $this->displayname = $row['user_displayname'];
                $this->email = $row['user_email'];
                $this->state_id = $row['user_state_id'];
                $this->age = $row['user_age'];
                $this->isadmin = $row['user_isadmin'];
                $this->createdate = $row['user_createdate'];
            }
            //Clean up
            $stmt->close();
            $result -> free_result();
            $db->closeConnection();
        }

        function insert(){

            //Create new connection. 
            $db = new DatabaseConnection();

            // Prepare the SELECT statement with a placeholder for the user ID
            $sql = "INSERT INTO skillswap.user (user_username, user_password, user_displayname, user_email, user_age, user_state_id, user_isadmin, user_createdate) VALUES (?,?,?,?,?,?,?, NOW());";
            $stmt = $db->connection->prepare($sql);

            // Check if the statement preparation was successful
            if ($stmt === false) {
                die("Error preparing statement: " . $db->connection->error);
            }
            
            //Password hash
            $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

            //Bind parameters to sql statement 
            $stmt->bind_param("ssssisi", $this->username, $hashedPassword, $this->displayname, $this->email, $this->age, $this->state_id, $this->isadmin);
            //Execute query
            if($stmt->execute()){
                $this->id = $stmt->insert_id;
            }

            //Clean up
            $stmt->close();
            $db->closeConnection();
        }

        public static function validateUser($p_username, $p_password){
            $userId = 0; 
                        
            //Create new connection. 
            $db = new DatabaseConnection();

            // Prepare the SELECT statement with a placeholder for the user ID
            $sql = "SELECT * FROM skillswap.user where user_username=?;";
            $stmt = $db->connection->prepare($sql);

            // Check if the statement preparation was successful
            if ($stmt === false) {
                die("Error preparing statement: " . $db->connection->error);
            }
            //Bind parameters to sql statement 
            $stmt->bind_param("s", $p_username);
            //Execute query
            $stmt->execute();
            // get the mysqli result
            $result = $stmt->get_result(); 

            if($row = $result->fetch_assoc()){
                $password = $row['user_password'];
                if(password_verify($p_password, $password)){
                    $userId = $row['user_id'];
                }
            }

           //Clean up
            $stmt->close();
            $result -> free_result();
            $db->closeConnection();

            return $userId; 
        }

    }




?>