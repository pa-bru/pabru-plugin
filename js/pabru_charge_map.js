function initMap() {
   map = new google.maps.Map(document.getElementById('pabru_map_'+pabru_map.pabru_map_id), {
    zoom: 12,
    center: {lat: -34.397, lng: 150.644}
  });
  var geocoder = new google.maps.Geocoder();
  geocodeAddress(geocoder, map);
}

function geocodeAddress(geocoder, resultsMap) {
  var address = pabru_map.pabru_map_address;

  geocoder.geocode({'address': address}, function (results, status) {
    if (status === google.maps.GeocoderStatus.OK) {
      resultsMap.setCenter(results[0].geometry.location);
      
      var marker = new google.maps.Marker({
        map: resultsMap,
        position: results[0].geometry.location,
        title: 'Uluru (Ayers Rock)'
      });

      var contentString = '<div id="content">'+
      '<div id="siteNotice">'+
      '</div>'+
      '<h1 id="firstHeading" class="firstHeading">'+ pabru_map.pabru_map_marker_title +'</h1>'+
      '<div id="bodyContent">'+
      '<p>'+ pabru_map.pabru_map_marker_content +'</p> '+
      '<p>Attribution: <a href="'+ pabru_map.pabru_map_marker_link +'">'+ pabru_map.pabru_map_marker_link + '</a></p>' +
      '</div>'+
      '</div>';

      var infowindow = new google.maps.InfoWindow({
        content: contentString
      });

      marker.addListener('click', function() {
        infowindow.open(map, marker);
      });

    }
    else {
      alert('Geocode was not successful for the following reason: ' + status);
    }
  });
}
//change zoom/scrollable