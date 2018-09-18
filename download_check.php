<?php error_reporting(0);
$filepath = urldecode($_GET['path']);
session_start();
if(!file_exists($filepath)) {
	header('HTTP/1.0 404 Not Found');
	include('404.html');
} else if(!isset($_SESSION['contactid']) && !in_array(explode('/',$filepath)[0],['css','img','Settings','Inventory','Website','Contacts']) && stripos($_GET['path'],'.jpg') === FALSE && stripos($_GET['path'],'.png') === FALSE && stripos($_GET['path'],'.gif') === FALSE && stripos($_GET['path'],'.bmp') === FALSE) {
	header('HTTP/1.0 403 Forbidden');
	include('403.html');
} else {
	$mime = 'text/html';
	switch(strtolower(pathinfo($filepath, PATHINFO_EXTENSION))) {
		case 'css': $mime = 'text/css'; break;
		case 'htc': $mime = 'text/x-component'; break;
		case 'js': $mime = 'application/x-javascript'; break;
		case 'js2': $mime = 'application/javascript'; break;
		case 'js3': $mime = 'text/javascript'; break;
		case 'js4': $mime = 'text/x-js'; break;
		case 'htm':
		case 'html': $mime = 'text/html'; break;
		case 'rtf':
		case 'rtx': $mime = 'text/richtext'; break;
		case 'svg':
		case 'svgz': $mime = 'image/svg+xml'; break;
		case 'csv': $mime = 'text/csv'; break;
		case 'txt': $mime = 'text/plain'; break;
		case 'xsd': $mime = 'text/xsd'; break;
		case 'xsl': $mime = 'text/xsl'; break;
		case 'xml': $mime = 'text/xml'; break;
		case 'asf':
		case 'asx':
		case 'wax':
		case 'wmv':
		case 'wmx': $mime = 'video/asf'; break;
		case 'avi': $mime = 'video/avi'; break;
		case 'bmp': $mime = 'image/bmp'; break;
		case 'divx': $mime = 'video/divx'; break;
		case 'doc':
		case 'docx': $mime = 'application/msword'; break;
		case 'eot': $mime = 'application/vnd.ms-fontobject'; break;
		case 'exe': $mime = 'application/x-msdownload'; break;
		case 'gif': $mime = 'image/gif'; break;
		case 'gz':
		case 'gzip': $mime = 'application/x-gzip'; break;
		case 'ico': $mime = 'image/x-icon'; break;
		case 'jpg':
		case 'jpeg':
		case 'jpe': $mime = 'image/jpeg'; break;
		case 'json': $mime = 'application/json'; break;
		case 'mdb': $mime = 'application/vnd.ms-access'; break;
		case 'mid':
		case 'midi': $mime = 'audio/midi'; break;
		case 'mov':
		case 'qt': $mime = 'video/quicktime'; break;
		case 'mp3':
		case 'm4a': $mime = 'audio/mpeg'; break;
		case 'mp4':
		case 'm4v': $mime = 'video/mp4'; break;
		case 'mpeg':
		case 'mpg':
		case 'mpe': $mime = 'video/mpeg'; break;
		case 'mpp': $mime = 'application/vnd.ms-project'; break;
		case 'otf': $mime = 'application/x-font-otf'; break;
		case '_otf': $mime = 'application/vnd.ms-opentype'; break;
		case 'odb': $mime = 'application/vnd.oasis.opendocument.database'; break;
		case 'odc': $mime = 'application/vnd.oasis.opendocument.chart'; break;
		case 'odf': $mime = 'application/vnd.oasis.opendocument.formula'; break;
		case 'odg': $mime = 'application/vnd.oasis.opendocument.graphics'; break;
		case 'odp': $mime = 'application/vnd.oasis.opendocument.presentation'; break;
		case 'ods': $mime = 'application/vnd.oasis.opendocument.spreadsheet'; break;
		case 'odt': $mime = 'application/vnd.oasis.opendocument.text'; break;
		case 'ogg': $mime = 'audio/ogg'; break;
		case 'pdf': $mime = 'application/pdf'; break;
		case 'png': $mime = 'image/png'; break;
		case 'pot':
		case 'pps':
		case 'ppt':
		case 'pptx': $mime = 'application/vnd.ms-powerpoint'; break;
		case 'ra':
		case 'ram': $mime = 'audio/x-realaudio'; break;
		case 'swf': $mime = 'application/x-shockwave-flash'; break;
		case 'tar': $mime = 'application/x-tar'; break;
		case 'tif':
		case 'tiff': $mime = 'image/tiff'; break;
		case 'ttf':
		case 'ttc': $mime = 'application/x-font-ttf'; break;
		case '_ttf': $mime = 'application/vnd.ms-opentype'; break;
		case 'wav': $mime = 'audio/wav'; break;
		case 'wma': $mime = 'audio/wma'; break;
		case 'wri': $mime = 'application/vnd.ms-write'; break;
		case 'woff': $mime = 'application/font-woff'; break;
		case 'woff2': $mime = 'application/font-woff2'; break;
		case 'xla':
		case 'xls':
		case 'xlsx':
		case 'xlt':
		case 'xlw': $mime = 'application/vnd.ms-excel'; break;
		case 'zip': $mime = 'application/zip'; break;
		case 'eps': $mime = 'image/eps'; break;
	}
	header("Content-Length: " . filesize ( $filepath ) );
	header("Content-type: ".$mime);
	header("Content-disposition: inline; filename=".basename($filepath));
	header('Expires: '.date('D, d M Y H:i:s',strtotime(0)));
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	ob_clean();
	flush();
	readfile($filepath);
}
