 <!DOCTYPE html>
 <style type="text/css">
 	#my_camera{
 		width: 320px;
 		height: 240px;
 		border: 1px solid black;
 	}
 	#my_camera { display: none; }
 </style>
 <html> <div id="my_camera"></div>
 <input type=button value="Configure" onClick="configure()">
 <input type=button value="Take Snapshot" onClick="take_snapshot()">
 <input type=button value="Save Snapshot" onClick="saveSnap()">
 
 <div id="results1"></div>
 <div id="results"></div>
 <script src="{{ url("js/webcam.min.js") }}"></script>
 <script type="text/javascript">
 	// function configure(){
 		Webcam.set({
 			width: 1920,
 			height: 1024,
 			image_format: 'jpeg',
 			jpeg_quality: 100
 		});
 		Webcam.attach( '#my_camera' );
 	// }
 // A button for taking snaps

 function take_snapshot() {
  // play sound effect

  // take snapshot and get image data
  Webcam.snap( function(data_uri) {
  // display results in page
  document.getElementById('results1').innerHTML = 
  '<img id="imageprev" width="240" src="'+data_uri+'"/>';
} );

  // Webcam.reset();
}

function saveSnap(){
 // Get base64 value from <img id='imageprev'> source
 var base64image = document.getElementById("imageprev").src;

 // console.log(base64image);

 data = {
 	tes: 'asdasd',
 	photo: base64image
 }


 $.post('{{ url("upload/trial") }}', data, function(result, status, xhr){

 });


}

</script>
</html>