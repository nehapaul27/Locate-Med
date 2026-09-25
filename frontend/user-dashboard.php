<?php
session_start();

// If no one is logged in, send them back to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

$fullname = htmlspecialchars($_SESSION['fullname']);
$initial = strtoupper(substr(trim($_SESSION['fullname']), 0, 1));
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Find Medicines - Locate Med</title>

    <link
        rel="stylesheet"
        href="user-dashboard.css?v=3"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/remixicon@4.9.0/fonts/remixicon.css"
        rel="stylesheet"
    />

</head>


<body>

<div id="main">


    <!-- ================= HEADER ================= -->

    <header id="header">

        <div id="logo">

            <i class="ri-map-pin-line"></i>

            <span>Locate Med</span>

        </div>


        <div id="user-menu">

            <div id="user-profile">

                <span
                    style="
                        width: 40px;
                        height: 40px;
                        border-radius: 50%;
                        background-color: green;
                        color: white;
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                    "
                    class="profile-initial"
                    aria-label="Your profile"
                >
                    <?php echo htmlspecialchars($initial); ?>
                </span>


                <span>
                    <?php echo $fullname; ?>
                </span>

            </div>


            <button id="logout-btn">

                <i class="ri-logout-box-line"></i>

                Logout

            </button>

        </div>

    </header>



    <!-- ================= MAIN CONTENT ================= -->

    <main id="content">


        <!-- ================= SEARCH SECTION ================= -->

        <section id="search-section">

            <h1>
                Find medicines near you
            </h1>


            <p>
                Enter your location, then the medicine name,
                to check real-time stock at nearby pharmacies
            </p>



            <form id="search-form">

                <div id="search-container">


                    <!-- ================= LOCATION ================= -->

                    <div class="input-step">

                        <span class="step-number">
                            1
                        </span>


                        <div
                            id="location-box"
                            class="input-box"
                        >

                            <i class="ri-map-pin-line"></i>


                            <input
                                type="text"
                                id="location-input"
                                name="location"
                                placeholder="Enter your location..."
                            >


                            <!-- GPS BUTTON -->

                            <button
                                type="button"
                                id="gps-btn"
                                aria-label="Use current location"
                            >

                                <i class="ri-gps-line"></i>

                            </button>

                        </div>


                        <!-- LOCATION STATUS -->

                        <small
                            id="location-hint"
                            style="
                                display: block;
                                margin-top: 5px;
                            "
                        >
                            Click the GPS button to detect your location.
                        </small>


                        <!--
                            These values will contain
                            customer's GPS coordinates.
                        -->

                        <input
                            type="hidden"
                            id="customer-latitude"
                            name="latitude"
                        >

                        <input
                            type="hidden"
                            id="customer-longitude"
                            name="longitude"
                        >

                    </div>



                    <!-- ================= MEDICINE ================= -->

                    <div class="input-step">

                        <span class="step-number">
                            2
                        </span>


                        <div
                            id="search-box"
                            class="input-box"
                        >

                            <i class="ri-search-line"></i>


                            <input
                                type="text"
                                id="medicine-input"
                                name="medicine"
                                placeholder="Search for medicine name..."
                            >

                        </div>

                    </div>



                    <!-- ================= SEARCH BUTTON ================= -->

                    <button
                        type="submit"
                        id="search-btn"
                    >

                        <i class="ri-search-line"></i>

                        Search Now

                    </button>

                </div>

            </form>

        </section>



        <!-- ================= POPULAR MEDICINES ================= -->

        <section id="popular-medicines">

            <h2>
                Popular Medicines
            </h2>


            <div id="popular-grid">


                <div class="medicine-quick-search">

                    <i class="ri-capsule-line"></i>

                    <span>
                        Aspirin
                    </span>

                </div>


                <div class="medicine-quick-search">

                    <i class="ri-capsule-line"></i>

                    <span>
                        Paracetamol
                    </span>

                </div>


                <div class="medicine-quick-search">

                    <i class="ri-capsule-line"></i>

                    <span>
                        Cough Syrup
                    </span>

                </div>


                <div class="medicine-quick-search">

                    <i class="ri-capsule-line"></i>

                    <span>
                        Antibiotics
                    </span>

                </div>


                <div class="medicine-quick-search">

                    <i class="ri-capsule-line"></i>

                    <span>
                        Blood Pressure
                    </span>

                </div>


                <div class="medicine-quick-search">

                    <i class="ri-capsule-line"></i>

                    <span>
                        Diabetic
                    </span>

                </div>

            </div>

        </section>



        <!-- ================= SEARCH RESULTS ================= -->

        <section
            id="results-section"
            hidden
        >

            <div id="results-header">

                <h2>

                    Pharmacies with

                    <span id="medicine-name">
                        Aspirin
                    </span>

                    in Stock

                </h2>


                <p id="results-info">
                    Use the search bar to find nearby stock
                </p>

            </div>



            <!-- PHARMACY RESULTS -->

            <div id="pharmacies-results">

            </div>

        </section>


    </main>

