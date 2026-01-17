<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metropolitano - Αρχική</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="style.css" />

    <style>

        h1, h4 {
            color: white;
        }
        .content-wrapper {
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: stretch;
            padding: 40px 20px;
            gap: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }
        #map {
            flex: 2;
            width: 100%;
            height: 750px;
            min-height: 250px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            order: unset;

        }
        .text-column {
            flex: 1;
            padding: 30px;
            background: #f4f4f4;
            border-radius: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            background-image: linear-gradient(rgba(255,255,255,0.8), rgba(255,255,255,0.8)), url("images/3.jpg");
            background-size: cover;
            color: #333;
        }
        .video-container {
            width: 100%;
            margin-top: 30px;
        }

        .video-container video {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Responsive */
        @media (min-width: 768px) and (max-width: 1023px) {
            .navbar {
                flex-direction: row;
                gap: 15px;
            }

            nav ul {
                gap: 10px;
                font-size: 0.9rem;
            }
            .content-wrapper {
                flex-direction: column;
            }

        }
        @media (min-width: 320px) and (max-width: 767px) {
            .navbar {
                flex-direction: column;
                gap: 15px;
            }
            nav ul {
                gap: 10px;
                font-size: 0.9rem;
            }
            .content-wrapper {
                flex-direction: column;
            }
            #map {
                flex: 2;
                order: unset;
            }
        )

        }
    </style>
</head>
<body>

<header class="navbar">
    <div class="logo">
        Metropolitano
        <img src="images/4.png" style="height: 30px; width: auto;" alt="Logo">
    </div>

    <nav>
        <ul>
            <li><a href="main_page.php">Αρχική</a></li>
            <li><a href="login.php">Σύνδεση</a></li>
            <li><a href="includes/register.php">Εγγραφή</a></li>
        </ul>
    </nav>
</header>

<div class="container">
    <section class="campus-info">
        <h1 style="text-align: center; margin-top: 30px;">Καλωσήρθατε στο Metropolitano Κολλέγιο</h1>
        <h4 style="text-align: center; opacity: 0.9;">Το μεγαλύτερο Κολλέγιο Πανεπιστημιακών Σπουδών στην Ελλάδα</h4>
    </section>
</div>

<div class="content-wrapper">
    <div class="text-column">
        <h3>Why Choose Us</h3>
        <p>Discover what makes Us stand out from the rest and what makes this College the best!</p>
        <ul>
            <li><a href="https://www.mitropolitiko.edu.gr/college/universities/" class="slide-1">1. International Universities</a>
            </li>
            <li><a href="https://www.mitropolitiko.edu.gr/programmata-spoydon/" class="slide-2">2. Top University Studies</a>
            </li>
            <li><a href="https://www.mitropolitiko.edu.gr/foititiki-zoi/facilities/" class="slide-3">3. Super Modern Facilities</a>
            </li>
            <li><a href="https://www.mitropolitiko.edu.gr/college/certificates/" class="slide-4">4. International Certificates</a>
            </li>
        </ul>
    </div>

    <div id="map"></div>

        <div class="text-column">
        <h3>Enrolment</h3>
            <p>
                Please find below all the different ways that you can reach us! Let us help you shape your future together!
                And if want you can even come see us in person if you follow the <b>map!</b>
            </p>
        <ul>
            <li><a href="https://www.mitropolitiko.edu.gr/contact/visit/#visit-form" class="slide-5">Visit Form</a>
            </li>
            <li><a href="includes/register.php" class="slide-6">Register Form</a>
            </li>
            <li><a href="https://www.mitropolitiko.edu.gr/contact/#contact-form" class="slide-7">Contact Form</a>
            </li>
            <li><a href="https://www.mitropolitiko.edu.gr/contact/" class="slide-8">Contact Numbers</a>
            </li>
        </ul>
        </div>

</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    var map = L.map('map', {
        scrollWheelZoom: false,
        dragging: !L.Browser.mobile,
        tap: !L.Browser.mobile
    }).setView([40.63436, 22.93929], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([40.63436, 22.93929]).addTo(map);
    marker.bindPopup("<b>Metropolitano Campus Thessaloniki</b><br>Welcome!").openPopup();
    window.addEventListener('resize', function() {
        setTimeout(function() {
            map.invalidateSize();
        }, 400);
    });

    /* potential back fix*/
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            window.location.reload(true);
        }
    });
</script>

<div class="video-container">
    <video autoplay muted loop playsinline poster="images/video-placeholder.jpg">
    <source src="images/vid.mp4" type="video/mp4">
    </video>
</div>


</div>

<div class="unis">
    <img src="images/2.jpg" alt="Partner Universities" style="width: 100%; display: block;">
</div>

<footer class="footer-content">
    <div class="social-links">
        <a href="https://x.com/"><img src="images/twitter-fill.png" alt="Twitter"></a>
        <a href="https://www.instagram.com/"><img src="images/instagram-line.png" alt="Instagram"></a>
        <a href="https://www.facebook.com/"><img src="images/facebook-line.png" alt="Facebook"></a>
        <a href="https://www.tiktok.com/"><img src="images/tiktok-line.png" alt="TikTok"></a>
    </div>
    <p style="margin-top: 20px;"><b>Copyright &copy; 2026 <br> "Metropolitano Κολλέγιο"</b></p>
</footer>

</body>
</html>