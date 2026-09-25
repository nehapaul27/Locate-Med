<?php
session_start();

// If no one is logged in, send them back to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

require "../backend/db_connect.php";

$owner_name    = htmlspecialchars($_SESSION['owner_name']);
$pharmacy_name = htmlspecialchars($_SESSION['pharmacy_name']);
$email         = htmlspecialchars($_SESSION['email']);
$phone         = htmlspecialchars($_SESSION['phone']);
$license       = htmlspecialchars($_SESSION['license']);
$address       = htmlspecialchars($_SESSION['address']);
$city          = htmlspecialchars($_SESSION['city']);
$pincode       = htmlspecialchars($_SESSION['pincode']);
$full_address  = $address . ', ' . $city . ' - ' . $pincode;

// Build initials from the pharmacy name for the top-right avatar, e.g. "City Care Pharmacy" -> "CC"
$words = preg_split('/\s+/', trim($pharmacy_name));
$initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));

// Resolve pharmacy_id for this session (same lookup used in add_medicine.php)
if (!isset($_SESSION['pharmacy_id'])) {
    $user_id = (int) $_SESSION['user_id'];
    $pharmacy_result = mysqli_query($conn, "SELECT id FROM pharmacies WHERE user_id = '$user_id' LIMIT 1");
    $pharmacy_row = mysqli_fetch_assoc($pharmacy_result);
    if ($pharmacy_row) {
        $_SESSION['pharmacy_id'] = $pharmacy_row['id'];
    }
}
$pharmacy_id = $_SESSION['pharmacy_id'] ?? null;

