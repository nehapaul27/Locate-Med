<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Locate Med</title>
     <link rel="stylesheet" href="style.css">
     <link
  href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css"
  rel="stylesheet"
/>
</head>

<body>
    <div id="main">
            <div id="header">
                <div id="logo">
                    <i class="ri-map-pin-line"></i>
                    <p>Locate Med</p>
                </div>

                <div class="texts"><p>Home</p></div>
                <div class="texts"><p>How it works</p></div>
                <div class="texts"><p>Features</p></div>
                <div class="texts"><p></p>About us</p></div>
                <div class="texts"><p>Services</p></div>
                <button id = "contact-us" class="texts" type="button"><p>Contact us</p></button>
        
            </div>  
        <div id="fakeheader"></div>

        <div id="page1">
            <div id="left-box" class="box">
                <p id="p1" class="left-para">Find Medicines.</p>
                <P id="p2" class="left-para">Nearby.</P>
                <p id="p3" class="left-para">Instantly.</p>
                    <pre class="left-para">
                        locate med helps you find medicines and nearby
                        pharmacies in real-time.Fast,reliable and always 
                        here for your health.
                    </pre>
                    <div class="login-signup">
                        <button id="login" type="button">
                          <span onclick="window.location.href='login.html'">Login</span> 
                        </button>
                        <button id="signup" type="button">
                             <span onclick="window.location.href='signup.html'">Sign-up</span> 
                        </button>
                    </div>
            </div>
            <div id="right-box" class="box"><img src="landing page picture.png" alt="Description"></div>
        </div>
        
        <div id="page2">
            <div id="box1" class="page2-box">
                <i class="ri-map-pin-line"></i>
                <div class="text-content">
                    <p class="heading"><u>Find Nearby Pharmacies</u></p>
                    <p class="description">Locate the nearest pharmacies with ease and convenience. Use our GPS-powered map to find pharmacies in your area and get real-time directions instantly.</p>
                </div>
            </div>
            <div id="box2" class="page2-box">
                <i class="ri-capsule-fill"></i>
                <div class="text-content">
                    <p class="heading"><u>Check Medicine Availability</u></p>
                    <p class="description">See if the medicines you need are available at nearby pharmacies. Search for specific medications and instantly check stock availability across multiple locations.</p>
                </div>
            </div>
            <div id="box3" class="page2-box">
                <i class="ri-flashlight-fill"></i>
                <div class="text-content">
                    <p class="heading"><u>Fast & Reliable Results</u></p>
                    <p class="description">Get instant results for your medicine search queries. Our advanced algorithm provides accurate and up-to-date information within seconds.</p>
                </div>
            </div>
            <div id="box4" class="page2-box">
                <i class="ri-shield-line"></i>
                <div class="text-content">
                    <p class="heading"><u>Secure & Trusted</u></p>
                    <p class="description">Your health information is protected with our secure platform. We use industry-standard encryption to safeguard your personal and medical data.</p>
                </div>
            </div>
        </div>

<div id="why-us">
    <h1>Why Choose Locate Med?</h1>
    <p>
        Locate Med is designed to make finding medicines simple, fast, and stress-free.
        Whether you need an urgent prescription or want to check medicine availability,
        our platform connects you with nearby pharmacies in real time.
    </p>

    <div id="why-container">
        <div class="why-card">
            <i class="ri-time-line"></i>
            <h3>Save Time</h3>
            <p>Find medicines without visiting multiple pharmacies.</p>
        </div>

        <div class="why-card">
            <i class="ri-map-pin-2-line"></i>
            <h3>Nearby Stores</h3>
            <p>Locate trusted pharmacies closest to your location.</p>
        </div>

        <div class="why-card">
            <i class="ri-shield-check-line"></i>
            <h3>Trusted Platform</h3>
            <p>Your searches are secure and your information stays protected.</p>
        </div>
    </div>
</div>
    
    <div id="footer">
        <div class="left">
            <i class="ri-copyright-line"></i>
            <p>2026 Locate Med. All rights reserved.</p>
        </div>

        <div class="middle">
            <p>Privacy Policy</p>
            <p>Terms & Conditions</p>
            <p>Help Center</p>
        </div>

        <div class="right">
            <p>Follow Us:</p>
            <i class="ri-facebook-circle-fill"></i>
            <i class="ri-instagram-line"></i>
            <i class="ri-linkedin-box-fill"></i>
            <i class="ri-twitter-fill"></i>
        </div>
        </div>
    <div id="fakefooter"></div>
</div>
<script src="index.js"></script>
</body>
</html>