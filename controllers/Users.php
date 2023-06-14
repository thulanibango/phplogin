<?php
require_once '../model/Users.php';
require_once '../helpers/session_helpers.php';

class Users {
    private $userModel;
    public function __construct(){
        $this->userModel = new User;
    }
     public function register(){

                //Clean Post data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            // //Initialising data array
            $data = [
                'userName'=>trim($_POST['userName']),
                'email'=>trim($_POST['email']),
                // 'userUid'=>trim($_POST['userUid']),
                'password'=>trim($_POST['password']),
                'password2'=>trim($_POST['password2'])
            ];

            // input validations
            if(empty($data['userName']) || empty($data['email']) || empty($data['password']) || empty($data['password2'])){
                flash("registration", "please fill out all inputs");
                redirect("../signup.php");
            }
            if(!preg_match("/^[a-zA-Z0-9]*$/", $data['userName'])){
                flash("registration", "Invalid username");
                redirect("../signup.php");
            }

            if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
                flash("registration", "Invalid email");
                redirect("../signup.php");
            }

            if(strlen($data['password']) < 6){
                flash("registration", " Password must be greater than 6 char");
                redirect("../signup.php");
            } 
            if($data['password'] !== $data['password2']){
                flash("registration", "Passwords don't match");
                redirect("../signup.php");
            }
            //User with the same email or password already exists
            if($this->userModel->findUser($data['email'], $data['userName'])){
                
                flash("registration", "Username or email already taken");
                redirect("../signup.php");
            }

            //Passed all validation checks.
            //Now going to hash password
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

            //Register User
          
            if($this->userModel->newUser($data)){
                redirect("../login.php");
            }else{
                die("Something went wrong");
            }
          
    }

    public function login(){
        //Clean the POST Data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        // Initialise data
        $data=[
            'email'=>trim($_POST['email']),
            'name'=>trim($_POST['name']),
            'password'=>trim($_POST['password'])
        ];
        echo "here";
        if (empty($data['email']) || empty($data['password'])) {
            flash("login", "Make sure all inputs are filled in");
            header("../login.php");
            exit();
        }
        echo "her2";
        //check email user exists
        try {
            $result = $this->userModel->findUser($data['email'],$data['name'] );
            if($result){
                $loggedIn = $this->userModel->loginUser($data['email'], $data['password']);
                // print_r($loggedIn);
                // print_r($result->id);
                if ($loggedIn) {
                    $this->userSession($loggedIn);
                    redirect("../index.php"); 
                } else {
                    flash("login", "Incorrect password or username");
                    redirect("../login.php"); 
                }
                
            }else{
                flash("login", "No user found");
                redirect("../login.php");
            }
        }catch (Exception $e) {
            echo $e->getMessage();
        }
    }
 
    public function userSession($user){
        $_SESSION['userId']= $user->id;
        $_SESSION['name'] = $user->name;
        $_SESSION['email'] = $user->email;
        redirect("../index.php");
    }
    public function logout(){
        unset($_SESSION['userId']);
        unset($_SESSION['name']);
        unset($_SESSION['email']);
        session_destroy();
        redirect("../index.php");
    }
}

$initUser =new Users;

// //Ensure that user is sending request to a post rest

    if($_SERVER['REQUEST_METHOD']=='POST'){
        switch($_POST['type']){
            case 'registration':
                $initUser->register();
                break;
            case 'login':
                $initUser->login();
                break;
            default:
                redirect("../index.php");
        }
    }else{
        switch($_GET['q']){
            case 'logout':
                $initUser->logout();
                break;
            default:
            redirect("../index.php");
        }
    }


?>