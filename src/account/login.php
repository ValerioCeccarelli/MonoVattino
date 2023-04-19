<? 

$current_email = "";
$current_password = "";

$error_to_display = "";

$email_error = "";
$password_error = "";

function email_check($email) {
    global $error_to_display;
    global $email_error;

    # check if the email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error_to_display = 'Please enter a valid email!';
        $email_error = 'Please enter a valid email!';

        require_once('login_template.php');
        exit;
    }
}

function password_check($password) {
    global $error_to_display;
    global $password_error;


    # check if the password is at least 8 characters long
    if (strlen($password) < 8) {

        $error_to_display = 'Please enter a valid password!';
        $password_error = 'Password must be at least 8 characters long!';

        require_once('login_template.php');
        exit;
    }
    # check if the password is at most 64 characters long
    if (strlen($password) > 64) {

        $error_to_display = 'Please enter a valid password!';
        $password_error = 'Password must be at most 64 characters long!';

        require_once('login_template.php');
        exit;
    }
    # check if the password contains at least one uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {

        $error_to_display = 'Please enter a valid password!';
        $password_error = 'Password must contain at least one uppercase letter!';

        require_once('login_template.php');
        exit;
    }
    # check if the password contains at least one lowercase letter
    if (!preg_match('/[a-z]/', $password)) {

        $error_to_display = 'Please enter a valid password!';
        $password_error = 'Password must contain at least one lowercase letter!';

        require_once('login_template.php');
        exit;
    }
    # check if the password contains at least one number
    if (!preg_match('/[0-9]/', $password)) {

        $error_to_display = 'Please enter a valid password!';
        $password_error = 'Password must contain at least one number!';

        require_once('login_template.php');
        exit;
    }
}

# if this is a GET request, display the login form
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    require_once('login_template.php');
    exit;
}

# if this is a POST request, process the login form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    # check that email and password were submitted
    if (empty($_POST['email']) || empty($_POST['password'])) {
        # I used empty() instead of isset() because it check if the variable is set and if it's not empty (since isset() doesn't work in this case)

        $error_to_display = 'Please fill both the email and password field!';
        $current_email = $_POST['email'];
        $current_password = $_POST['password'];

        require_once('login_template.php');
        exit;
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    $current_email = $email;
    $current_password = $password;

    email_check($email);
    password_check($password);

    # redirect to the home page
    header('Location: /index.php');

    exit;
}

# if this is neither a GET nor a POST request, display an error
echo 'Method not allowed';
exit;

?>