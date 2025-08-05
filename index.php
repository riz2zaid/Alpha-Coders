<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkyLink Management - Sign In</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f5f5f5;
            padding: 20px;
        }

        .signin-container {
            position: relative;
            width: 100%;
            max-width: 900px;
            height: 500px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
        }

        .signin-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(0deg, transparent, 
                #3e64ff, #3e64ff);
            transform-origin: bottom right;
            animation: animate 6s linear infinite;
        }

        .signin-container::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(0deg, transparent, 
                #3e64ff, #3e64ff);
            transform-origin: bottom right;
            animation: animate 6s linear infinite;
            animation-delay: -3s;
        }

        @keyframes animate {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        .signin-box {
            position: absolute;
            inset: 4px;
            background: #fff;
            border-radius: 8px;
            z-index: 10;
            display: flex;
        }

        .image-section {
            flex: 1;
            background: url('../image/flight.png') center/cover no-repeat;
            display: none;
        }

        .form-section {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        h2 {
            color: #3e64ff;
            margin-bottom: 30px;
            font-size: 28px;
            text-align: center;
        }

        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            outline: none;
            transition: all 0.3s;
        }

        .input-group input:focus {
            border-color: #3e64ff;
            box-shadow: 0 0 5px rgba(62, 100, 255, 0.5);
        }

        .input-group label {
            position: absolute;
            top: 12px;
            left: 10px;
            color: #999;
            pointer-events: none;
            transition: all 0.3s;
        }

        .input-group input:focus + label,
        .input-group input:valid + label {
            top: -10px;
            left: 10px;
            font-size: 12px;
            background: #fff;
            padding: 0 5px;
            color: #3e64ff;
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 20px;
        }

        .forgot-password a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }

        .forgot-password a:hover {
            color: #3e64ff;
        }

        .signin-btn {
            background: #3e64ff;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
            font-weight: 600;
        }

        .signin-btn:hover {
            background: #2a4fc7;
        }

        @media (min-width: 768px) {
            .image-section {
                display: block;
            }
            
            .signin-box {
                flex-direction: row;
            }
        }

        @media (max-width: 767px) {
            .signin-container {
                height: auto;
                max-width: 500px;
            }
            
            .form-section {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="signin-container">
        <div class="signin-box">
            <div class="image-section"></div>
            <div class="form-section">
                <h2>Sign In</h2>
                <form id="signinForm">
                    <div class="input-group">
                        <input type="text" id="username" name="username" required>
                        <label for="username">Username</label>
                    </div>
                    <div class="input-group">
                        <input type="password" id="password" name="password" required>
                        <label for="password">Password</label>
                    </div>
                   
                    <button type="submit" class="signin-btn">Sign In</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal (hidden by default) -->
    <div id="forgotPasswordModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
        <div style="background:white; padding:30px; border-radius:10px; width:90%; max-width:400px;">
            <h3 style="margin-bottom:20px; color:#3e64ff;">Change Password</h3>
            <form id="changePasswordForm">
                <div class="input-group">
                    <input type="text" id="resetUsername" name="username" required>
                    <label for="resetUsername">Username</label>
                </div>
                <div class="input-group">
                    <input type="password" id="newPassword" name="newPassword" required>
                    <label for="newPassword">New Password</label>
                </div>
                <div class="input-group">
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                    <label for="confirmPassword">Confirm Password</label>
                </div>
                <div style="display:flex; justify-content:space-between; margin-top:20px;">
                    <button type="button" id="cancelReset" style="background:#ccc; color:#333; padding:10px 20px; border:none; border-radius:4px; cursor:pointer;">Cancel</button>
                    <button type="submit" style="background:#3e64ff; color:white; padding:10px 20px; border:none; border-radius:4px; cursor:pointer;">Change Password</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('signinForm').addEventListener('submit', function(e) {
            e.preventDefault();
            clientSignin();
        });

        document.getElementById('forgotPasswordLink').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('forgotPasswordModal').style.display = 'flex';
        });

        document.getElementById('cancelReset').addEventListener('click', function() {
            document.getElementById('forgotPasswordModal').style.display = 'none';
        });

        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            changePassword();
        });

        function clientSignin() {
            var username = document.getElementById("username").value;
            var password = document.getElementById("password").value;

            if (!username || !password) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Please fill in all fields',
                    icon: 'error',
                    confirmButtonColor: '#3e64ff',
                    width: 300
                });
                return;
            }

            var formData = new FormData();
            formData.append("u", username);
            formData.append("p", password);

            fetch("clientLoginProcess.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(response => {
                if (response === "Success") {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Login successful',
                        icon: 'success',
                        confirmButtonColor: '#3e64ff',
                        width: 300
                    }).then(() => {
                        window.location.href = "clientDashboard.php";
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response,
                        icon: 'error',
                        confirmButtonColor: '#3e64ff',
                        width: 300
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#3e64ff',
                    width: 300
                });
            });
        }

        function changePassword() {
            var username = document.getElementById("resetUsername").value;
            var newPassword = document.getElementById("newPassword").value;
            var confirmPassword = document.getElementById("confirmPassword").value;

            if (!username || !newPassword || !confirmPassword) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Please fill in all fields',
                    icon: 'error',
                    confirmButtonColor: '#3e64ff',
                    width: 300
                });
                return;
            }

            if (newPassword !== confirmPassword) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Passwords do not match',
                    icon: 'error',
                    confirmButtonColor: '#3e64ff',
                    width: 300
                });
                return;
            }

            var formData = new FormData();
            formData.append("username", username);
            formData.append("newPassword", newPassword);

            fetch("changePasswordProcess.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(response => {
                if (response === "Success") {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Password changed successfully',
                        icon: 'success',
                        confirmButtonColor: '#3e64ff',
                        width: 300
                    }).then(() => {
                        document.getElementById('forgotPasswordModal').style.display = 'none';
                        document.getElementById('changePasswordForm').reset();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response,
                        icon: 'error',
                        confirmButtonColor: '#3e64ff',
                        width: 300
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#3e64ff',
                    width: 300
                });
            });
        }
    </script>
</body>
</html>