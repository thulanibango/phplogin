<?php
use PHPMailer\PHPMailer\PHPMailer;

require_once '../model/resetPassword.php';
require_once '../helpers/session_helpers.php';
require_once '../model/Users.php';
//Require Php Mailer
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/Exception.php';
require_once '../PHPMailer/src/SMTP.php';


class PasswordReset {
    private $resetModel;
    private $userModel;
    private $mail;
    
    public function __construct(){
        $this->resetModel  = new ResetPassword;
        $this->userModel = new User;
        $this->mail =  new PHPMailer();
        $this->mail->isSMTP();
        $this->mail->Host = 'sandbox.smtp.mailtrap.io';
        $this->mail->SMTPAuth = true;
        $this->mail->Port = 2525;
        $this->mail->Username = '2d53479cc3487d';
        $this->mail->Password = '8505e8b1e71fef';
    }

    public function sendEmail(){
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $usersEmail = trim($_POST['email']);
       
        if(empty($usersEmail) ){
            flash("rest", "please fill out all inputs");
            redirect("../resetPassword.php");
        }
        if(!filter_var($usersEmail, FILTER_VALIDATE_EMAIL)){
            flash("reset", "Invalid email");
            redirect("../resetPassword.php");
        }
        
        // //used to query users from the database
        $selector = bin2hex(random_bytes(8));
        // //confirmms match of the database entry
        $token = random_bytes(32);
        $url = 'http://localhost/phpLogin/newpassword.php?selector='.$selector.'&validator='.bin2hex($token);
        // //expiration date will last half an hour
        $expires = date("U")+ 1800;
        if(!$this->resetModel->deleteEmail($usersEmail)){
            die("there was an error");
        };
        $hashedToken = password_hash($token, PASSWORD_DEFAULT);
        if(!$this->resetModel->insertToken($usersEmail, $selector, $hashedToken, $expires)){
            die("there was an error");
        }
        //can send email with the ff:
        $subject = "Reset Password";
        $message= "<p>Here is your password reset link:</p>";
        $message .="<a href='".$url."'>".$url."</a>";
        
        $this->mail->setFrom('tulani.service@gmail.com');
        $this->mail->isHTML(true);
        $this->mail->Subject = $subject;
        $this->mail->Body = $message;
        $this->mail->addAddress($usersEmail);

        $this->mail->send();

        flash("reset", "Check your email", 'form-message form-message-green');
        redirect("../resetPassword.php");
    }

    public function resetingPassword(){
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $data = [
            'selector' => trim($_POST['selector']),
            'validator' => trim($_POST['validator']),
            'pwd'=> trim($_POST['pwd']),
            'pwd-repeat'=> trim($_POST['pwd-repeat'])
        ];
        $url = '../newpassword.php?selector='.$data['selector'].'$validator='.$data['validator'];
        if(empty($_POST['pwd'] || $_POST['pwd-repeat']) ){
            flash("newpassword", "please fill out all inputs");
            redirect($url);
        }else if($data['pwd'] != $data['pwd-repeat']){
            flash("newpassword", "Password do not match");
            redirect($url);
        }else if(strlen($data['pwd']) < 6){
            flash("newpassword", "Password must be greater than 6 characters");
            redirect($url);
        }
        $currentDate = date("U");
        if(!$row = $this->resetModel->resetPassword($data['selector'], $currentDate)){
            flash("newpassword", "This link is no longer valid");
            redirect($url);
        };

        $tokenBin = hex2bin($data['validator']);
        $tokenCheck = password_verify($tokenBin, $row->pwdResetToken);
        if($tokenCheck){
            flash("newpassword", "Submit request again");
            redirect($url);
        }

        $tokenEmail = $row->pwdResetToken;
        if ($this->userModel->findUser($tokenEmail, $tokenEmail)) {
            flash("newpassword", "There was an error");
            redirect($url);
        }
        $newPwdHash = password_hash($data['pwd'], PASSWORD_DEFAULT);
        if (!$this->userModel->resetPassword($newPwdHash, $tokenEmail)) {
            flash("newpassword", "There was an error");
            redirect($url);
        }

        if (!$this->resetModel->deleteEmail($tokenEmail)) {
            flash("newpassword", "There was an error");
            redirect($url);
        }
        echo "hello1111";

        flash("newpassword", "Your password has been updated", 'form-message form-message-green');
        redirect($url);
        echo "hello";

    }
};
$initPass = new PasswordReset;
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    switch($_POST['type']){
        case 'reset':
            // echo "hello";
            $initPass->sendEmail();
            break;  
        case 'newpassword':
            $initPass->resetingPassword();
            break;         
    }
}else{
    header("../index.php");
}

?>