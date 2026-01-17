/* js/map-init.js */
function initMetropolitanoMap() {
    const mapContainer = document.getElementById('map');
    if (!mapContainer) return; // Έλεγχος αν υπάρχει ο χάρτης στη σελίδα

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
        setTimeout(function() { map.invalidateSize(); }, 400);
    });
}

// Εκτέλεση
document.addEventListener('DOMContentLoaded', initMetropolitanoMap);