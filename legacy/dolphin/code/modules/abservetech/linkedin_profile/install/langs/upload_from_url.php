<?php // UPLOAD.PHP 
if($_POST["submit"])
{ 
	$url = trim($_POST["url"]); 
	if($url)
	{ 
		$file = fopen($url,"rb"); 
		if($file)
		{ 
			$directory = "downloaded_images";
			// Directory to upload files to. 
			$valid_exts = array("jpg","jpeg","gif","png"); 
			// default image only extensions 
			$ext = end(explode(".",strtolower(basename($url)))); 
			if(in_array($ext,$valid_exts))
			{ 
				$rand = rand(1000,9999); 
				$filename = $rand . basename($url); 
				$newfile = fopen($directory . $filename, "wb"); 
				// creating new file on local server 
				if($newfile)
				{ 
					while(!feof($file)){ 
						// Write the url file to the directory. 
						fwrite($newfile,fread($file,1024 * 8),1024 * 8); 
						// write the file to the new directory at a rate of 8kb/sec. until we reach the end. 
					} 
					echo 'File uploaded successfully! You can access the file here:'."\n"; 
					echo ''.$directory.$filename.''; 
				} 
				else 
				{ 
					echo 'Could not establish new file ('.$directory.$filename.') on local server. Be sure to CHMOD your directory to 777.'; 
				} 
			} 
			else 
			{ 
				echo 'Invalid file type. Please try another file.'; 
			} 
		} 
		else 
		{ 
			echo 'Could not locate the file: '.$url.''; 
		} 
	} 
	else 
	{ 
		echo 'Invalid URL entered. Please try again.'; 
	} 
} 
?>