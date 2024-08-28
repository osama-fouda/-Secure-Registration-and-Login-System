<?php
    include "layouts/header.php";


    if (isset($_SESSION["email"])){
      header("location: /index.php");
      exit;
    }

    $first_name = "";
    $last_name = "";
    $email = "";
    $phone = "";
    $address = "";
    $password = "";
    $confirm_password = "";

    $first_name_error = "";
    $last_name_error = "";
    $email_error = "";
    $phone_error = "";
    $address_error = "";
    $password_error = "";
    $confirm_password_error = "";
    $error = false;

    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $first_name = $_POST['first_name'] ?? '';
        $last_name = $_POST['last_name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';



        // Validate first name
        if (empty($first_name)) {
            $first_name_error = "First name is required";
            $error = true;
        }

        // Validate last name
        if (empty($last_name)) {
        $last_name_error = "Last name is required";
        $error = true;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_error = "Email format is not valid";
            $error = true;
        }
        include "tools/db.php";
        $dbConnection = getDatabaseConnection();

        // Prepare the SQL statement
        $statement = $dbConnection->prepare("SELECT id FROM users WHERE email = ?");

        // Bind variables to the prepared statement as parameters
        $statement->bind_param("s", $email);

        // Execute the statement
        $statement->execute();

        // Check if email is already in the database
        $statement->store_result();

// Check if any rows are returned
        if ($statement->num_rows > 0) {
          $email_error = "Email is already used";
          $error = true;
        }

// Close the statement
        $statement->close();


        
        // Validate phone number
        // Define a regex for phone format
        // Optional country code (+ or 00 followed by 1 to 3 digits)
        // Optional space or dash separator
        // Number (7 to 12 digits)
        $phone_regex = '/^(\+|00\d{1,3})?[- ]?\d{7,12}$/';
        if (!preg_match($phone_regex, $phone)) {
            $phone_error = "Phone format is not valid";
            $error = true;
        }

        // Validate password
        if (strlen($password) < 6) {
            $password_error = "Password must have at least 6 characters";
            $error = true;
        }

        // Validate confirm password
        if ($confirm_password !== $password) {
            $confirm_password_error = "Password and Confirm Password do not match";
            $error = true;
        }

        if(!$error){
          $password = password_hash($password, PASSWORD_DEFAULT);
          $created_at = date('Y-m-d H:i:s');

          $sql = "INSERT INTO users (first_name, last_name, email, phone, address, password, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)";
          $statement = $dbConnection->prepare($sql);

           // Check if prepare() was successful
            if ($statement === false) {
              die("Error preparing the SQL statement: " . $dbConnection->error);
            }

          // Bind variables to the prepared statement as parameters
          $statement->bind_param('sssssss', $first_name, $last_name, $email, $phone, $address, $password, $created_at);

          // Execute the statement
          $statement->execute();

          // Get the last inserted ID
          $insert_id = $statement->insert_id;

          // Close the statement
          $statement->close();

      // Save session data
      $_SESSION["id"] = $insert_id;
      $_SESSION["first_name"] = $first_name;
      $_SESSION["last_name"] = $last_name;
      $_SESSION["email"] = $email;
      $_SESSION["phone"] = $phone;
      $_SESSION["address"] = $address;
      $_SESSION["created_at"] = $created_at;    //no password for more security

// Redirect user to the home page
      header("Location: /index.php");
      exit;
        }
    }


?>

<div class="container py-5">
  <div class="row">
    <div class="col-lg-6 mx-auto border shadow p-4">
      <h2 class="text-center mb-4">Register</h2>
      <form method="post">
      <div class="row mb-3">
      <label class="col-sm-4 col-form-label">First Name*</label>
      <div class="col-sm-8">
      <input type="text" class="form-control" name="first_name" value="<?= $first_name ?>" />
      <span class="text-danger"><?= $first_name_error  ?></span>
    </div>
  </div>
  <div class="row mb-3">
  <label class="col-sm-4 col-form-label">Last Name*</label>
  <div class="col-sm-8">
    <input type="text" class="form-control" name="last_name" value="<?= $last_name ?>" />
    <span class="text-danger"><?= $last_name_error  ?></span>
  </div>
</div>

<div class="row mb-3">
  <label class="col-sm-4 col-form-label">Email*</label>
  <div class="col-sm-8">
    <input type="email" class="form-control" name="email" value="<?= $email ?>" />
    <span class="text-danger"><?= $email_error  ?></span>
  </div>
</div>
<div class="row mb-3">
  <label class="col-sm-4 col-form-label">Phone*</label>
  <div class="col-sm-8">
    <input type="text" class="form-control" name="phone" value="<?= $phone ?>" />
    <span class="text-danger"><?= $phone_error  ?></span>
  </div>
</div>

<div class="row mb-3">
  <label class="col-sm-4 col-form-label">Address</label>
  <div class="col-sm-8">
    <input type="text" class="form-control" name="address" value="<?= $address ?>" />
    <span class="text-danger"><?= $address_error  ?></span>
  </div>
</div>

<div class="row mb-3">
  <label class="col-sm-4 col-form-label">Password*</label>
  <div class="col-sm-8">
    <input class="form-control" type="password" name="password" />
    <span class="text-danger"><?= $password_error  ?></span>
  </div>
</div>

<div class="row mb-3">
  <label class="col-sm-4 col-form-label">Confirm Password*</label>
  <div class="col-sm-8">
    <input class="form-control" type="password" name="confirm_password" />
    <span class="text-danger"><?= $confirm_password_error  ?></span>
  </div>
</div>

<div class="row mb-3">
  <div class="offset-sm-4 col-sm-4 d-grid">
    <button type="submit" class="btn btn-primary">Register</button>
  </div>
  <div class="col-sm-4 d-grid">
    <a href="/index.php" class="btn btn-outline-primary">Cancel</a>
  </div>
</div>

  </form>

    </div>
  </div>
</div>
<?php
include "layouts/footer.php";
?>