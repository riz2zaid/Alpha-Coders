<?php
function getSubscriptionPlans()
{
  $servername = "localhost";
  $username = "root";
  $password = "N200311.";
  $dbname = "skylink_main";

  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $sql = "SELECT * FROM subscription_plans WHERE is_active = 1";
  $result = $conn->query($sql);

  $plans = array();
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $plans[] = $row;
    }
  }
  $conn->close();
  return $plans;
}

function getPaymentGateways()
{
  $servername = "localhost";
  $username = "root";
  $password = "N200311.";
  $dbname = "skylink_main";

  $conn = new mysqli($servername, $username, $password, $dbname);

  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  $sql = "SELECT id, gatway_name, gatway_icon, gatway_image, link FROM payment_gatway WHERE is_active = 1";
  $result = $conn->query($sql);

  $gateways = array();
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $gateways[] = $row;
    }
  }
  $conn->close();
  return $gateways;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Request POS - DSS</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="icon" href="../image/logo/icon.png" type="image/x-icon">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body,
    html {
      height: 100%;
      font-family: 'Poppins', sans-serif;
      overflow-x: hidden;
    }

    .container {
      display: flex;
      height: 100vh;
      width: 100%;
      flex-direction: row;
      position: relative;
    }

    /* Left Panel (75%) - Image Slider */
    .left-panel {
      flex: 3;
      position: relative;
      overflow: hidden;
    }

    .swiper {
      width: 100%;
      height: 100%;
    }

    .swiper-slide {
      display: flex;
      align-items: center;
      justify-content: center;
      background: #f5f5f5;
    }

    .swiper-slide img {
      display: block;
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    /* Right Panel (25%) - Request Details */
    .right-panel {
      flex: 1;
      background: #ffffff;
      display: flex;
      flex-direction: column;
      overflow-y: auto;
      padding: 40px 30px;
      box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .acc-box {
      width: 100%;
      padding: 20px;
      border: 2px solid #e0e0e0;
      border-radius: 12px;
      background: #f9f9f9;
      margin-bottom: 25px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .acc-box:hover {
      border-color: #3e64ff;
      box-shadow: 0 6px 16px rgba(62, 100, 255, 0.1);
    }

    .acc-detail {
      display: flex;
      margin-bottom: 12px;
      align-items: baseline;
      flex-wrap: wrap;
    }

    .acc-detail:last-child {
      margin-bottom: 0;
    }

    .acc-label {
      font-weight: 500;
      color: #555;
      min-width: 140px;
      margin-right: 10px;
      font-size: 14px;
    }

    .acc-value {
      font-weight: 600;
      color: #333;
      flex: 1;
      word-break: break-word;
    }

    .form-box {
      width: 100%;
    }

    .form-box h2 {
      font-weight: 600;
      margin-bottom: 25px;
      color: #333;
      font-size: 1.5rem;
    }

    .input-box {
      position: relative;
      margin-bottom: 25px;
    }

    .input-box input,
    .input-box select,
    .input-box textarea {
      width: 100%;
      padding: 12px;
      font-size: 15px;
      border: none;
      border-bottom: 2px solid #ccc;
      outline: none;
      background: transparent;
    }

    .input-box textarea {
      resize: vertical;
      min-height: 100px;
    }

    .input-box label {
      position: absolute;
      top: 12px;
      left: 0;
      pointer-events: none;
      color: #999;
      transition: 0.3s;
    }

    .input-box input:focus~label,
    .input-box input:valid~label,
    .input-box select:focus~label,
    .input-box select:valid~label,
    .input-box textarea:focus~label,
    .input-box textarea:valid~label {
      top: -10px;
      font-size: 13px;
      color: #3e64ff;
    }

    .btn {
      width: 100%;
      padding: 12px;
      background: #3e64ff;
      color: #fff;
      border: none;
      font-weight: 500;
      border-radius: 5px;
      cursor: pointer;
      transition: 0.3s;
      margin-top: 10px;
    }

    .btn:hover {
      background: #324ddb;
    }

    .form-section {
      margin-bottom: 30px;
    }

    .form-section h3 {
      font-size: 1.1rem;
      margin-bottom: 15px;
      color: #444;
      border-bottom: 1px solid #eee;
      padding-bottom: 8px;
    }

    /* File Upload Styles */
    .file-upload-box {
      margin-bottom: 25px;
      position: relative;
      z-index: 1;
    }

    .file-upload-label {
      display: block;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .file-upload-input {
      position: absolute;
      width: 100%;
      height: 100%;
      opacity: 0;
      cursor: pointer;
      left: 0;
      top: 0;
    }

    .file-upload-content {
      border: 2px dashed #ccc;
      border-radius: 8px;
      padding: 30px 20px;
      text-align: center;
      background-color: #f9f9f9;
      transition: all 0.3s ease;
      position: relative;
      z-index: 0;
    }

    .file-upload-label:hover .file-upload-content,
    .file-upload-input:focus+.file-upload-content {
      border-color: #3e64ff;
      background-color: #f0f4ff;
    }

    .file-upload-icon {
      width: 48px;
      height: 48px;
      margin-bottom: 12px;
      color: #666;
    }

    .file-upload-text {
      display: block;
      font-weight: 500;
      color: #333;
      margin-bottom: 6px;
      font-size: 16px;
    }

    .file-upload-hint {
      display: block;
      color: #777;
      font-size: 13px;
    }

    .file-upload-preview {
      margin-top: 15px;
      display: none;
      text-align: center;
    }

    /* When file is selected */
    .file-upload-input:valid+.file-upload-content {
      border-style: solid;
      background-color: #e8f5e9;
      border-color: #4caf50;
    }

    .file-upload-input:valid+.file-upload-content .file-upload-icon {
      color: #4caf50;
    }

    /* Preview styles when file is selected */
    .file-upload-preview img {
      max-width: 100%;
      max-height: 200px;
      border-radius: 4px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .file-upload-preview .file-info {
      margin-top: 10px;
      font-size: 14px;
      color: #555;
    }

    /* Checkbox styles */
    .checkbox-container {
      display: flex;
      align-items: center;
      position: relative;
      padding-left: 30px;
      cursor: pointer;
      user-select: none;
      font-size: 14px;
      margin-bottom: 20px;
    }

    .checkbox-container input {
      position: absolute;
      opacity: 0;
      cursor: pointer;
      height: 0;
      width: 0;
    }

    .checkmark {
      position: absolute;
      left: 0;
      height: 18px;
      width: 18px;
      background-color: #fff;
      border: 2px solid #3e64ff;
      border-radius: 4px;
    }

    .checkbox-container:hover input~.checkmark {
      background-color: #f0f4ff;
    }

    .checkbox-container input:checked~.checkmark {
      background-color: #3e64ff;
    }

    .checkmark:after {
      content: "";
      position: absolute;
      display: none;
    }

    .checkbox-container input:checked~.checkmark:after {
      display: block;
    }

    .checkbox-container .checkmark:after {
      left: 5px;
      top: 1px;
      width: 5px;
      height: 10px;
      border: solid white;
      border-width: 0 2px 2px 0;
      transform: rotate(45deg);
    }

    .checkbox-label {
      margin-left: 8px;
      color: #333;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .container {
        flex-direction: column;
        height: auto;
      }

      .left-panel {
        flex: none;
        height: 50vh;
      }

      .right-panel {
        flex: none;
        height: auto;
        padding: 30px 20px;
        overflow-y: visible;
      }

      .form-box {
        max-width: 100%;
      }

      .acc-label {
        min-width: 120px;
      }
    }

    @media (max-width: 576px) {
      .file-upload-content {
        padding: 20px 15px;
      }

      .file-upload-icon {
        width: 36px;
        height: 36px;
      }

      .file-upload-text {
        font-size: 14px;
      }

      .file-upload-hint {
        font-size: 12px;
      }

      .acc-detail {
        flex-direction: column;
      }

      .acc-label {
        min-width: 100%;
        margin-bottom: 5px;
      }

      .checkbox-container {
        font-size: 13px;
      }

      .checkmark {
        height: 16px;
        width: 16px;
      }
    }

    /* Plans Section Styles */
    .plans-container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-bottom: 30px;
      justify-content: center;
    }

    .plan-card {
      flex: 1;
      min-width: 280px;
      max-width: 320px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      padding: 25px;
      transition: all 0.3s ease;
      position: relative;
      border: 1px solid #e0e0e0;
    }

    .plan-card.active-plan {
      border: 2px solid #3e64ff;
      background-color: #f8faff;
    }

    /* Rest of your existing plan card styles... */
    /* Plans Section Styles */
    .plans-container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin-bottom: 30px;
    }

    .plan-card {
      flex: 1;
      min-width: 250px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      padding: 25px;
      transition: all 0.3s ease;
      position: relative;
      border: 1px solid #e0e0e0;
    }

    .plan-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
      border-color: #3e64ff;
    }

    .plan-header {
      text-align: center;
      margin-bottom: 20px;
      padding-bottom: 20px;
      border-bottom: 1px solid #eee;
    }

    .plan-header h3 {
      font-size: 1.2rem;
      color: #333;
      margin-bottom: 10px;
    }

    .plan-price {
      font-size: 1.8rem;
      font-weight: 700;
      color: #3e64ff;
    }

    .plan-price span {
      font-size: 0.9rem;
      font-weight: 500;
      color: #777;
    }

    .plan-features {
      margin-bottom: 25px;
    }

    .plan-features ul {
      list-style: none;
    }

    .plan-features li {
      padding: 8px 0;
      color: #555;
      position: relative;
      padding-left: 25px;
    }

    .plan-features li:before {
      content: "✓";
      color: #4caf50;
      position: absolute;
      left: 0;
    }

    .plan-footer {
      text-align: center;
    }

    .select-plan-btn {
      width: 100%;
      padding: 12px;
      background: #3e64ff;
      color: white;
      border: none;
      border-radius: 6px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .select-plan-btn:hover {
      background: #324ddb;
    }

    /* Popular Plan Styling */
    .plan-card.popular {
      border: 2px solid #3e64ff;
    }

    .popular-badge {
      position: absolute;
      top: -12px;
      right: 20px;
      background: #3e64ff;
      color: white;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
    }

    /* Responsive Plans */
    @media (max-width: 900px) {
      .plans-container {
        flex-direction: column;
      }

      .plan-card {
        min-width: 100%;
      }
    }

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
      background-color: #fefefe;
      margin: 5% auto;
      padding: 25px;
      border-radius: 12px;
      width: 80%;
      max-width: 800px;
      max-height: 80vh;
      overflow-y: auto;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
    }

    .close-modal {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }

    .close-modal:hover {
      color: #333;
    }

    /* View Gateways Button */
    .view-gateways-btn {
      display: inline-block;
      padding: 10px 20px;
      background: #3e64ff;
      color: white;
      border: none;
      border-radius: 6px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 10px;
    }

    .view-gateways-btn:hover {
      background: #324ddb;
      transform: translateY(-2px);
    }

    /* Modal Responsiveness */
    @media (max-width: 768px) {
      .modal-content {
        width: 90%;
        margin: 10% auto;
        padding: 15px;
      }
    }

    .plans-slider-container {
      width: 100%;
      margin-bottom: 30px;
    }

    .plans-swiper {
      width: 100%;
      height: auto;
      padding: 20px 0;
    }

    .plans-swiper .swiper-slide {
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .plans-swiper .plan-card {
      margin: 0 auto;
      max-width: 350px;
    }

    .plans-swiper .swiper-pagination {
      position: relative;
      margin-top: 20px;
    }

    .plans-swiper .swiper-pagination-bullet {
      background: #ccc;
      opacity: 1;
    }

    .plans-swiper .swiper-pagination-bullet-active {
      background: #3e64ff;
    }
  </style>
</head>

<body>
  <div class="container">
    <!-- 75% Image Slider -->
    <div class="left-panel">
      <div class="swiper">
        <div class="swiper-wrapper">
          <div class="swiper-slide">
            <img src="../image/ChatGPT Image Jul 17, 2025, 11_19_07 AM.png" alt="POS System">
          </div>
          <div class="swiper-slide">
            <img src="../image/lap.png" alt="Retail Store">
          </div>
          <div class="swiper-slide">
            <img src="../image/explain.png" alt="Payment System">
          </div>
        </div>
        <div class="swiper-pagination"></div>
      </div>
    </div>

    <!-- 25% Request Details Panel -->
    <div class="right-panel">
      <div class="form-box">
        <h2>Request SkyLink System</h2>

        <form id="requestForm" enctype="multipart/form-data">
          <div class="form-section">
            <h3>Personal Information</h3>
            <div class="input-box">
              <input type="text" name="fullname" id="fullname" required>
              <label>Full Name</label>
            </div>
            <div class="input-box">
              <input type="email" name="email" id="email" required>
              <label>Email</label>
            </div>

            <div class="input-box">
              <input type="text" name="mobile" id="mobile" required>
              <label>mobile</label>
            </div>

          </div>

          <div class="form-section">
            <h3>Business Information</h3>
            <div class="input-box">
              <input type="text" name="bname" id="bname" required>
              <label>Business Name</label>
            </div>
            <div class="input-box">
              <input type="text" name="baddress" id="baddress" required>
              <label>Business Address</label>
            </div>
          </div>

          <!-- Replace the existing dropdown section in the form -->
          <div class="form-section">
            <h3>Choose Your Plan</h3>
            <div class="input-box">
              <select name="selected_plan" id="selected_plan" required>
                <option value="" disabled selected>Select a Plan</option>
                <?php
                $plans = getSubscriptionPlans();
                foreach ($plans as $data) {
                  ?>
                  <option value="<?php echo $data['id']; ?>" data-price="<?php echo $data['monthly_rental']; ?>">
                    <?php echo htmlspecialchars($data['plan_name']); ?> (Rs.
                    <?php echo htmlspecialchars($data['monthly_rental']); ?>/month)
                  </option>
                  <?php
                }
                ?>
              </select>

            </div>
          </div>

          <div class="form-section">
            <p>Your plan details are displayed below. Check them out and select the plan that fits you best to enjoy the
              system!</p>
          </div>

          <!-- Replace the existing Plan Details section with this code -->
          <div class="form-section">
            <h3>Plan Details</h3>
            <div class="plans-slider-container">
              <div class="plans-swiper">
                <div class="swiper-wrapper">
                  <?php
                  $plans = getSubscriptionPlans();
                  foreach ($plans as $plan) {
                    $isPopular = ($plan['id'] == 2); // Assuming ID 2 is most popular
                    ?>
                    <div class="swiper-slide">
                      <div class="plan-card <?php echo $isPopular ? 'popular' : ''; ?>">
                        <?php if ($isPopular) { ?>
                          <div class="popular-badge">Most Popular</div>
                        <?php } ?>
                        <div class="plan-header">
                          <h3><?php echo htmlspecialchars($plan['plan_name']); ?></h3>
                          <div class="plan-price">Rs.
                            <?php echo htmlspecialchars($plan['monthly_rental']); ?><span>/month</span>
                          </div>
                        </div>
                        <div class="plan-features">
                          <ul>
                            <li><?php echo htmlspecialchars($plan['advantage']); ?></li>
                            <li>Up to <?php echo htmlspecialchars($plan['base_candidate_limit']); ?> candidates</li>
                            <!-- Add more features as needed -->
                          </ul>
                        </div>
                        <div class="plan-footer"></div>
                      </div>
                    </div>
                  <?php } ?>
                </div>
                <div class="swiper-pagination"></div>
              </div>
            </div>
          </div>

          <div class="form-section">
            <h3>Request Payment</h3>
            <p>To use this system, you are required to pay a monthly fee of Rs. 2000. For more information, please read
              our terms and conditions. Additionally, refer to the Top Security Details section for further assurance.
            </p>
            <a href="condition.php">Terms and Conditions</a>
          </div>

          <div class="form-section">
            <h3>Payment Details</h3>
            <div class="acc-box">
              <div class="acc-detail">
                <span class="acc-label">Account Number:</span>
                <span class="acc-value">94309606</span>
              </div>
              <div class="acc-detail">
                <span class="acc-label">Bank Name:</span>
                <span class="acc-value">Bank of Ceylon</span>
              </div>
              <div class="acc-detail">
                <span class="acc-label">Branch:</span>
                <span class="acc-value">KOTEHENA</span>
              </div>
              <div class="acc-detail">
                <span class="acc-label">Account Holder:</span>
                <span class="acc-value">DIGITAL SPARK SOLUTION PVT LTD</span>
              </div>
              <div class="acc-detail">
                <span class="acc-label">Amount:</span>
                <span class="acc-value">Rs. 20,000</span>
              </div>
            </div>
          </div>

          <div class="form-section">
            <button type="button" class="view-gateways-btn">View Available Payment Gateways</button>
          </div>

          <div class="file-upload-box">
            <label class="file-upload-label">
              <input type="file" name="slip" id="slip" required class="file-upload-input" accept="image/jpeg,image/png">
              <div class="file-upload-content">
                <svg class="file-upload-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                  <polyline points="17 8 12 3 7 8"></polyline>
                  <line x1="12" y1="3" x2="12" y2="15"></line>
                </svg>
                <span class="file-upload-text">Choose payment slip to upload</span>
                <span class="file-upload-hint">JPEG or PNG (Max 5MB)</span>
              </div>
              <div class="file-upload-preview"></div>
            </label>
          </div>

          <div class="accept-conditions">
            <label class="checkbox-container">
              <input type="checkbox" id="acceptAll" required>
              <span class="checkmark"></span>
              <span class="checkbox-label">Accept All Conditions</span>
            </label>
          </div>

          <button type="submit" class="btn">Submit Request</button>
        </form>
      </div>
    </div>
  </div>

  <!-- Add this modal HTML right before the closing </body> tag -->
  <div id="paymentGatewaysModal" class="modal">
    <div class="modal-content">
      <span class="close-modal">&times;</span>
      <h3>Available Payment Gateways</h3>
      <div class="plans-container">
        <?php
        $gateways = getPaymentGateways();
        if (count($gateways)) {
          foreach ($gateways as $gateway) {
            ?>
            <div class="plan-card">
              <div class="plan-header">
                <h3><?php echo htmlspecialchars($gateway['gatway_name']); ?></h3>
              </div>
              <div class="plan-header">
                <?php if (!empty($gateway['gatway_icon'])) { ?>
                  <img height="20"
                    src="../skylinkboard/gatewayImages/<?php echo htmlspecialchars($gateway['gatway_icon']); ?>"
                    alt="<?php echo htmlspecialchars($gateway['gatway_name']); ?> Icon">
                <?php } ?>
              </div>
              <div class="plan-header">
                <?php if (!empty($gateway['gatway_image'])) { ?>
                  <img height="100"
                    src="../skylinkboard/gatewayImages/<?php echo htmlspecialchars($gateway['gatway_image']); ?>"
                    alt="<?php echo htmlspecialchars($gateway['gatway_name']); ?> Image">
                <?php } ?>
              </div>
              <div class="plan-header">
                <?php if (!empty($gateway['link'])) { ?>
                  <p><a href="<?php echo htmlspecialchars($gateway['link']); ?>"
                      target="_blank"><?php echo htmlspecialchars($gateway['link']); ?></a></p>
                <?php } ?>
              </div>
            </div>
          <?php }
        } else {
          echo "<p>No active payment gateways available.</p>";
        } ?>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
  <script>
    // Initialize Swiper
    const swiper = new Swiper('.swiper', {
      loop: true,
      pagination: {
        el: '.swiper-pagination',
        clickable: true,
      },
      autoplay: {
        delay: 3000,
        disableOnInteraction: false,
      },
    });

    // Add focus class to inputs when they have content
    document.querySelectorAll('.input-box input, .input-box select, .input-box textarea').forEach(input => {
      input.addEventListener('change', function () {
        if (this.value) {
          this.nextElementSibling.classList.add('active');
        } else {
          this.nextElementSibling.classList.remove('active');
        }
      });

      // Trigger change event on page load for prefilled fields
      if (input.value) {
        input.dispatchEvent(new Event('change'));
      }
    });

    // Update payment amount when a plan is selected from the dropdown
    document.getElementById('selected_plan').addEventListener('change', function () {
      const selectedOption = this.options[this.selectedIndex];
      const planPrice = selectedOption.getAttribute('data-price');
      const planName = selectedOption.textContent;

      // Format price with commas
      const formattedPrice = 'Rs. ' + parseInt(planPrice).toLocaleString('en-IN');

      // Update the amount in payment details
      document.querySelector('.acc-detail .acc-value:last-child').textContent = formattedPrice;

      // Show confirmation
      Swal.fire({
        title: 'Plan Selected',
        text: `You've selected the ${planName} plan`,
        icon: 'success',
        confirmButtonColor: '#3e64ff',
      });
    });

    // File upload preview
    document.querySelector('.file-upload-input').addEventListener('change', function (e) {
      const preview = document.querySelector('.file-upload-preview');
      preview.innerHTML = '';
      preview.style.display = 'none';

      if (this.files && this.files[0]) {
        preview.style.display = 'block';
        const file = this.files[0];

        if (file.type.match('image.*')) {
          const reader = new FileReader();

          reader.onload = function (e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            preview.appendChild(img);

            const info = document.createElement('div');
            info.className = 'file-info';
            info.textContent = `${file.name} (${(file.size / 1024 / 1024).toFixed(2)}MB)`;
            preview.appendChild(info);
          }

          reader.readAsDataURL(file);
        } else {
          const icon = document.createElement('div');
          icon.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#3e64ff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
          <polyline points="13 2 13 9 20 9"></polyline>
        </svg>
      `;
          preview.appendChild(icon);

          const info = document.createElement('div');
          info.className = 'file-info';
          info.textContent = `${file.name} (${(file.size / 1024 / 1024).toFixed(2)}MB)`;
          preview.appendChild(info);
        }
      }
    });

    // Form submission
    document.getElementById('requestForm').addEventListener('submit', function (e) {
      e.preventDefault();

      const formData = new FormData(this);

      // Validate form
      const fullname = document.getElementById('fullname').value.trim();
      const email = document.getElementById('email').value.trim();
      const mobile = document.getElementById('mobile').value.trim();
      const bname = document.getElementById('bname').value.trim();
      const baddress = document.getElementById('baddress').value.trim();
      const slip = document.getElementById('slip').files[0];
      const acceptAll = document.getElementById('acceptAll').checked;
      const selectedPlan = document.getElementById('selected_plan').value;

      if (!fullname || !email || !mobile || !bname || !baddress || !slip || !acceptAll || !selectedPlan) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Please fill all required fields, select a plan, and accept the conditions',
        });
        return;
      }

      // File size validation (5MB max)
      if (slip.size > 5 * 1024 * 1024) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'File size must be less than 5MB',
        });
        return;
      }

      // Submit form via AJAX
      fetch('requestProcess.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.text())
        .then(data => {
          if (data === "Success") {
            Swal.fire({
              title: "Success!",
              text: "Request Submitted, Wait We will contact you soon.",
              icon: "success",
              confirmButtonColor: "#0091ff",
            }).then(() => {
              document.getElementById('requestForm').reset();
              document.querySelector('.file-upload-preview').style.display = 'none';
              document.querySelectorAll('.input-box label').forEach(label => {
                label.classList.remove('active');
              });
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: data,
            });
          }
        })
        .catch(error => {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "An error occurred while submitting the form",
          });
          console.error('Error:', error);
        });
    });

    // Get the modal
    const modal = document.getElementById("paymentGatewaysModal");

    // Get the button that opens the modal
    const btn = document.querySelector(".view-gateways-btn");

    // Get the <span> element that closes the modal
    const span = document.querySelector(".close-modal");

    // When the user clicks the button, open the modal 
    btn.onclick = function () {
      modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function () {
      modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function (event) {
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }

    // Initialize Plans Swiper
    const plansSwiper = new Swiper('.plans-swiper', {
      slidesPerView: 1,
      spaceBetween: 20,
      pagination: {
        el: '.plans-swiper .swiper-pagination',
        clickable: true,
      },
      autoplay: {
        delay: 5000,
        disableOnInteraction: false,
      },
    });
  </script>
</body>

</html>