<?php
session_start();

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Fresh Mart</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 450px;
            width: 100%;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .login-header p {
            color: #666;
            font-size: 14px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
            transition: transform 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fa fa-shield fa-3x" style="color: #667eea;"></i>
            <h2>Admin Login</h2>
            <p>Fresh Mart Administration Panel</p>
        </div>
        
        <?php
        if (isset($_GET['error'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> ' . htmlspecialchars($_GET['error']) . '
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                  </div>';
        }
        if (isset($_GET['success'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> ' . htmlspecialchars($_GET['success']) . '
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                  </div>';
        }
        ?>
        
        <form action="login_process.php" method="POST">
            <div class="form-group">
                <label for="username"><i class="fa fa-user"></i> Username</label>
                <input type="text" class="form-control" id="username" name="username" required placeholder="Enter username">
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fa fa-lock"></i> Password</label>
                <input type="password" class="form-control" id="password" name="password" required placeholder="Enter password">
            </div>
            
            <button type="submit" class="btn btn-login btn-block">
                <i class="fa fa-sign-in"></i> Login
            </button>
        </form>
        
        <div class="back-link">
            <a href="../index.php"><i class="fa fa-arrow-left"></i> Back to Store</a>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
</body>
</html>
