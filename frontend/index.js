// ============================================
// Locate Med - Front-end helpers only.
// Signup, login, and inventory are handled by
// plain PHP forms (action="....php" method="POST").
// This file only adds small UX conveniences that
// don't need our own backend.
// ============================================

// ---- Pharmacist signup: auto-fill lat/long before the form submits ----
function geocodePharmacyLocation(address, city, pincode) {
	const query = [address, city, pincode, 'India']
		.filter(Boolean)
		.join(', ');

	return fetch(
		`https://nominatim.openstreetmap.org/search?format=jsonv2&limit=1&countrycodes=in&q=${encodeURIComponent(query)}`,
		{ headers: { 'Accept': 'application/json' } }
	)
		.then((response) => {
			if (!response.ok) {
				throw new Error('Unable to look up the pharmacy location.');
			}
			return response.json();
		})
		.then((results) => {
			if (!Array.isArray(results) || results.length === 0) {
				throw new Error('No matching location was found for that address.');
			}
			return {
				latitude: Number(results[0].lat),
				longitude: Number(results[0].lon)
			};
		});
}

function setupPharmacistSignupGeocoding() {
	const signupForm = document.getElementById('signup-form');
	const latitudeField = document.getElementById('latitude');
	const longitudeField = document.getElementById('longitude');

	// Only run this on the pharmacist signup page.
	if (!signupForm || !latitudeField || !longitudeField) {
		return;
	}

	signupForm.addEventListener('submit', function handleSubmit(event) {
		// Stop the very first submit so we can fetch coordinates first.
		event.preventDefault();

		const address = document.getElementById('address').value.trim();
		const city = document.getElementById('city').value.trim();
		const pincode = document.getElementById('pincode').value.trim();

		const password = document.getElementById('password').value;
		const confirmPassword = document.getElementById('confirm-password').value;
		if (password !== confirmPassword) {
			alert('Passwords do not match.');
			return;
		}

		geocodePharmacyLocation(address, city, pincode)
			.then((coordinates) => {
				latitudeField.value = String(coordinates.latitude);
				longitudeField.value = String(coordinates.longitude);

				// Remove this listener so the next submit goes through normally,
				// then submit the form for real (hits register-pharmacist.php).
				signupForm.removeEventListener('submit', handleSubmit);
				signupForm.submit();
			})
			.catch((error) => {
				alert(error.message);
			});
	});
}

// ---- User signup: simple client-side password match check ----
// (register-user.php still re-checks this on the server, this is just
// so the person gets instant feedback instead of a page reload.)
function setupUserSignupValidation() {
	const signupForm = document.getElementById('signup-form');
	const fullnameField = document.getElementById('fullname');
	const pharmacyNameField = document.getElementById('pharmacy-name');

	// Only run this on the user signup page (has #fullname, no #pharmacy-name).
	if (!signupForm || !fullnameField || pharmacyNameField) {
		return;
	}

	signupForm.addEventListener('submit', (event) => {
		const password = document.getElementById('password').value;
		const confirmPassword = document.getElementById('confirm-password').value;

		if (password !== confirmPassword) {
			event.preventDefault();
			alert('Passwords do not match.');
		}
		// Otherwise let the form submit normally to register-user.php
	});
}

// ---- Logout button: just link to logout.php, which destroys the session ----
function attachLogout(button) {
	if (!button) {
		return;
	}
	button.addEventListener('click', () => {
		window.location.href = '../backend/logout.php';
	});
}

// ---- User dashboard: GPS button + quick medicine search tags ----
function setupUserDashboardHelpers() {
	const gpsBtn = document.getElementById('gps-btn');
	const locationInput = document.getElementById('location-input');

	if (gpsBtn && navigator.geolocation) {
		gpsBtn.addEventListener('click', () => {
			navigator.geolocation.getCurrentPosition(
				(position) => {
					if (locationInput) {
						locationInput.value = `${position.coords.latitude.toFixed(4)}, ${position.coords.longitude.toFixed(4)}`;
					}
				},
				() => alert('Unable to read your location. Please allow GPS access.')
			);
		});
	}

	document.querySelectorAll('.medicine-quick-search').forEach((button) => {
		button.addEventListener('click', () => {
			const medicineInput = document.getElementById('medicine-input');
			const label = button.querySelector('span')?.textContent || '';
			if (medicineInput) {
				medicineInput.value = label;
			}
		});
	});

	attachLogout(document.getElementById('logout-btn'));
}

// ---- Pharmacist dashboard: just wire up the logout button for now ----
// (Inventory add/remove will move to PHP forms in the next step.)
function setupPharmacistDashboardHelpers() {
	const medicineForm = document.getElementById('add-medicine-form');
	if (!medicineForm) {
		return;
	}
}

setupPharmacistSignupGeocoding();
setupUserSignupValidation();
setupUserDashboardHelpers();
setupPharmacistDashboardHelpers();