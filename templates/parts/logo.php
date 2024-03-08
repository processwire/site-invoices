<?php namespace ProcessWire;

/**
 * Render logo <img> for invoice profile
 * 
 */

// max height for logo image in px (adjust as needed)
$logoHeight = 120; 
$logo = settings()->image;

if($logo) {
	$logo1x = $logo->maxHeight($logoHeight);
	$logo2x = $logo->maxHeight($logoHeight * 2); // for hidpi
	$website = settings()->website;
	$alt = $logo->description;
	$style = "max-height:{$logoHeight}px";
	$img = "<img id='logo' src='$logo1x->url' srcset='$logo2x->url 2x' alt='$alt' style='$style'>";
	if($website) $img = "<a href='$website'>$img</a>";
	echo $img;
} else {
	echo "<!-- no logo image available -->";
}