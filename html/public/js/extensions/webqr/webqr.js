var gCtx = null;
var gCanvas = null;
var c = 0;
var stype = 0;
var gUM = false;
var webkit = false;
var moz = false;
var v = null;
var facing = true;

var vidhtml = '<video id="v" autoplay></video>';

function dragenter(e) {
  e.stopPropagation();
  e.preventDefault();
}

function dragover(e) {
  e.stopPropagation();
  e.preventDefault();
}
function drop(e) {
  e.stopPropagation();
  e.preventDefault();

  var dt = e.dataTransfer;
  var files = dt.files;
  if (files.length>0) {
	handleFiles(files);
  } else if (dt.getData('URL')) {
	qrcode.decode(dt.getData('URL'));
  }
}

function handleFiles(f)
{
	var o=[];

	for(var i =0;i<f.length;i++) {
        var reader = new FileReader();
        reader.onload = (function(theFile) {
          return function(e) {
            gCtx.clearRect(0, 0, gCanvas.width, gCanvas.height);
            qrcode.decode(e.target.result);
          };
        })(f[i]);
        reader.readAsDataURL(f[i]);
    }
}

function initCanvas(w,h)
{
    gCanvas = document.getElementById("qr-canvas");
    gCanvas.style.width = w + "px";
    gCanvas.style.height = h + "px";
    gCanvas.width = w;
    gCanvas.height = h;
    gCtx = gCanvas.getContext("2d");
    gCtx.clearRect(0, 0, w, h);
}


function captureToCanvas() {
    if (stype!=1)
        return;
    if (gUM) {
        try {
            gCtx.drawImage(v,0,0);
            try {
                qrcode.decode();
            } catch(e) {
                setTimeout(captureToCanvas, 500);
            };
        } catch(e) {
            setTimeout(captureToCanvas, 500);
        };
    }
}

function htmlEntities(str)
{
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function isCanvasSupported()
{
    var elem = document.createElement('canvas');
    return !!(elem.getContext && elem.getContext('2d'));
}

function success(stream)
{
    v.srcObject = stream;
    v.play();

    gUM = true;
    setTimeout(captureToCanvas, 500);
}

function error2(error)
{
    gUM = false;
    return;
}

function loadCamera()
{
	if (isCanvasSupported() && window.File && window.FileReader) {
		initCanvas(800, 600);
		qrcode.callback = scanCallback;
        document.getElementById("scanner-stream").style.display="block";
        setwebcam();
	} else {
		loadScanAlert('camera_error');
	}
}

function setwebcam()
{
	var options = true;
	if(navigator.mediaDevices && navigator.mediaDevices.enumerateDevices)
	{
		try {
			navigator.mediaDevices.enumerateDevices()
			.then(function(devices) {
			  devices.forEach(function(device) {
				if (device.kind === 'videoinput') {
				  if(device.label.toLowerCase().search("back") >-1)
					options = {'facingMode': facing ? 'environment' : 'user'} ;
				}
			  });
			  setwebcam2(options);
			});
		} catch(e) {
            loadScanAlert('camera_error');
		}
	} else {
		console.log("no navigator.mediaDevices.enumerateDevices" );
		setwebcam2(options);
	}
}

function setwebcam2(options)
{
    if(stype==1)
    {
        setTimeout(captureToCanvas, 500);
        return;
    }
    var n=navigator;
    document.getElementById("scanner-stream").innerHTML = vidhtml;
    v=document.getElementById("v");

    try {
        if(n.mediaDevices.getUserMedia)
        {
            n.mediaDevices.getUserMedia({video: options, audio: false}).
                then(function(stream){
                    success(stream);
                    $('#scanner').modal('show');
                }).catch(function(error){
                    loadScanAlert('camera_error');
                    error2(error);
                });
        } else if (n.getUserMedia) {
    		webkit=true;
            n.getUserMedia({video: options, audio: false}, success, error);
    	} else if (n.webkitGetUserMedia) {
            webkit=true;
            n.webkitGetUserMedia({video:options, audio: false}, success, error);
        }
    } catch (e) {
        loadScanAlert('camera_error');
    }

    stype=1;
    setTimeout(captureToCanvas, 500);
}

function stopCamera()
{
    $("#scanner-stream").hide();
    if (v) {
        v.pause();
        if (v.srcObject) {
            v.srcObject.getTracks()[0].stop();
        }
    }
    gCtx = null;
    gCanvas = null;
    c = 0;
    stype = 0;
    gUM = false;
    webkit = false;
    moz = false;
    v = null;
}

function switchCamera() {
    if (v == null) return;
    v.srcObject.getTracks().forEach(t => {
        t.stop();
    });
    gCtx = null;
    gCanvas = null;
    c = 0;
    stype = 0;
    gUM = false;
    webkit = false;
    moz = false;
    v = null;
    facing = !facing;
    loadCamera();
}
