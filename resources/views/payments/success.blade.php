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
            --primary-color: #EAB308;
            --secondary-color: #FDE047;
            --dark-color: #111827;
            --text-color: #F3F4F6;
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
                radial-gradient(circle at 10% 20%, rgba(234, 179, 8, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 90% 80%, rgba(253, 224, 71, 0.1) 0%, transparent 50%);
            z-index: -1;
        }

        .floating-trains {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: -1;
            overflow: hidden;
        }

        .train-shape {
            position: absolute;
            font-size: 24px;
            color: rgba(234, 179, 8, 0.1);
            animation: float-train 20s infinite linear;
        }

        @keyframes float-train {
            0% { transform: translateX(-100%) translateY(0); }
            100% { transform: translateX(100vw) translateY(-20px); }
        }
        
        .card {
            background-color: rgba(31, 41, 55, 0.9);
            border: 2px solid var(--primary-color);
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(234, 179, 8, 0.2);
            border-radius: 1rem;
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
            text-shadow: 0 0 10px rgba(234, 179, 8, 0.3);
        }
        
        .lead {
            color: #9CA3AF;
            line-height: 1.6;
        }
        
        .btn-theme {
            background-color: var(--primary-color);
            color: var(--dark-color);
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(234, 179, 8, 0.2);
            font-weight: bold;
        }
        
        .btn-theme:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(234, 179, 8, 0.3);
        }

        .train-icon-container {
            position: relative;
            display: inline-block;
        }

        .train-track {
            width: 200px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), transparent);
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <div class="floating-trains">
        <i class="fas fa-train train-shape" style="left: 10%; animation-duration: 25s;"></i>
        <i class="fas fa-subway train-shape" style="left: 30%; animation-duration: 30s;"></i>
        <i class="fas fa-train train-shape" style="left: 60%; animation-duration: 20s;"></i>
        <i class="fas fa-subway train-shape" style="left: 80%; animation-duration: 28s;"></i>
    </div>

    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-md-6 text-center">
                <div class="card shadow">
                    <div class="card-body py-5">
                        <div class="mb-4 train-icon-container">
                            <i class="fas fa-train-subway success-icon"></i>
                            <div class="train-track"></div>
                        </div>
                        <h2 class="mt-4 text-highlight">Payment Successful!</h2>
                        <p class="lead mb-4">Your ticket has been booked successfully. Have a safe journey!</p>
                        <div class="mt-4 d-flex justify-content-center gap-3">
                            <a href="/" class="btn btn-theme">
                                <i class="fas fa-home me-2"></i>Back to Home
                            </a>
                            <a href="/tickets" class="btn btn-theme">
                                <i class="fas fa-ticket me-2"></i>View Ticket
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
