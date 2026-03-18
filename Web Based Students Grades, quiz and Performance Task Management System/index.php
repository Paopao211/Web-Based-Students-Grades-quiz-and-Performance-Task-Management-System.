<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Carwah Booking</title>

   <!-- Bootstrap CSS -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

   <!-- Bootstrap Icons -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

   <!-- jQuery -->
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

   <style>
     body {
        background-image: url('image/back.webp');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        animation: fadeIn 1s ease-in-out;
     }

     /* Fade-in effect for whole page */
     @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
     }

     /* Slide-in animation for login box */
     .login-box {
        animation: slideUp 0.9s ease-out;
     }

     @keyframes slideUp {
        from {
          transform: translateY(50px);
          opacity: 0;
        }
        to {
          transform: translateY(0px);
          opacity: 1;
        }
     }

     /* Login button glow animation */
     .btn-glow {
        animation: glowing 2s infinite;
     }

     @keyframes glowing {
        0% { box-shadow: 0 0 5px #ff0000; }
        50% { box-shadow: 0 0 20px #ff6600; }
        100% { box-shadow: 0 0 5px #ff0000; }
     }

   </style>

<script type="text/javascript">
  $(document).ready(function(){
    $("#login").click(function(){
      let email_address = $("#email_address").val();
      let password = $("#password").val();

      $.ajax({
        url: 'login_user_ajax.php',
        type: 'POST',
        dataType: 'json',
        data: {
          email_address: email_address,
          password: password
        }
      })
      .done(function(response) {
        if (response['isValidUser'] === 1) {
          alert('Login successful: ' + response['msg']);
          window.location.href = 'login.php';
        } else {
          alert('Login Failed: ' + response['msg']);
        }
      })
      .fail(function(jqXHR, textStatus, errorThrown) {
        alert('Error occurred: ' + jqXHR.statusText + ' ' + jqXHR.responseText);
      });
    });
  });
</script>

</head>
<body>
  
<div class="container">
   <div class="col-md-4 mx-auto">
      <div class="shadow-lg rounded login-box" style="min-height: 300px; padding:30px; background: rgba(255,255,255,0.8); margin-top:50px;">
        
        <div style="text-align:center;">
         <img src="image/logo.png" style="width:100px;height:100px; animation: fadeIn 1s;">

          <h1 style="animation: fadeIn 1.5s;">Login</h1>
        </div>

        <form method="POST">
          <div class="mb-4">
            <label for="email_address" class="form-label">Email Address</label>
            <input type="email" placeholder="Email or Phone Number" required class="form-control" style="border-radius: 50px;" id="email_address">
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" placeholder="Password" required class="form-control" style="border-radius: 50px;" id="password">
           
          </div>	

          <div class="d-flex justify-content-center mt-1">
           <button type="button" class="btn btn-primary me-2" id="login" 
   style="width:300px; height:40px; font-size:20px; border-radius:50px;
        background-color:blue; color:white; border:none;">
  <i class="bi bi-person-plus-fill"></i> Login
</button>

          </div>

          <div class="text-center mt-4">
            <p style="color:black;">
              Don’t have an account?
              <a href="register.php" style="color:red; text-decoration:underline;">Create Account</a>
            </p>
          </div>

        </form>

      </div>
   </div>
</div>

<footer>
  
</footer>

</body>
</html>
