<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4CAF50;
            --secondary-color: #81C784;
            --dark-color: #1a1a1a;
            --text-color: #E8F5E9;
        }
        
        body {
            background-color: var(--dark-color);
            color: var(--text-color);
            transition: background-color 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 10% 20%, rgba(76, 175, 80, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 90% 80%, rgba(129, 199, 132, 0.1) 0%, transparent 50%);
            z-index: -1;
        }

        .floating-shapes {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(76, 175, 80, 0.05);
            animation: float 15s infinite linear;
        }

        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); }
            100% { transform: translateY(-100vh) rotate(360deg); }
        }
        
        .card {
            background-color: rgba(45, 45, 45, 0.9);
            border: 2px solid var(--primary-color);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(76, 175, 80, 0.2);
        }
        
        .success-icon {
            color: var(--primary-color);
            font-size: 80px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }
        
        .text-highlight {
            color: var(--primary-color);
            text-shadow: 0 0 10px rgba(76, 175, 80, 0.3);
        }
        
        .lead {
            color: #B0BEC5;
            line-height: 1.6;
        }
        
        .btn-theme {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.2);
        }
        
        .btn-theme:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.3);
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape" style="left: 10%; width: 80px; height: 80px; animation-duration: 20s;"></div>
        <div class="shape" style="left: 30%; width: 60px; height: 60px; animation-duration: 25s;"></div>
        <div class="shape" style="left: 60%; width: 100px; height: 100px; animation-duration: 30s;"></div>
        <div class="shape" style="left: 80%; width: 50px; height: 50px; animation-duration: 22s;"></div>
    </div>

    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-md-6 text-center">
                <div class="card shadow">
                    <div class="card-body py-5">
                        <div class="mb-4">
                            <i class="fas fa-check-circle success-icon"></i>
                        </div>
                        <h2 class="mt-3 text-highlight">Payment Successful!</h2>
                        <p class="lead mb-4">Thank you for your purchase. Your transaction has been completed successfully.</p>
                        <div class="mt-4">
                            <a href="/" class="btn btn-theme">
                                <i class="fas fa-home me-2"></i>Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
