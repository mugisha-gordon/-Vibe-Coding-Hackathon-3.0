<?php
require_once "config/database.php";
session_start();

// Handle amount selection
if(isset($_GET['amount'])) {
    $_SESSION['donation_amount'] = $_GET['amount'];
    header("Location: donate_details.php");
    exit();
}

// Handle custom amount submission
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = trim($_POST["amount"]);
    if(!empty($amount) && is_numeric($amount) && $amount > 0) {
        $_SESSION['donation_amount'] = $amount;
        header("Location: donate_details.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Amount - Bumbobi Child Support Uganda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/donation.css">
    <style>
        .donation-amounts {
            padding: 80px 0;
        }
        .amount-card {
            border: 2px solid var(--light-bg);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            transition: var(--transition);
            cursor: pointer;
        }
        .amount-card:hover, .amount-card.selected {
            border-color: var(--primary-color);
            transform: translateY(-5px);
        }
        .amount-card.selected {
            background: var(--light-bg);
        }
        .amount {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        .custom-amount {
            max-width: 400px;
            margin: 0 auto;
        }
        .custom-amount input {
            border-radius: 30px;
            padding: 15px 25px;
            font-size: 1.2rem;
        }
        .progress-tracker {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            position: relative;
        }
        .progress-tracker::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--light-bg);
            z-index: 1;
        }
        .progress-step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 2px solid var(--light-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
        }
        .progress-step.active {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        .progress-step.completed {
            background: var(--secondary-color);
            border-color: var(--secondary-color);
            color: white;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <!-- Progress Tracker -->
        <div class="progress-tracker">
            <div class="progress-step completed">
                <i class="fas fa-check"></i>
            </div>
            <div class="progress-step active">
                <span>2</span>
            </div>
            <div class="progress-step">
                <span>3</span>
            </div>
            <div class="progress-step">
                <span>4</span>
            </div>
        </div>

        <div class="donation-amounts">
            <h2>Select Your Gift Amount</h2>
            
            <!-- Quick Amounts -->
            <div class="row justify-content-center mb-5">
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="amount-card" onclick="selectAmount(50)">
                        <div class="amount">$50</div>
                        <p>Provide school supplies for a child</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="amount-card" onclick="selectAmount(100)">
                        <div class="amount">$100</div>
                        <p>Fund a month of education</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="amount-card" onclick="selectAmount(250)">
                        <div class="amount">$250</div>
                        <p>Support healthcare for several children</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="amount-card" onclick="selectAmount(500)">
                        <div class="amount">$500</div>
                        <p>Provide nutritious meals for a year</p>
                    </div>
                </div>
                 <div class="col-md-4 col-sm-6 mb-4">
                    <div class="amount-card" onclick="selectAmount(1000)">
                        <div class="amount">$1000</div>
                        <p>Sponsor a child's full year of needs</p>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-4">
                    <div class="amount-card" onclick="selectAmount(2500)">
                        <div class="amount">$2500</div>
                        <p>Support a community project</p>
                    </div>
                </div>
            </div>

            <!-- Custom Amount -->
            <div class="custom-amount">
                <h3 class="text-center mb-4">Or Choose Your Own Amount</h3>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="input-group mb-3">
                        <span class="input-group-text">$</span>
                        <input type="number" class="form-control" name="amount" min="1" step="any" placeholder="Enter custom amount" required>
                        <button type="submit" class="btn btn-primary">Continue</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Loading animation - remove or adapt if not needed with new design
        window.addEventListener('load', () => {
            const loading = document.querySelector('.loading');
            if (loading) {
                loading.style.display = 'none';
            }
        });

        // Amount selection
        function selectAmount(amount) {
            window.location.href = `donate_details.php?amount=${amount}`;
        }

        // Highlight selected amount (optional - can be done with URL param on load)
        const urlParams = new URLSearchParams(window.location.search);
        const selectedAmount = urlParams.get('amount');
        if (selectedAmount) {
            document.querySelectorAll('.amount-card').forEach(card => {
                if (card.getAttribute('onclick').includes(`selectAmount(${selectedAmount})`)) {
                    card.classList.add('selected');
                }
            });
        }
    </script>
</body>
</html> 