// Pull all of this pharmacy's medicines (filtering now happens live in the browser)
$medicines = [];
if ($pharmacy_id) {
    $medicines_result = mysqli_query(
        $conn,
        "SELECT name, category, quantity, price FROM medicines WHERE pharmacy_id = '$pharmacy_id' ORDER BY name ASC"
    );
    while ($row = mysqli_fetch_assoc($medicines_result)) {
        $medicines[] = $row;
    }
}
$LOW_STOCK_THRESHOLD = 20;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pharmacist Dashboard — Locate Med</title>
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css" rel="stylesheet"/>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="pharmacist-dashboard.css">
</head>
<body>
<div id="main">

  <!-- ================= HEADER ================= -->
  <header id="header">
    <div id="logo">
      <div class="mark"><i class="ri-capsule-fill"></i></div>
      <div>
        Locate Med
        <span class="tag">Pharmacist Portal</span>
      </div>
    </div>

    <div id="header-right">
      <div id="owner-badge" tabindex="0">
        <div id="owner-trigger">
          <div id="owner-avatar"><?php echo $initials; ?></div>
          <div>
            <p class="owner-name"><?php echo $pharmacy_name; ?></p>
            <p class="owner-role">Owner</p>
          </div>
          <i class="ri-arrow-down-s-line"></i>
        </div>

        <!-- hover-revealed shop info card -->
        <div id="shop-card">
          <div class="shop-card-head">
            <span class="badge-pin">Verified</span>
            <h3><?php echo $pharmacy_name; ?></h3>
            <p><?php echo $license; ?></p>
          </div>
          <div class="shop-card-body">
            <div class="shop-row">
              <i class="ri-map-pin-line"></i>
              <div><span class="label">Address</span><p class="value"><?php echo $full_address; ?></p></div>
            </div>
            <div class="shop-row">
              <i class="ri-time-line"></i>
              <div><span class="label">Hours</span><p class="value">Set your opening hours later</p></div>
            </div>
            <div class="shop-row">
              <i class="ri-phone-line"></i>
              <div><span class="label">Phone</span><p class="value"><?php echo $phone; ?></p></div>
            </div>
            <div class="shop-row">
              <i class="ri-mail-line"></i>
              <div><span class="label">Email</span><p class="value"><?php echo $email; ?></p></div>
            </div>
          </div>
          <div class="shop-card-foot">
            <span class="rating"><i class="ri-star-fill"></i> Pharmacy profile</span>
            <a href="#dashboard">Profile</a>
          </div>
        </div>
      </div>

      <form action="../backend/logout.php" method="POST" class="logout-form">
        <button type="submit" class="notification-btn" aria-label="Log out" title="Log out">
          <i class="ri-logout-box-r-line"></i>
        </button>
      </form>
    </div>
  </header>

  <div style="display:flex;flex:1;">

    <!-- ================= CONTENT ================= -->
    <main id="content">

      <div id="dashboard"></div>
      <section id="welcome">
        <div>
          <p class="eyebrow">Pharmacist dashboard</p>
          <h1>Welcome back, <?php echo $owner_name; ?></h1>
          <p>Add medicines to keep your pharmacy inventory up to date.</p>
        </div>
        <div class="welcome-icon"><i class="ri-capsule-line"></i></div>
      </section>

      <section class="panel" id="add-medicine">
        <div class="panel-head">
          <div>
            <p class="eyebrow">Inventory</p>
            <h2>Add a medicine</h2>
            <p class="sub">Enter the medicine details below to add it to your pharmacy.</p>
          </div>
        </div>

        <?php if (isset($_GET['added']) && $_GET['added'] == '1'): ?>
          <p class="form-status success">Medicine saved to your inventory.</p>
        <?php endif; ?>

        <form id="add-medicine-form" method="POST" action="../backend/add_medicine.php">
          <div class="field">
            <label for="med-name">Medicine name</label>
            <input type="text" id="med-name" name="name" placeholder="e.g. Amoxicillin 250mg" required>
          </div>
          <div class="field">
            <label for="med-category">Category</label>
            <select id="med-category" name="category">
              <option>Tablet</option>
              <option>Syrup</option>
              <option>Capsule</option>
              <option>Injection</option>
              <option>Ointment</option>
              <option>Other</option>
            </select>
          </div>
          <div class="field">
            <label for="med-qty">Quantity</label>
            <input type="number" id="med-qty" name="quantity" min="0" placeholder="0" required>
          </div>
          <div class="field">
            <label for="med-price">Price (₹)</label>
            <input type="number" id="med-price" name="price" min="0" step="0.01" placeholder="0.00">
          </div>
          <button type="submit" class="btn btn-primary"><i class="ri-add-line"></i> Add medicine</button>
        </form>
      </section>

      <section class="panel" id="your-medicines">
        <div class="panel-head">
          <div>
            <p class="eyebrow">Inventory</p>
            <h2>Your medicines</h2>
            <p class="sub">Everything currently listed under <?php echo $pharmacy_name; ?>.</p>
          </div>
        </div>

        <div class="search-form">
          <input type="text" id="medicine-search" placeholder="Search by medicine name">
          <button type="button" id="medicine-search-btn" aria-label="Search"><i class="ri-search-line"></i></button>
        </div>

        <table class="simple-table" id="medicines-table">
          <tr>
            <th>Medicine</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Price</th>
          </tr>
          <?php if (empty($medicines)): ?>
            <tr>
              <td colspan="4">No medicines added yet.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($medicines as $med): ?>
              <tr data-name="<?php echo strtolower(htmlspecialchars($med['name'])); ?>">
                <td><?php echo htmlspecialchars($med['name']); ?></td>
                <td><?php echo htmlspecialchars($med['category']); ?></td>
                <td><?php echo (int) $med['quantity']; ?></td>
                <td>₹<?php echo number_format((float) $med['price'], 2); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </table>
        <p id="no-search-results" style="display:none;">No medicines match your search.</p>
      </section>

    </main>
  </div>
</div>

<script>
  // ---- Live search: filter medicines by name, no page reload ----
  const medSearch    = document.getElementById('medicine-search');
  const medSearchBtn = document.getElementById('medicine-search-btn');

  if (medSearch) {
    const rows = document.querySelectorAll('#medicines-table tr[data-name]');
    const noResults = document.getElementById('no-search-results');

    function filterMedicines() {
      const q = medSearch.value.trim().toLowerCase();
      let visible = 0;
      rows.forEach(row => {
        const match = row.dataset.name.includes(q);
        row.style.display = match ? '' : 'none';
        if (match) visible++;
      });
      noResults.style.display = (visible === 0) ? 'block' : 'none';
    }

    medSearch.addEventListener('input', filterMedicines);
    if (medSearchBtn) medSearchBtn.addEventListener('click', filterMedicines);
  }

  // ---- Sidebar / section scroll highlighting ----
  const sections = ['dashboard', 'add-medicine', 'your-medicines']
    .map(id => document.getElementById(id)).filter(Boolean);
  const navItems = document.querySelectorAll('.nav-item');

  window.addEventListener('scroll', () => {
    let currentId = 'dashboard';
    sections.forEach(sec => {
      if (window.scrollY + 120 >= sec.offsetTop) currentId = sec.id;
    });
    navItems.forEach(item => {
      item.classList.toggle('active', item.getAttribute('href') === '#' + currentId);
    });
  });
</script>
</body>
</html>