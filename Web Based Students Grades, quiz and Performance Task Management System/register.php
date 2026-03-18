<!DOCTYPE html>
<html lang="en">
    <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Registration Form</title>

  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

  <style>
    body {
      background-image: url('image/pagod.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
    }

    /* Gradient placeholder */
    .form-control::placeholder {
      color: transparent;
      background: linear-gradient(to right, #000000, #FFFFFF);
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
      font-style: italic;
      font-weight: 400;
    }

    /* Gradient buttons */
    .gradient-btn {
      background: linear-gradient(to right, #ff69b4, #fcd116);
      border: none;
      color: white;
      font-weight: bold;
      transition: 0.3s;
    }

    .gradient-btn:hover {
      opacity: 0.9;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
  </style>

  <script type="text/javascript">
    $(document).ready(function () {
      $("#register").click(function () {
        let email_address = $("#email_address").val().trim();
        let password = $("#password").val().trim();
        let first_name = $("#first_name").val().trim();
        let middle_name = $("#middle_name").val().trim();
        let last_name = $("#last_name").val().trim();

        if (email_address === "" || password === "" || first_name === "" ||
          middle_name === "" || last_name === "") {
          alert("Please complete all the fields!");
          return;
        }

        $.ajax({
          url: 'create_user.php',
          type: 'POST',
          dataType: 'json',
          data: {
            email_address: email_address,
            password: password,
            first_name: first_name,
            middle_name: middle_name,
            last_name: last_name,
          }
        })
          .done(function (response) {
            alert('Request successful: ' + response['msg']);
            $("#email_address").val("");
            $("#password").val("");
            $("#first_name").val("");
            $("#middle_name").val("");
            $("#last_name").val("");
          })
          .fail(function (jqXHR, textStatus, errorThrown) {
            alert('Error occurred: ' + textStatus + " - " + errorThrown + "\n" + jqXHR.responseText);
          });
      });
    });
  </script>
</head>

<body style="text-align:center">


<div class="container" style="margin-top:20px;">
  <div class="col-md-4 mx-auto">
     <div class="shadow-lg rounded" style="min-height: 300px; padding:30px; background-color: #FFFFFF;">
            <div style="text-align:center;">
			    <h1> Registration Form </h1>
                </div>
        <div style="text-align:left;">
  <form method="POST">
    <div class="mb-3">
       <label for="email_address" class="form-label">Email address</label>
      <input type="email" class="form-control" id="email_address" aria-describedby="emailHelp" placeholder="Email Address" required="">
    </div>
    <div class="mb-3">
       <label for="password" class="form-label">Password</label>
       <input type="password" class="form-control" id="password" placeholder="Password" required="">
    </div>
    <div class="mb-3">
       <label for="first_name" class="form-label">First Name</label>
       <input type="text" class="form-control" id="first_name" placeholder="First Name" required="">
    </div>
    <div class="mb-3">
       <label for="middle_name" class="form-label">Middle Name</label>
       <input type="text" class="form-control" id="middle_name" placeholder="Middle Name" required="">
    </div>
    <div class="mb-3">
       <label for="last_name" class="form-label">Last Name</label>
       <input type="text" class="form-control" id="last_name" placeholder="Last Name" required="">
    </div>
          <div style="text-align:center;">
      <button type="button" class="btn" id="register" style="width:300px; height:40px; font-size:20px; border-radius:50px;
        background-color:blue; color:white; border:none;">
    <i class="bi bi-person-add"></i> Register
</button>



          </div>
          <div style="text-align:center; margin-top:10px;">
          	<div style="text-align:center;"><br>
    <p style="color: blue;">
 Login here?
  <a href="index.php" style="color: red; text-decoration: underline;">login</a>
</p>
          </div>
        
      </div></form>
    </div>
  </div>

  <footer>
 
  </footer>


</div>
     </div>
   </body>
  </html>