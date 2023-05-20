
<?php
require('connection.php');
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendMail($email,$v_code)
{
    require ("PHPMailer/PHPMailer.php");
    require ("PHPMailer/SMTP.php");
    require ("PHPMailer/Exception.php");
    $mail = new PHPMailer(true);
    try {
        //Server settings                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'bajrang.bs546@gmail.com';                     //SMTP username
        $mail->Password   = 'bajrang@123';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    
        //Recipients
        $mail->setFrom('bajrang.bs546@gmail.com', 'Bajrang Singh');
        $mail->addAddress($email);     //Add a recipient
        
    
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Email verification form';
        $mail->Body    = "Thanks for registration.Click link to verify the email address <a href='http://localhost/skyline/verify.php?email=$email&v_code=$v_code'>Verify</a>";
    
        $mail->send();
        return true;
    } 
    catch (Exception $e) {
        return false;
    }
}

if(isset($_POST['login']))
{
    $query="SELECT * FROM `registered_users` WHERE `email`=`$_POST[email_username]`";
    $result=mysqli_query($con,$query);
    if($result)
    {
        if(mysqli_num_rows($result)==1)
        {
            $result_fetch=mysqli_fetch_assoc($result);
            if($result_fetch['is_verified']==1)
            {
                if(password_verify($_POST['password'],$result_fetch['password']))
                {
                    $_SESSION['logged_in']=true;
                    $_SESSION['username']=$result_fetch['username'];
                    echo "
                        <script>
                            alert('Login Successful');
                            window.location.href='index.php';
                        </script>
                    ";
                    header("location: index.php");
                }
                else{
                    echo "
                        <script>
                            alert('incorrect password');
                            window.location.href='index.php';
                        </script>
                    ";
                }
            }
            else{
                echo "
                        <script>
                            alert('Email not verified');
                            window.location.href='index.php';
                        </script>
                    ";
            }
            
        }
    }
}


if(isset($_POST['register']))
{
    try{
        $temp=$_POST['email'];
        $query="SELECT * FROM `registered_users` WHERE `email`='$temp'";
        $result=mysqli_query($con,$query);
        if($result)
        {
            if(mysqli_num_rows($result)>0)
            {
                $result_fetch=mysqli_fetch_assoc($result);
                if($result_fetch['username']==$_POST['username'])
                {
                    echo "
                        <script>
                            alert('Username already taken');
                            window.location.href='index.php';
                        </script>
                    ";
                }
                else{
                    echo "
                        <script>
                            alert('email already registered');
                            window.location.href='index.php';
                        </script>
                    ";
                }
            }
            else{
                $password=password_hash($_POST['password'],PASSWORD_BCRYPT);
                $v_code=bin2hex(random_bytes(16));
                $t1=$_POST['fullname'];
                $t2=$_POST['username'];
                $t3=$_POST['email'];
                $query="INSERT INTO `registered_users`(`full_name`,`username`,`email`,`password`,`verification_code`,`is_verified`) VALUES ('$t1','$t2','$t3','$password','$v_code','0')";
                if(mysqli_query($con,$query) && sendMail($_POST['email'],$v_code))
                {
                    echo "
                        <script>
                            alert('Registration successful');
                            window.location.href='index.php';
                        </script>
                    ";
                }
                else{
                    echo "
                        <script>
                            alert('Server Down');
                            window.location.href='index.php';
                        </script>
                    ";
                }
            } 
        }
    }
    catch(Exception $e)
    {

    }
}
?>