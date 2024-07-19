<?php /* Template Name: CustomPageT1 */ 
require_once(ABSPATH . 'wp-config.php');
date_default_timezone_set('Asia/Kolkata');
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);

$display_message="";
$error=0;
$error_message="";
$color="#000";
$droneType="none";
$password="";
$ispasswordgenerated=0;

function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);
}

function phpAlert($msg) {
    echo "<script>alert('" . $msg . "')</script>";
}

function redirect() {
    echo "<script>
    alert('You need to log in to access password utility');
    window.location.href='my-account/';
    </script>";
}

function redirectHome() {
    echo "<script>
    alert('Password Utility is not available right now, please try after some time');
    window.location.href='/';
    </script>";
}

function validateSSID($ssid) {
    global $error;
    global $error_message;
    global $droneType;  
    switch(strlen($ssid)) {
        case 15:
            if((substr($ssid, 0, 6) === "Pluto_") && is_numeric(substr($ssid, 6, 4)) && (substr($ssid, 10, 1) === "_") && is_numeric(substr($ssid, 11, 4))){
                debug_to_console("string starts with pluto and valid");
                $droneType="pluto";
            } else if((substr($ssid, 0, 11) === "GURU_DRONE_") && is_numeric(substr($ssid, 11, 4))) {
                debug_to_console("string starts with guru and valid");
                $droneType="guru";
            } else {
                $error=1;
                $error_message="invalid ssid";
                debug_to_console("string doesnt starts with pluto or guru or invalid");
            }
            break;
        case 16:
            if((substr($ssid, 0, 7) === "PlutoX_") && is_numeric(substr($ssid, 7, 4)) && (substr($ssid, 11, 1) === "_") && is_numeric(substr($ssid, 12, 4))){
                debug_to_console("string starts with plutox and valid");
                $droneType="plutox";
            } else {
                $error=1;
                $error_message="invalid ssid";
                debug_to_console("string doesnt starts with plutox or invalid");
            }
            break;
        default:
            $error=1;
            $error_message="invalid ssid";
            debug_to_console("invalid string ssid");
            break;
    }
}

function validateKey($key) {
    global $error;
    global $error_message;
    if(empty($key)){
        $error=1;
        $error_message="invalid key";
        debug_to_console("empty key");
    } else {
        debug_to_console("valid key");
    }
}

function validateBoughtFrom($boughtfrom) {
    global $error;
    global $error_message;
    if($boughtfrom==="none") {
        $error=1;
        $error_message="please select bought from option";
        debug_to_console("invalid source");
    } else {
        debug_to_console("valid source");
    }
}

function matchKeyandSSID($key, $ssid) {
    global $error;
    global $error_message;
    global $droneType; 
    debug_to_console("in keymatch");
    debug_to_console($droneType);
    switch($droneType) {
        case "pluto":
            if((int)$key===(((int)substr($ssid, 6, 4)+(int)substr($ssid, 11, 4)))) {
                debug_to_console("pluto key match");
            } else {
                $error=1;
                $error_message="key validation failed: check key or ssid";
                debug_to_console("pluto key mismatch");
            }
            break;
        case "plutox":
            if((int)$key===(((int)substr($ssid, 7, 4)+(int)substr($ssid, 12, 4)))) {
                debug_to_console("plutox key match");
            } else {
                $error=1;
                $error_message="key validation failed: check key or ssid";   
                debug_to_console("plutox key mismatch");
            }
            break;
        case "guru":
            if((int)$key===((2000-(int)substr($ssid, 11, 4)))) {
                debug_to_console("guru key match");
            } else {
                $error=1;
                $error_message="key validation failed: check key or ssid";   
                debug_to_console("plutox key mismatch");
            }
            break;
        default:
            $error=1;
            $error_message="key validation failed: check key or ssid";
            break;
    }
}

function generatePassword($key) {
    global $error;
    global $error_message;
    global $droneType; 
    global $password;
    global $ispasswordgenerated;
    debug_to_console("in passgen");
    debug_to_console($droneType);
    switch($droneType) {
        case "pluto":
            if ((int) $key & 1 ) {
                $password=$key."pluto";
                $ispasswordgenerated=1;
            } else {
                $password="pluto".$key;
                $ispasswordgenerated=1;
            }
            break;
        case "plutox":
            if ((int) $key & 1 ) {
                $password=$key."plutox";
                $ispasswordgenerated=1;
            } else {
                $password="plutox".$key;
                $ispasswordgenerated=1;
            }
            break;
        case "guru":
            if ((int) $key & 1 ) {
                $password="droneguru".$key;
                $ispasswordgenerated=1;
            } else {
                $password="gurudrone".$key;
                $ispasswordgenerated=1;
            }
            break;
        default:
            $error=1;
            $error_message="unable to generate password, please contact pluto support";
            break;
    }
}

