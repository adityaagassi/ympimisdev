<!-- This work is licensed under the W3C Software and Document License
     (http://www.w3.org/Consortium/Legal/2015/copyright-software-and-document).
 -->
 <html>
 <head>
 	<title>TES 1</title>
 	<meta name="viewport" content="width=device-width">
 	<style>
 		.error {
 			color: #d22;
 		}
 		input[type="button"] {
 			height: 30px
 		}
 		.cropped {
 			width: 200px;
 			height: 200px;
 			background-position: center center;
 			background-repeat: no-repeat;
 		}

 	</style>
 </head>
 <body>
 	<table border="1">
 		<tr>
 			<td colspan="2" style="text-align: center;">
 				<b>AP-215</b> <br>
 				Transformer T-Pro-A-TRN-T-PRO-02
 			</td>
 		</tr>
 		<tr>
 			<td style="width: 50%"><img src="{{url('qr.png')}}" class="cropped"></td>
 			<td style="text-align: left" id="title">
 				Exp. 02-07-2020 <br>
 				Last Check : 06-04-2020 (BAIK)
 			</td>
 		</tr>
 	</table>
 	<script type="text/javascript">
 		function onLoad() {     
 			if (navigator.share === undefined) {

 				if (window.location.protocol === 'http:') {

 					window.location.replace(window.location.href.replace(/^http:/, 'https:'));

 					const title_input = document.querySelector('#title');

 					const title = title_input.disabled ? undefined : title_input.files;

 					navigator.share({title});
 				} else {
 					logError('Error: You need to use a browser that supports this draft ' +
 						'proposal.');
 				}
 			}
 		}

 		function logError(message) {
 			logText(message, true);
 		}

 		function logText(message, isError) {
 			if (isError)
 				console.error(message);
 			else
 				console.log(message);

 			const p = document.createElement('p');
 			if (isError)
 				p.setAttribute('class', 'error');
 			document.querySelector('#output').appendChild(p);
 			p.appendChild(document.createTextNode(message));
 		}

 		window.addEventListener('load', onLoad);
 	</script>
 </body>
 </html>
