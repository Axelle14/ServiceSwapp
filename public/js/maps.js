'use strict';

const mapState = {
  autocomplete: null,
  map: null,
  marker: null,
  isInitialized: false,
};

function loadGoogleMapsScript() {
  if (window.google?.maps) {
    return Promise.resolve();
  }

  if (!window.googleMapsApiKey) {
    return Promise.reject(new Error('Google Maps API key not configured.'));
  }

  return new Promise((resolve, reject) => {
    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=${encodeURIComponent(window.googleMapsApiKey)}&libraries=places&callback=initMapsLoader`;
    script.async = true;
    script.defer = true;
    script.onerror = () => reject(new Error('Google Maps could not be loaded.'));
    document.head.appendChild(script);
    window.initMapsLoader = () => resolve();
  });
}

function initLocationAutocomplete() {
  const input = document.getElementById('locationAddress');
  if (!input || !window.google?.maps?.places) {
    return;
  }

  if (mapState.autocomplete) {
    mapState.autocomplete.unbindAll();
  }

  mapState.autocomplete = new google.maps.places.Autocomplete(input, {
    fields: ['formatted_address', 'geometry', 'place_id'],
    types: ['geocode'],
  });

  mapState.autocomplete.addListener('place_changed', () => {
    const place = mapState.autocomplete.getPlace();
    const formattedAddress = place?.formatted_address || input.value;

    if (place && place.geometry && place.geometry.location) {
      const lat = place.geometry.location.lat();
      const lng = place.geometry.location.lng();
      persistLocation(formattedAddress, lat, lng);
      updateServiceMap(lat, lng, formattedAddress);
      return;
    }

    if (place?.place_id) {
      const geocoder = new google.maps.Geocoder();
      geocoder.geocode({ placeId: place.place_id }, results => {
        if (!results || !results[0] || !results[0].geometry?.location) {
          setLocationNotice('Please select a valid address from the suggestions.');
          return;
        }
        const location = results[0].geometry.location;
        const lat = location.lat();
        const lng = location.lng();
        const address = results[0].formatted_address || formattedAddress;
        persistLocation(address, lat, lng);
        input.value = address;
        updateServiceMap(lat, lng, address);
      });
      return;
    }

    setLocationNotice('Please select a valid address from the suggestions.');
  });
}

function setLocationNotice(message) {
  const notice = document.getElementById('locationNotice');
  if (!notice) return;
  if (!message) {
    notice.style.display = 'none';
    notice.textContent = '';
    return;
  }
  notice.style.display = 'block';
  notice.textContent = message;
}

function persistLocation(address, lat, lng) {
  const addressInput = document.getElementById('locationAddress');
  const latInput = document.getElementById('locationLatitude');
  const lngInput = document.getElementById('locationLongitude');
  if (addressInput) addressInput.value = address;
  if (latInput) latInput.value = lat;
  if (lngInput) lngInput.value = lng;
}

function initMaps() {
  if (mapState.isInitialized || !window.google?.maps) return;
  mapState.isInitialized = true;
  initLocationAutocomplete();
  if (window.serviceMapData?.lat && window.serviceMapData?.lng) {
    renderServiceMap(window.serviceMapData);
  }
}

function renderServiceMap(data) {
  const container = document.getElementById('serviceMap');
  if (!container || !data.lat || !data.lng) return;

  container.style.minHeight = '280px';
  container.style.borderRadius = '16px';
  container.style.overflow = 'hidden';

  const center = { lat: parseFloat(data.lat), lng: parseFloat(data.lng) };
  mapState.map = new google.maps.Map(container, {
    center,
    zoom: 14,
    disableDefaultUI: true,
    gestureHandling: 'auto',
  });

  mapState.marker = new google.maps.Marker({
    position: center,
    map: mapState.map,
  });

  const infoWindow = new google.maps.InfoWindow({
    content: `<div style="font-size:14px;color:#1c1c1c;max-width:240px;">${escapeHtml(data.address)}</div>`,
  });

  mapState.marker.addListener('click', () => infoWindow.open(mapState.map, mapState.marker));
}

function updateServiceMap(lat, lng, address) {
  if (!window.google?.maps) return;
  const container = document.getElementById('serviceMap');
  if (!container) return;
  renderServiceMap({ lat, lng, address });
}

function escapeHtml(value) {
  return String(value)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function showMapsError() {
  const notice = document.getElementById('locationNotice');
  if (notice) {
    notice.style.display = 'block';
    notice.textContent = 'Location services are temporarily unavailable. Please enter the address manually or try again later.';
  }

  const mapContainer = document.getElementById('serviceMap');
  if (mapContainer) {
    mapContainer.innerHTML = '<div style="padding:24px;font-size:14px;color:#555;">Location services are temporarily unavailable. Please try again later.</div>';
    mapContainer.style.background = '#fafafa';
  }
}

function ensureMapsLoaded() {
  if (!document.getElementById('locationAddress') && !document.getElementById('serviceMap')) {
    return;
  }

  loadGoogleMapsScript()
    .then(initMaps)
    .catch(() => showMapsError());
}

window.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('locationAddress') || document.getElementById('serviceMap')) {
    ensureMapsLoaded();
  }
});

window.initMapsLoader = initMaps;