// Check connection
if (!$connection) {
    debug_to_console("Cant connect to db");
    redirectHome();
} else {
    debug_to_console("connected to db");
    debug_to_console(get_current_user_id());
    mysqli_select_db($connection, DB_NAME);
}

$usrid=get_current_user_id();
global $current_user;
get_currentuserinfo();
$email = (string) $current_user->user_email;
$name = (string) $current_user->display_name;
$display_message="your password will be displayed here";

if($usrid<1){
    redirect();
}
?>

<?php get_header(); ?>
<style scoped>
.password-gen-div {
    width: 600px;
    margin: auto;
    padding: 20px;
    border: solid 2px #333333;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.password-gen-div h2 {
    background-color: #333333;
    color: #FFFFFF;
    padding: 10px;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
    text-align: center;
    margin: -20px -20px 20px -20px;
}

.password-gen-div label {
    display: block;
    margin: 10px 0 5px;
    font-weight: bold;
}

.password-gen-div input[type="text"],
.password-gen-div select {
    width: 100%;
    padding: 10px;
    margin: 5px 0 20px;
    border: solid 1px #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

.password-gen-div input[type="submit"] {
    width: 100%;
    padding: 10px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

.password-gen-div input[type="submit"]:hover {
    background-color: #0056b3;
}

.message {
    font-size: 20px;
    margin-top: 20px;
}
</style>
<script>
function showAlert(message) {
    alert(message);
}
</script>

<div id="primary" class="content-area">
    <div class="password-gen-div">
        <h2>Get Your Drone's Password</h2>
        <?php
            if(isset($_POST['submit'])) { //check if form was submitted
                $ssid = mysqli_real_escape_string($connection,$_POST['dronessid']);
                $key = mysqli_real_escape_string($connection,$_POST['key']);
                $boughtfrom = mysqli_real_escape_string($connection,$_POST['bought_from']);

                validateSSID($ssid);        
                if($error==0) validateKey($key);
                if($error==0) validateBoughtFrom($boughtfrom);
                if($error==0) matchKeyandSSID($key, $ssid);
                if($error==0) generatePassword($key);

                if($error==0) {
                    $now = new DateTime();
                    $datetime=$now->format('Y-m-d H:i:s');
                    $sql = "INSERT INTO password_util_entries VALUES ('$usrid','$name','$email','$ssid','$boughtfrom','$datetime')";
                    if ($connection->query($sql) === TRUE) {
                        $color="#008000";               
                        $display_message="Password of ".$ssid." is: ".$password;    
                        echo "<script>showAlert('Password is: ".$password."\\nFor SSID: ".$ssid."');</script>";
                    } else {
                        echo "Error: " . $sql . "<br>" . $connection->error;
                        $color="#FF0000";  
                        $display_message="password generation failed, please contact pluto support";
                    }
                    $connection->close();
                } else {
                    $color="#FF0000";  
                    $display_message=$error_message;
                }    
            } 
        ?>            

        <div>
            <form action="" method="post">
                <label>DRONE SSID:</label>
                <input type="text" name="dronessid" required style="background-color:#f0f0f0;border:1px solid #ccc;"/>

                <label>AUTH KEY:</label>
                <input type="text" name="key" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" maxlength="4" required style="background-color:#f0f0f0;border:1px solid #ccc;"/>

                <label>BOUGHT FROM:</label>
                <select name="bought_from" required style="background-color:#f0f0f0;border:1px solid #ccc;">
                    <option value="none"></option>
                    <option value="amazon">Amazon</option>
                    <option value="website">Website</option>
                    <option value="vendor">Vendor</option>
                    <option value="other">Other</option>
                </select>
                <input type="submit" value="Generate BIkram" name="submit"/><br />
            </form>
            <div class="message" style="color:<?php echo $color;?>;"><?php echo $display_message; ?></div>
        </div>
    </div>
</div>

<?php get_footer(); ?>