</div>



<script src="index.js"></script>



<!-- ========================================================= -->
<!-- GPS + REVERSE GEOCODING                                  -->
<!-- ========================================================= -->

<script>

    // ---------------------------------------------------------
    // GET ELEMENTS
    // ---------------------------------------------------------

    const gpsBtn =
        document.getElementById('gps-btn');

    const locationInput =
        document.getElementById('location-input');

    const latitudeInput =
        document.getElementById('customer-latitude');

    const longitudeInput =
        document.getElementById('customer-longitude');

    const locationHint =
        document.getElementById('location-hint');

    const searchForm =
        document.getElementById('search-form');



    // ---------------------------------------------------------
    // GPS BUTTON
    // ---------------------------------------------------------

    gpsBtn.addEventListener(
        'click',
        function () {


            // Check browser support

            if (!navigator.geolocation) {

                alert(
                    "GPS / location is not supported by your browser."
                );

                return;
            }



            // Change button while detecting

            gpsBtn.disabled = true;

            gpsBtn.innerHTML = `
                <i class="ri-loader-4-line"></i>
            `;


            locationHint.textContent =
                "Detecting your location...";



            // -------------------------------------------------
            // GET GPS POSITION
            // -------------------------------------------------

            navigator.geolocation.getCurrentPosition(

                async function (position) {


                    // Get coordinates

                    const latitude =
                        position.coords.latitude;

                    const longitude =
                        position.coords.longitude;



                    // -------------------------------------------------
                    // SAVE CUSTOMER COORDINATES
                    // -------------------------------------------------

                    latitudeInput.value =
                        latitude;

                    longitudeInput.value =
                        longitude;



                    locationHint.textContent =
                        "Location detected. Finding address...";



                    try {


                        // -------------------------------------------------
                        // REVERSE GEOCODING
                        // -------------------------------------------------

                        const response = await fetch(

                            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&addressdetails=1`,

                            {
                                headers: {
                                    'Accept':
                                        'application/json'
                                }
                            }

                        );



                        if (!response.ok) {

                            throw new Error(
                                "Address lookup failed"
                            );

                        }



                        const data =
                            await response.json();



                        if (!data.address) {

                            throw new Error(
                                "Address not found"
                            );

                        }



                        const address =
                            data.address;



                        // -------------------------------------------------
                        // GET DIFFERENT ADDRESS PARTS
                        // -------------------------------------------------

                        const houseNumber =
                            address.house_number || "";

                        const road =
                            address.road || "";

                        const neighbourhood =
                            address.neighbourhood ||
                            address.suburb ||
                            "";


                        const city =
                            address.city ||
                            address.town ||
                            address.village ||
                            address.municipality ||
                            "";


                        const pincode =
                            address.postcode || "";



                        // -------------------------------------------------
                        // BUILD READABLE LOCATION
                        // -------------------------------------------------

                        let locationText = "";



                        if (houseNumber) {

                            locationText +=
                                houseNumber + ", ";

                        }


                        if (road) {

                            locationText +=
                                road + ", ";

                        }


                        if (neighbourhood) {

                            locationText +=
                                neighbourhood + ", ";

                        }


                        if (city) {

                            locationText +=
                                city;

                        }


                        if (pincode) {

                            locationText +=
                                " - " + pincode;

                        }



                        // Remove extra spaces

                        locationText =
                            locationText
                                .replace(/,\s*$/, "")
                                .trim();



                        // -------------------------------------------------
                        // PUT LOCATION INTO SEARCH BOX
                        // -------------------------------------------------

                        locationInput.value =
                            locationText;



                        // -------------------------------------------------
                        // SUCCESS
                        // -------------------------------------------------

                        locationHint.textContent =
                            "Location detected successfully ✓";

                        locationHint.style.color =
                            "green";



                        gpsBtn.disabled =
                            false;


                        gpsBtn.innerHTML = `
                            <i class="ri-gps-line"></i>
                        `;

                    }


                    catch (error) {

                        console.error(error);


                        // GPS still worked,
                        // but address lookup failed.

                        locationInput.value =
                            `${latitude}, ${longitude}`;


                        locationHint.textContent =
                            "GPS detected, but address could not be found.";

                        locationHint.style.color =
                            "orange";


                        gpsBtn.disabled =
                            false;


                        gpsBtn.innerHTML = `
                            <i class="ri-gps-line"></i>
                        `;


                        alert(
                            "Your GPS location was detected, " +
                            "but the address could not be found. " +
                            "You can enter your location manually."
                        );

                    }

                },



                // -------------------------------------------------
                // GPS ERROR
                // -------------------------------------------------

                function (error) {


                    gpsBtn.disabled =
                        false;


                    gpsBtn.innerHTML = `
                        <i class="ri-gps-line"></i>
                    `;



                    if (
                        error.code ===
                        error.PERMISSION_DENIED
                    ) {

                        alert(
                            "Location permission was denied. " +
                            "Please allow location access and try again."
                        );

                    }


                    else if (
                        error.code ===
                        error.POSITION_UNAVAILABLE
                    ) {

                        alert(
                            "Your location could not be determined. " +
                            "Please try again."
                        );

                    }


                    else if (
                        error.code ===
                        error.TIMEOUT
                    ) {

                        alert(
                            "Location request timed out. " +
                            "Please try again."
                        );

                    }


                    else {

                        alert(
                            "Unable to detect your location."
                        );

                    }


                    locationHint.textContent =
                        "Location could not be detected.";

                },


                // -------------------------------------------------
                // GPS OPTIONS
                // -------------------------------------------------

                {

                    enableHighAccuracy: true,

                    timeout: 15000,

                    maximumAge: 0

                }

            );

        }
    );



    // =========================================================
    // SEARCH FORM
    // =========================================================

    searchForm.addEventListener(
        'submit',
        async function (e) {

            e.preventDefault();


            const medicine =
                document.getElementById(
                    'medicine-input'
                ).value.trim();


            const latitude =
                latitudeInput.value;


            const longitude =
                longitudeInput.value;


            // -------------------------------------------------
            // CHECK MEDICINE
            // -------------------------------------------------

            if (!medicine) {

                alert(
                    "Please enter a medicine name."
                );

                return;

            }



            // -------------------------------------------------
            // CHECK LOCATION
            // -------------------------------------------------

            if (!latitude || !longitude) {

                alert(
                    "Please click the GPS button or enter your location before searching."
                );

                return;

            }



            const resultsSection =
                document.getElementById('results-section');

            const resultsHeader =
                document.getElementById('medicine-name');

            const resultsInfo =
                document.getElementById('results-info');

            const pharmaciesResults =
                document.getElementById('pharmacies-results');

            resultsSection.hidden = false;
            resultsHeader.textContent = medicine;
            resultsInfo.textContent = 'Searching nearby pharmacies...';
            pharmaciesResults.innerHTML = '';

            try {
                const response = await fetch('../backend/search-medicine.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        medicine: medicine,
                        latitude: latitude,
                        longitude: longitude
                    })
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Search failed.');
                }

                resultsInfo.textContent = data.results.length === 1
                    ? '1 pharmacy found nearby.'
                    : `${data.results.length} pharmacies found nearby.`;

                if (data.results.length === 0) {
                    pharmaciesResults.innerHTML = '<p class="no-results">No pharmacies found with this medicine.</p>';
                    return;
                }

                data.results.forEach((pharmacy) => {
                    const result = document.createElement('article');
                    result.className = 'pharmacy-result';
                    result.innerHTML = `
                        <div class="pharmacy-top">
                            <div class="pharmacy-info">
                                <h3>${escapeHtml(pharmacy.name)}</h3>
                                <div class="pharmacy-meta">
                                    <span class="distance"><i class="ri-map-pin-line"></i>${pharmacy.distance} km away</span>
                                </div>
                            </div>
                            <span class="stock-badge available"><i class="ri-checkbox-circle-line"></i>In stock</span>
                        </div>
                        <div class="medicine-availability">
                            <div class="medicine-detail">
                                <h4>${escapeHtml(pharmacy.medicine)}</h4>
                                <p class="stock-info">${pharmacy.quantity} available</p>
                                <p class="price">Price: ${pharmacy.price}</p>
                            </div>
                        </div>
                        <p class="stock-info"><i class="ri-map-pin-line"></i>${escapeHtml(pharmacy.address)}, ${escapeHtml(pharmacy.city)} - ${escapeHtml(pharmacy.pincode)}</p>
                    `;
                    pharmaciesResults.appendChild(result);
                });
            } catch (error) {
                resultsInfo.textContent = 'Unable to complete the search.';
                pharmaciesResults.innerHTML = `<p class="no-results">${escapeHtml(error.message)}</p>`;
            }

        }
    );

    function escapeHtml(value) {
        const element = document.createElement('div');
        element.textContent = value ?? '';
        return element.innerHTML;
    }



    // =========================================================
    // POPULAR MEDICINE CLICK
    // =========================================================

    const quickSearchButtons =
        document.querySelectorAll(
            '.medicine-quick-search'
        );


    quickSearchButtons.forEach(
        function (button) {

            button.addEventListener(
                'click',
                function () {

                    const medicineName =
                        button
                            .querySelector('span')
                            .textContent
                            .trim();


                    document.getElementById(
                        'medicine-input'
                    ).value =
                        medicineName;

                }
            );

        }
    );

</script>

</body>

</html>