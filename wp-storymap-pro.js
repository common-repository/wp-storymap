var markersArray = [];
var activeMarkerPosition = 1;
var firstLoad = true;
var routeline;


var val = document.getElementById('map-pro').getAttribute('data-value');
var map_width = document.getElementById('map-pro').offsetWidth;

var geojson = pw_script_vars1_pro.points;


var geojson1 = {};
geojson1['type'] = 'FeatureCollection';
geojson1['features'] = [];


var show_line = pw_script_vars2_pro.stories.stories[0].properties.show_line;

for (i=0; i< geojson.features.length; i++){
  if (pw_script_vars1_pro.points.features[i].properties.storyId == val) geojson1['features'].push(pw_script_vars1_pro.points.features[i]);
}


//=================
// Identify story number
var stories = pw_script_vars2_pro.stories;
var story_number;
var story_name;
var story_description;
var story_image;
var basemap;
var story_height;



for (i=0; i< stories.stories.length; i++){
  if(pw_script_vars2_pro.stories.stories[i].properties.number == val){
    story_number = val;
    basemap = pw_script_vars2_pro.stories.stories[i].properties.map;
    story_name = pw_script_vars2_pro.stories.stories[i].properties.name;
    story_description = pw_script_vars2_pro.stories.stories[i].properties.description.replace(/\\"http/g, '"http');
    story_image = pw_script_vars2_pro.stories.stories[i].properties.image;

    var new_story_height = pw_script_vars2_pro.stories.stories[i].properties.height;
    if(new_story_height > 0) story_height = new_story_height;

  }
}

markersArray.push([[story_name], [story_description], [story_image]])

//================

var imageContainerMargin = 20;  // Margin + padding

// This watches for the scrollable container
var scrollPosition = 0;


jQuery('div#contents').scroll(function() {
  scrollPosition = jQuery(this).scrollTop();
});

function initMap() {
  // This creates the Leaflet map with a generic start point, because code at bottom automatically fits bounds to all markers

  /*mobile behaviour*/
  if(jQuery(window).width() > 1000){
    var map = L.map('map-pro', {
      center: [0, 0],
      zoom: 3
      //scrollWheelZoom: false
    }).setActiveArea({
      position: 'absolute',
      width: '50%',
      height: '65%'
    });
  }else{
    var map = L.map('map-pro', {
      center: [0, 0],
      zoom: 3
      //scrollWheelZoom: false
    }).setActiveArea({
      position: 'absolute',
      width: '50%',
      height: '125%'
    });
  }


  //change widht and heigth
  if(story_height > 0) jQuery('#map-pro').css("height", story_height+'vh');
  if(story_height > 0) jQuery('.pannel').css("height", story_height+'vh')


  // This displays a base layer map (other options available)
  var layer;
  switch(basemap){
    case 'satellite':
      layer = new L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
         id: 'mapbox.satellite',
         attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' + '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' + 'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
      });
      break;

    case 'hybrid':
      layer = new L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
         id: 'mapbox.streets-satellite',
         attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' + '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' + 'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
      });
      break;

    case 'osm':
      layer = new L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
      });
      break;

    case 'osm_bw':
      layer = L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
      });
      break;

    case 'relief':
    layer = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
    	attribution: 'Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)'
    });
    break;

    case 'cyclemap':
    layer = L.tileLayer('https://dev.{s}.tile.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png', {
    	maxZoom: 20,
    	attribution: '<a href="https://github.com/cyclosm/cyclosm-cartocss-style/releases" title="CyclOSM - Open Bicycle render">CyclOSM</a> | Map data: &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    });
    break;

    case 'watercolor':
    layer = L.tileLayer('https://stamen-tiles-{s}.a.ssl.fastly.net/watercolor/{z}/{x}/{y}.{ext}', {
    	attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> &mdash; Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    	subdomains: 'abcd',
    	ext: 'jpg'
    });
    break;
    case 'worldstreetmap':
    layer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
    	attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DeLorme, NAVTEQ, USGS, Intermap, iPC, NRCAN, Esri Japan, METI, Esri China (Hong Kong), Esri (Thailand), TomTom, 2012'
    });
    break;

    default:
      layer = new L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
      });
  }

  layer.addTo(map);

  //line connection points
  //only if user decides
  var array_line = [];
  for(var i = 0; i < geojson1.features.length; i++){
    var coor =  [geojson1.features[i].geometry.coordinates[1], geojson1.features[i].geometry.coordinates[0]];
    array_line.push(coor);
  }

  if(show_line.length > 0){
    var fullLine = new L.Polyline(array_line, {
        color: '#808080',
        opacity: 0.6,
        weight: 3,
        dashArray: '10, 10',
        dashOffset: '20',
        smoothFactor: 1
    });
    fullLine.addTo(map);

    var pointA = new L.LatLng(geojson1.features[0].geometry.coordinates[1],geojson1.features[0].geometry.coordinates[0]);
    var pointList = [pointA, pointA];

    routeline = new L.Polyline(pointList, {
        color: 'red',
        weight: 3,
        dashArray: '10, 10',
        dashOffset: '20',
        smoothFactor: 1
    });
    routeline.addTo(map);
  }

  // This loads the GeoJSON map data file from a local folder
  var geojson = L.geoJson(geojson1, {
    onEachFeature: function (feature, layer) {
      (function(layer, properties) {
        var numerciMarker = new L.divIcon({
          html: '<span style="font-size:1.7em" class="fa-stack fa-2x"><i style="color:'+feature.properties['color']+'" class="fas fa-map-marker fa-stack-2x"></i><i style="color:'+feature.properties['shapeColor']+'" class="icon-marker '+feature.properties['shape']+' fa-stack-1x fa-inverse"></i></span>',
          iconSize: [40, 40],
          className: 'marker-icon'
        });
        layer.setIcon(numerciMarker);


        // This creates the contents of each chapter from the GeoJSON data. Unwanted items can be removed, and new ones can be added
        var chapter = jQuery('<p></p>', {
          text: feature.properties['name'],
          class: 'chapter-header'
        });

        var image = jQuery('<img>', {
          //width:'35vw',
          width: map_width/2 + 'px',
          alt: feature.properties['alt'],
          src: feature.properties['image']
        });


        //console.log(feature.properties['description'].replace(/\\"http/g, '"http'));
        var description = jQuery('<p></p>', {
          text: feature.properties['description'].replace(/\\"http/g, '"http'),
          class: 'description'
        });

        var container = jQuery('<div></div>', {
          id: 'container' + feature.properties['id'],
          class: 'image-container'
        });

        var imgHolder = jQuery('<div></div', {
          class: 'img-holder'
        });

        imgHolder.append(image);
        container.append(chapter).append(imgHolder).append(description);
        jQuery('#contents').append(container);

        var i;
        var areaTop = -200;
        var areaBottom = 0;

        // Calculating total height of blocks above active
        for (i = 1; i < feature.properties['id']; i++) {
          areaTop += jQuery('div#container' + i).height() + imageContainerMargin;
        }

        areaBottom = areaTop + jQuery('div#container' + feature.properties['id']).height();

        // Make markers clickable
        layer.on('click', function() {
          var numericMarker = new L.divIcon({
            html: '<span style="font-size:1.7em" class="fa-stack fa-2x"><i style="color:'+feature.properties['color']+'" class="fas fa-map-marker fa-stack-2x"></i><i style="color:'+feature.properties['shapeColor']+'" class="icon-marker '+feature.properties['shape']+' fa-stack-1x fa-inverse"></i></span>',
            iconSize: [40, 40],
            className: 'marker-icon'
          });

          markersArray[activeMarkerPosition].setIcon(numericMarker);
          for(var i=0; i<markersArray.length; i++){
            if(markersArray[i]._leaflet_id == layer._leaflet_id){
              activeMarkerPosition = i;
              break;
            }
          }

          setSelectedIcon();

          if(show_line.length > 0){
            redefineRouteLineOnClick();
          }

          map.setView([feature.geometry.coordinates[1], feature.geometry.coordinates[0] ], feature.properties['zoom']);
          //setTimeout(function(){ map.setZoom(feature.properties['zoom']);}, 1300);
          jQuery('.pannel').empty();
          jQuery('.pannel').append("<div class='point_title'>"+feature.properties['name']+"</div>");
          jQuery('.pannel').append(imgHolder);
          jQuery('.pannel').append("<div class='description'>"+feature.properties['description']+"</div>");
        });

      })(layer, feature.properties);
      markersArray.push(layer);
    }
  });


  // arrow controls
  // Create additional Control placeholders
  function addControlPlaceholders(map) {
      var corners = map._controlCorners,
          l = 'leaflet-',
          container = map._controlContainer;

      function createCorner(vSide, hSide) {
          var className = l + vSide + ' ' + l + hSide;

          corners[vSide + hSide] = L.DomUtil.create('div', className, container);
      }

      createCorner('verticalcenter', 'left');
      createCorner('verticalcenter', 'right');
  }
  addControlPlaceholders(map);

  var leftArrow = L.Control.extend({
    options: {
      position: 'verticalcenterleft'
    },
    onAdd: function (map) {
      var container = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom leftArrow');
      container.innerHTML = '<i class="fas fa-angle-double-left fa-2x"></i>';

      container.onclick = function(){
        activeMarkerPosition--;

        if(activeMarkerPosition == 0){
          unselectIcon();
          generateContentFromMarker(geojson._layers);
        }

        if(activeMarkerPosition < 0){
          activeMarkerPosition = 0;
        }

        if(activeMarkerPosition > 0){
          if(activeMarkerPosition < 0){
            activeMarkerPosition = 0;
          }
          if(activeMarkerPosition < markersArray.length && markersArray.length !=1){
            fillValuesOnClick(activeMarkerPosition);
            setSelectedIcon_B();
            setSelectedIcon();
            map.setView([markersArray[activeMarkerPosition].feature.geometry.coordinates[1], markersArray[activeMarkerPosition].feature.geometry.coordinates[0] ], markersArray[activeMarkerPosition].feature.properties['zoom']);
          }
        }

        if(activeMarkerPosition == 0){
          activeMarkerPosition = 1;
          firstLoad = true;
        }

        if(show_line.length > 0){
          lineDescendent()
        }

      }
      return container;
    },
  });

  var rightArrow = L.Control.extend({
    options: {
      position: 'verticalcenterright'
    },
    onAdd: function (map) {
      var container = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom rightArrow');

      if(jQuery(window).width() < 480){
        container.style.right = '2vw';
      } else {
        container.style.right = map_width/2 + 'px';
      }

      container.innerHTML = '<i class="fas fa-angle-double-right fa-2x"></i>';

      container.onclick = function(){
        if(firstLoad == false){
          activeMarkerPosition++;
        }
        if(firstLoad == true){
          firstLoad = false;
        }

        if(activeMarkerPosition >= 0){
          if(activeMarkerPosition >= markersArray.length){
            activeMarkerPosition = markersArray.length -1;
          }

          if(activeMarkerPosition < markersArray.length && markersArray.length !=1 ){

            if(show_line.length > 0){
              lineAscendent();
            }

            fillValuesOnClick(activeMarkerPosition);
            setSelectedIcon_A();
            setSelectedIcon();

            map.setView([markersArray[activeMarkerPosition].feature.geometry.coordinates[1], markersArray[activeMarkerPosition].feature.geometry.coordinates[0] ], markersArray[activeMarkerPosition].feature.properties['zoom']);
          }
        }

      }
      return container;
    },
  });


  var pannel = L.Control.extend({
    options: {
      position: 'topright'
    },
    onAdd: function (map) {
      this._div = L.DomUtil.create('div', 'pannel');
      L.DomEvent.disableClickPropagation(this._div);
      //this._div.style.width = '34vw';
      this._div.style.width = map_width/2 + 'px';
      this._div.style.height = '100vh';
  		this.update();
  	  return this._div;
    },
    update:function(d){
    }
  });


  map.addControl(new pannel());
  map.addControl(new leftArrow());
  map.addControl(new rightArrow());

  // function to get first object of markers
  function generateContentFromMarker(marcadors){
    for (var firstObjectKey in marcadors) break;
    jQuery('.pannel').empty();
    jQuery('.pannel').append("<div class='point_title'>"+story_name+"</div>");
    var image = jQuery('<img>', {
      //width:'35vw',
      width: map_width/2 + 'px',
      src: story_image
    });
    var imgHolder = jQuery('<div></div', {
      class: 'img-holder'
    });
    imgHolder.append(image);

    jQuery('.pannel').append(imgHolder);
    jQuery('.pannel').append("<div class='description'>"+story_description+"</div>");


  }

  generateContentFromMarker(geojson._layers);

  function fillValuesOnClick(pos){
    jQuery('.pannel').empty();
    jQuery('.pannel').append("<div class='point_title'>"+markersArray[pos].feature.properties.name+"</div>");
    var image = jQuery('<img>', {
      //width:'35vw',
      width: map_width/2 + 'px',
      alt: markersArray[pos].feature.properties['alt'],
      src: markersArray[pos].feature.properties['image']
    });
    var imgHolder = jQuery('<div></div', {
      class: 'img-holder'
    });
    imgHolder.append(image);
    jQuery('.pannel').append(imgHolder);
    jQuery('.pannel').append("<div class='description'>"+markersArray[pos].feature.properties.description.replace(/\\"http/g, '"http')+"</div>");
  }


  function setSelectedIcon(){
    var markerNumber = parseInt(markersArray[activeMarkerPosition].feature.properties['id']);

    var selectedIcon = new L.divIcon({
      html: '<span style="font-size:1.7em" class="fa-stack fa-2x"><i style="color:#FF8C00" class="fas fa-map-marker fa-stack-2x"></i><i style="color:'+markersArray[activeMarkerPosition].feature.properties['shapeColor']+'" class="icon-marker '+markersArray[activeMarkerPosition].feature.properties['shape']+' fa-stack-1x fa-inverse"></i></span>',
      iconSize: [40, 40],
      className: 'marker-icon'
    });

    markersArray[activeMarkerPosition].setIcon(selectedIcon);
  }

  function setSelectedIcon_A(){
    if(activeMarkerPosition > 1){
      var markerNumber = parseInt(markersArray[activeMarkerPosition].feature.properties['id']) -1;

      var selectedIcon = new L.divIcon({
        html: '<span style="font-size:1.7em" class="fa-stack fa-2x"><i style="color:'+markersArray[activeMarkerPosition-1].feature.properties['color']+'" class="fas fa-map-marker fa-stack-2x"></i><i style="color:'+markersArray[activeMarkerPosition-1].feature.properties['shapeColor']+'" class="icon-marker '+markersArray[activeMarkerPosition-1].feature.properties['shape']+' fa-stack-1x fa-inverse"></i></span>',
        iconSize: [40, 40],
        className: 'marker-icon'
      });

      markersArray[activeMarkerPosition-1].setIcon(selectedIcon);
    }
  }

  function unselectIcon(){

    var numericMarker = new L.divIcon({
      html: '<span style="font-size:1.7em" class="fa-stack fa-2x"><i style="color:'+markersArray[1].feature.properties['color']+'" class="fas fa-map-marker fa-stack-2x"></i><i style="color:'+markersArray[1].feature.properties['shapeColor']+'" class="icon-marker '+markersArray[1].feature.properties['shape']+' fa-stack-1x fa-inverse"></i></span>',
      iconSize: [40, 40],
      className: 'marker-icon'
    });

    markersArray[1].setIcon(numericMarker);
    map.fitBounds(geojson.getBounds())

  }

  function setSelectedIcon_B(){
    var markerNumber = parseInt(markersArray[activeMarkerPosition].feature.properties['id'])+1;

    var selectedIcon = new L.divIcon({
      html: '<span style="font-size:1.7em" class="fa-stack fa-2x"><i style="color:'+markersArray[activeMarkerPosition+1].feature.properties['color']+'" class="fas fa-map-marker fa-stack-2x"></i><i style="color:'+markersArray[activeMarkerPosition+1].feature.properties['shapeColor']+'" class="icon-marker '+markersArray[activeMarkerPosition+1].feature.properties['shape']+' fa-stack-1x fa-inverse"></i></span>',
      iconSize: [40, 40],
      className: 'marker-icon'
    });

    markersArray[activeMarkerPosition+1].setIcon(selectedIcon);
  }

  function lineAscendent(){
    var lat = markersArray[activeMarkerPosition]._latlng.lat;
    var lon = markersArray[activeMarkerPosition]._latlng.lng;
    var pointc = new L.LatLng(lat,lon);
    if(routeline.getLatLngs().length <= markersArray.length){
      routeline.addLatLng(pointc);
    }

  }

  function lineDescendent(){
    var vertex = routeline.getLatLngs();
    vertex.pop()

    //create a temporal routeline
    var routeline_ = new L.Polyline(vertex, {
        color: 'red',
        weight: 3,
        dashArray: '10, 10',
        dashOffset: '20',
        smoothFactor: 1
    });
    routeline_.addTo(map)

    routeline.setLatLngs(vertex);

    //remove the temporal routeline
    map.removeLayer(routeline_);
  }

  function redefineRouteLineOnClick(){
    var newVertexCoor = [];
    var lineCoor = fullLine.getLatLngs();
    for(var i=0; i< activeMarkerPosition; i++){
      newVertexCoor.push(lineCoor[i])
    }
    routeline.setLatLngs(newVertexCoor)

  }

  geojson.addTo(map);
  map.fitBounds(geojson.getBounds())


  // MiniMap
  var osmUrl='https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
  var osm2 = new L.TileLayer(osmUrl);
  var miniMap = new L.Control.MiniMap(osm2, { toggleDisplay: true, zoomLevelOffset:-3, position:'bottomleft' }).addTo(map);



}

initMap();
