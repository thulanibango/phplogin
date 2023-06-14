<?php
require_once '../config/database.php';

class User{
    private $db;
    public function __construct(){
        $this->db = new Database;
    }

    //finding user by email or username
    public function findUser($email, $userName){
        $this->db->query('SELECT * FROM user WHERE userName = :userName OR email = :email');
        $this->db->bind(':userName', $userName);
        $this->db->bind(':email', $email);
        $row = $this->db->single();
        //check rows
        if ($this->db->rowCount() > 0) {
            return $row;
        }else{
            return false;
        }
    }

    //registering and adding new user
    public function newUser($data){
        $this->db->query('INSERT INTO user (userName, email, password) VALUES (:userName, :email, :password)');
        // Bind values
        $this->db->bind(':userName', $data['userName']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['password']);
        try {
            $result = $this->db->execute();
            if ($result) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }       
    }
    public function loginUser($email, $password){
        $row= $this->findUser($email, $name);
        if ($row == false) {
            echo "false";
        }else{
            $hashedPassword = $row->password;
           
            // // check matching password/
            if(password_verify($password, $hashedPassword)){
                return $row;
            }else{
                return false;
            }
        }
    }

    public function resetnewPassword($newPwdHash, $tokenEmail){
        $this->db->query('UPDATE user SET password=:password WHERE email=:email');
        $this->db->bind(':password', $newPwdHash);
        $this->db->bind(':email', $tokenEmail);

        // //Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
};

?>