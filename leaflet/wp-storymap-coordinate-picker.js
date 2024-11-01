/**
  This document generats an interactive map to interactively select coordinates.
 */

var mymap;
var marker;

window.onload = function(){
  var lat;
  var lng;
  var zoom;
  var latField = document.getElementById('lat').value;
  var lngField = document.getElementById('lon').value;
  var zoomField = document.getElementById('zoom').value;
  var color = document.getElementById('color').value;
  var shape = document.getElementById('shape').value;
  var shapeColor = document.getElementById('shapeColor').value;

  mymap = L.map('wp-storymap-pro-picker').setView([0,0], 2);


  // change pointer style
  document.getElementById('wp-storymap-pro-picker').style.cursor = 'crosshair'

  // basemap
	L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		maxZoom: 18,
		attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
			'<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
			'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
		id: 'mapbox.streets'
	}).addTo(mymap);

	var popup = L.popup();

  if(latField.length != 0){
    lat = document.getElementById('lat').value;
  }else{
    lat = 0;
  }

  if(lngField.length != 0){
    lng = document.getElementById('lon').value;
  }else{
    lng = 0;
  }

  if(zoomField.length != 0){
    zoom = document.getElementById('zoom').value;
    mymap.flyTo([lat, lng ], zoom);
  }else{
    zoom = 0;
  }

  var markerSytle = new L.divIcon({
    //html: '<i class="fab fa-accessible-icon"></i>',
    html: '<span style="font-size:1.7em" class="fa-stack fa-2x"><i style="color:'+color+'" class="fas fa-map-marker fa-stack-2x"></i><i style="color:'+shapeColor+'" class="icon-marker '+shape+' fa-stack-1x fa-inverse"></i></span>',
    iconSize: [40, 40],
    className: 'marker-icon'
  });

  var latlng = new L.LatLng(lat, lng);
  marker = L.marker(latlng, {icon:markerSytle}).addTo(mymap);




  //geosearch control
  var GeoSearchControl = window.GeoSearch.GeoSearchControl;
  var OpenStreetMapProvider = window.GeoSearch.OpenStreetMapProvider;

  var provider = new OpenStreetMapProvider();

  var searchControl = new GeoSearchControl({
    provider: provider,
    showMarker:true,
    marker: {                                           // optional: L.Marker    - default L.Icon.Default
      icon: new L.Icon.Default(),
    },
    autoClose: true,
    style: 'bar',
  });

  mymap.addControl(searchControl);

	function onMapClick(e) {
		//popup
		//	.setLatLng(e.latlng)
		//	.setContent("You clicked the map at " + e.latlng.toString())
		//	.openOn(mymap);

    // assign lat/lon values to input field
    document.getElementById('lat').value = e.latlng.lat.toFixed(5);
    document.getElementById('lon').value = e.latlng.lng.toFixed(5);

    //mopve marker
    var newLatLng = new L.LatLng(e.latlng.lat.toFixed(5), e.latlng.lng.toFixed(5));
    marker.setLatLng(newLatLng);

	}

	mymap.on('click', onMapClick);

  mymap.on('moveend', function() {
    document.getElementById('zoom').value = mymap.getZoom();
  });


}

function changeLatLongZoom(){
  var lat;
  var lng;
  var zoom;
  var latField = document.getElementById('lat').value;
  var lngField = document.getElementById('lon').value;
  var zoomField = document.getElementById('zoom').value;

  if(latField.length != 0){
    lat = document.getElementById('lat').value;
  }else{
    lat = 0;
  }

  if(lngField.length != 0){
    lng = document.getElementById('lon').value;
  }else{
    lng = 0;
  }

  if(zoomField.length != 0){
    zoom = document.getElementById('zoom').value;
    mymap.flyTo([lat, lng ], zoom);
  }else{
    zoom = 0;
  }

  var newLatLng = new L.LatLng(lat, lng);
  marker.setLatLng(newLatLng);

}
