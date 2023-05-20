<?php
    require("connection.php");
    if(isset($_GET['email']) && isset($_GET['v_code']))
    {
        $query="SELECT * FORM `registered_users` WHERE `email`='$_GET[email]' AND `verification_code`='$_GET[v_code]'";
        $result=mysqli_query($con,$query);
        if($result)
        {
            if(mysqli_num_rows($result)==1)
            {
                $result_fetch=mysqli_fetch_assoc($result);
                if($result_fetch['is_verified']==0)
                {
                    $update="UPDATE `registered_users` SET 'is_verified'='1' WHERE `email`='$result_fetch[email]'";
                    if(msqli_query($con,$update))
                    {
                        echo "
                        <script>
                            alert('Success');
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
        else{
            echo "
                        <script>
                            alert('Already Registered email');
                            window.location.href='index.php';
                        </script>
                    ";
        }
    }