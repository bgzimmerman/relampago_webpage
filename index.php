<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>RELAMPAGO Model Viewer</title>
    <link rel="stylesheet" type="text/css" href="css/main.css?v=<?=time();?>">
    <link rel="stylesheet" href="css/jquery-ui.css">

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/full-slider.css" rel="stylesheet">



    <script src="js/moment.min.js"></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
<?php
        $models = array("gefs", "smneta", "dmcwrf", "inmetcosmo", "gfs", "wrfens");

	$formats = array("gefs" => ".png",
		"smneta" => ".gif",
	        "dmcwrf" => ".png",
		"inmetcosmo" => ".png",
		"gfs" => ".png",
		"wrfens" => ".png",
	);
	
        $inits = array("gefs" => "",
		"smneta" => "",
	        "dmcwrf" => "",
	        "inmetcosmo" => "",
	        "gfs" => "",
	        "wrfens" => "",
	);

	$latest = array("gefs" => "",
		"smneta" => "",
	        "dmcwrf" => "",
	        "inmetcosmo" => "",
	        "gfs" => "",
	        "wrfens" => "",
	);

	$orddate = array("gefs" => "",
		"smneta" => "",
	        "dmcwrf" => "",
	        "inmetcosmo" => "",
	        "gfs" => "",
	        "wrfens" => "",
	);

	foreach ($models as $mod) {
		$datesmod = glob( $mod . '/*' );
		$orddates = array_reverse( $datesmod );
		$orddate[$mod] = $orddates;
		$latest[$mod] = substr($orddates[0],-10);
		$inits[$mod] = (isset($_GET["init"])) ? $_GET["init"] : $latest[$mod];
	}

	/*
	$dirgefs = "gefs_graphics/*";
	$datesgefs = glob( $dirgefs );
	$orddatesgefs = array_reverse( $datesgefs );
	$latestgefs = substr($orddatesgefs[0],-10);
	$initgefs = (isset($_GET["init"])) ? $_GET["init"] : $latestgefs;
	
	
  	$dirsmn = "smn_graphics/*";
	$datessmn = glob( $dirsmn );
	$orddatessmn = array_reverse( $datessmn );
	$latestsmn = substr($orddatessmn[0],-10);
	$initsmn = (isset($_GET["init"])) ? $_GET["init"] : $latestsmn;
	 */

	$product = (isset($_GET["product"])) ? $_GET["product"] : "slp";
	$model = (isset($_GET["model"])) ? $_GET["model"] : "gefs";
	$dmdt = (isset($_GET["dmdt"])) ? $_GET["dmdt"] : "none";
	$startimg = (isset($_GET["start"])) ? $_GET["start"] : 1;

	// Find our images here
	$prod = $product;
	$mod = $model;
		if ($dmdt !== 'none') {
			$images = [];
       			foreach ( $orddate[$mod] as $daten ):
            			$dir = $daten . "/" . $product . "_" . $dmdt . "_*" . $formats[$mod];
            			$files = glob( $dir );
            			if (!empty($files)) {
                			$images[] = $files[0];
				};
		        endforeach;

	        } else {
		 
	                //echo "./" . $mod . "/" . $inits[$mod] . "/" . $product . "*" . $formats[$mod];
        		$dir = "./" . $mod . "/" . $inits[$mod] . "/" . $product . "*" . $formats[$mod];
	                //echo $dir;
	                $images = glob( $dir );
	
		};
       $numimg = count($images);
?>
<script src="js/jquery.min.js"></script>
<script src="js/jquery-ui.min.js"></script>
<script src="js/jquery.hotkeys.js"></script>
<script src="js/menus.js"></script>

<script type='text/javascript'>
$(document).ready(function() {
    $(document).bind('keydown', 'left right . ,', showImage);    
    $(document).bind('keydown', 'h s', showheader);
    $(document).bind('keydown', 'r', loadNewField);
    //$(document).bind('keydown', 'o', showOverlay);
    //$(document).bind('keydown', 'v', showObs);

/*    
    $( "#datepicker" ).datepicker({
        minDate: "00 UTC Tue 7 Apr 2015",
        maxDate: new Date("2017-06-02"),
        dateFormat: "00 UTC D dd M yy", 
        onSelect: changeDate,
        buttonImage: "calendar_small.png",
        showOn: "button",
        buttonImageOnly: true
    });
 */

    loadImages();
    /*
    if (verif == 1) { showObs(); }
     */
    window.setInterval(function() {
        if (imagesLoaded.length != window.imagelist.length) loadImages();
    }, 60000);
});


function loadNewField() {
    //$('img#mainimage').fadeOut(100);
    loadImages();
    //$('img#mainimage').fadeIn(100);
}

function showheader() { $('div#header').slideToggle("slow"); }
/*
function showOverlay() { $("#overlaybox").toggle(); }
function showObs() { loadObs(); $("#overlayboxobs").toggle(); }
 */
function showImage(e) {
    imagesLoaded.sort(function(a, b){return a-b});
    thisIndex = jQuery.inArray(activehr, imagesLoaded);

    if (e.keyCode == 37 || e.keyCode == 188) nextIndex = thisIndex-1;
    else if (e.keyCode == 39 || e.keyCode == 190) nextIndex = thisIndex+1;

    if (nextIndex > imagesLoaded.length-1) nextIndex = 0;
    if (nextIndex < 0) nextIndex = imagesLoaded.length-1;

    activehr = imagesLoaded[nextIndex];
    var indx = (activehr-start)/interval;

    $("div.rollover ").removeClass("selected");
    $("div#"+activehr+".rollover").addClass("selected");
    $('img#mainimage').attr("src", window.imagelist[indx]);
    $('img#overlayimageobs').attr("src", window.obslist[indx]);
}

function loadImages() {
    // preload images here
    //activehr = 0; 

    imagesLoaded = new Array();    
    window.images = new Array();
    for (var i = 0; i < window.imagelist.length; i++) {
        window.images[i]= new Image();               // initialize array of image objects
        window.images[i].onload = function() {
            // figure out which forecast hour this is
            var indexes = this.src.match(/f[0-9]{3,4}/);
            var index = indexes.pop();
            //var fhr  = this.src.substr(index.index+1, 3);
	    var fhr  = index.substr(1, 3);
            var thisrollover = $("div#"+parseInt(fhr, 10)+".rollover")
            imagesLoaded.push(parseInt(fhr,10))
            // change class
            thisrollover.addClass("loaded");

            // attach mouseover 
            thisrollover.mouseover(function() {
                var fcsthr = $( this ).attr('id');
                activehr = parseInt(fcsthr, 10);
                // change class for rollover
                $("div.rollover ").removeClass("selected");
                $( this ).addClass("selected");
                var indx = (activehr-start)/interval; 
                $('img#mainimage').attr("src", window.imagelist[indx])
                $('img#overlayimageobs').attr("src", window.obslist[indx]);
            });
        };
        window.images[i].src = window.imagelist[i];    // src of image
    }
}

function loadObs() {
    window.obsimages = new Array();
    for (var i = 0; i < window.obslist.length; i++) {
        window.obsimages[i]= new Image();
        // put blank image in place of image if obs overlay not
        // available, otherwise empty image will show up in browser
        window.obsimages[i].onerror = function() {
             var fname = this.src.substring(this.src.lastIndexOf('/')+1);
             for (var j=0; j<window.obslist.length; j++) {
                 var match = window.obslist[j].lastIndexOf(fname);
                 if (match >= 0) { window.obslist[j] = "missing.png"; break; }
             }
        }
        window.obsimages[i].src = window.obslist[i];
    }
}

var yyyymmddhh = '<?php echo $inits[$model]; ?>';
var field = '<?php echo $product; ?>';
var region = 'CONUS';
var start = '<?php 
	$ismatch = preg_match('/f[0-9]{3}/', $images[0], $matches);
       	$fhourzero= (int)substr($matches[0],1,3);
	$ismatch = preg_match('/f[0-9]{3}/', $images[1], $matches);
       	$fhourone= (int)substr($matches[0],1,3);
        echo $fhourzero; ?>';
var interval = '<?php echo $fhourone-$fhourzero; ?>';
var verif = '0'
var imagelist = [
<?php
	foreach ($images as $img):
            echo "'" . $img . "',";
        endforeach;

?>
];
var obslist = [
];
</script>




</head>

<body>


    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="http://www.atmos.washington.edu/~lmadaus/relampago">RELAMPAGO</a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <!-- GEFS Ensemble Here -->
                    <li class="dropdown">
                        <a href="#"  class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">GEFS <span class="caret"></span></a>

                      <ul class="dropdown-menu">
		      <li><a href="<?php echo 'index.php?init=' . $latest['gefs'] . '&model=gefs&product=precip06' ?>">6-hr Precipitation Mean & Spread</a></li>
		      <li><a href="<?php echo 'index.php?init=' . $latest['gefs'] . '&model=gefs&product=hgt500' ?>">500 hPa Height Mean and Spread</a></li>
		      <li><a href="<?php echo 'index.php?init=' . $latest['gefs'] . '&model=gefs&product=slp' ?>">SLP Mean and Spread</a></li>
		      <li><a href="<?php echo 'index.php?init=' . $latest['gefs'] . '&model=gefs&product=cape' ?>">MUCAPE Mean and Spread</a></li>
		      <li><a href="<?php echo 'index.php?init=' . $latest['gefs'] . '&model=gefs&product=2mRH' ?>">2m Relative Humidity Mean and Spread</a></li>
		      <li><a href="<?php echo 'index.php?init=' . $latest['gefs'] . '&model=gefs&product=2mtemp' ?>">2m Temperature Mean and Spread</a></li>
                      </ul>
	           </li>


                    <!-- GFS 0.25 deg here -->
                    <li class="dropdown">
                        <a href="#"  class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">GFS <span class="caret"></span></a>

                      <ul class="dropdown-menu">
		      <li><a href="<?php echo 'index.php?init=' . $latest['gfs'] . '&model=gfs&product=precip' ?>">Precipitation</a></li>
		      <li><a href="<?php echo 'index.php?init=' . $latest['gfs'] . '&model=gfs&product=pwat' ?>">Precipitable Water</a></li>
		      <li><a href="<?php echo 'index.php?init=' . $latest['gfs'] . '&model=gfs&product=2mtemp' ?>">Air Temperature (2m)</a></li>
		      <li><a href="<?php echo 'index.php?init=' . $latest['gfs'] . '&model=gfs&product=2mRH' ?>">Relative Humidity(2m)</a></li>
                      </ul>
	           </li>

                    <!-- WRF Ensemble here -->
                    <li class="dropdown">
                        <a href="#"  class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">WRF Ensemble <span class="caret"></span></a>

                      <ul class="dropdown-menu">
                      <li><a href="<?php echo 'index.php?init=' . $latest['wrfens'] . '&model=wrfens&product=wrfens_crefprob' ?>">Prob CREF > 30 dBZ</a></li>
                      <li><a href="<?php echo 'index.php?init=' . $latest['wrfens'] . '&model=wrfens&product=wrfens_t2spread' ?>">2m Temp Spread and 10m Wind</a></li>
                      </ul>
                   </li>


                    <!-- SMN ETA Here -->
                    <li class="dropdown">
                        <a href="#"  class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">SMN/ETA <span class="caret"></span></a>

                      <ul class="dropdown-menu">
		      <li><a href="<?php echo 'index.php?init=' . $latest['smneta'] . '&model=smneta&product=precip24' ?>">24-hr Precipitation</a></li>
		      <li><a href="<?php echo 'index.php?init=' . $latest['smneta'] . '&model=smneta&product=precip03' ?>">3-hr Precip and 10m Wind</a></li>
		     <li role="separator" class="divider"></li>
		      <li><a href="<?php echo 'index.php?init=' . $latest['smneta'] . '&model=smneta&product=slp' ?>">SLP and 1000-500 hPa Thickness</a></li>
		      <li><a href="<?php echo 'index.php?init=' . $latest['smneta'] . '&model=smneta&product=t2rh' ?>">2m Temperature and RH</a></li>
		      <li><a href="<?php echo 'index.php?init=' . $latest['smneta'] . '&model=smneta&product=u10rdp' ?>">10m Wind over Rio de la Plata</a></li>
                      </ul>
	           </li>


                    <!-- DMC WRF Here -->
                    <li class="dropdown">
                        <a href="#"  class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">DMC/WRF <span class="caret"></span></a>

                      <ul class="dropdown-menu">
		      <li><a href="<?php echo 'index.php?init=' . $latest['dmcwrf'] . '&model=dmcwrf&product=precip03' ?>">3-hr Precipitation</a></li>
		      <li><a href="<?php echo 'index.php?init=' . $latest['dmcwrf'] . '&model=dmcwrf&product=u10rainc' ?>">3-hr Convective Precip and 10m Wind</a></li>
		      <li><a href="<?php echo 'index.php?init=' . $latest['dmcwrf'] . '&model=dmcwrf&product=capepwat' ?>">CAPE and Precipitable Water</a></li>
		      <li><a href="<?php echo 'index.php?init=' . $latest['dmcwrf'] . '&model=dmcwrf&product=slp' ?>">Sea-level Pressure</a></li>
		      <li><a href="<?php echo 'index.php?init=' . $latest['dmcwrf'] . '&model=dmcwrf&product=vort500' ?>">500 hPa Height and Vorticity</a></li>
                      </ul>
	           </li>


                    <!-- INMET COSMO Here -->
                    <li class="dropdown">
                        <a href="#"  class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">INMET/COSMO <span class="caret"></span></a>

                      <ul class="dropdown-menu">
		      <li><a href="<?php echo 'index.php?init=' . $latest['inmetcosmo'] . '&model=inmetcosmo&product=precip24' ?>">24-hr Precipitation</a></li>
		      <li><a href="<?php echo 'index.php?init=' . $latest['inmetcosmo'] . '&model=inmetcosmo&product=precip03' ?>">3-hr Precipitation</a></li>
		      <li><a href="<?php echo 'index.php?init=' . $latest['inmetcosmo'] . '&model=inmetcosmo&product=capek' ?>">CAPE and K-index</a></li>
		      <li><a href="<?php echo 'index.php?init=' . $latest['inmetcosmo'] . '&model=inmetcosmo&product=pwat850' ?>">850 hPa Height and Precipitable Water</a></li>
		      <li><a href="<?php echo 'index.php?init=' . $latest['inmetcosmo'] . '&model=inmetcosmo&product=h500' ?>">500 hPa Height</a></li>
                      </ul>
	           </li>
                </ul>


                <!-- This is the right-side control bar -->
		<ul class="nav navbar-nav navbar-right">
		<!-- DM/DT here -->
		<li><a href="#" id="dmdt">dM/dT</a></li>
		<!-- View previous dates here -->
		

    	 	<li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-e
xpanded="false">Model Date <span class="caret"></span></a>
                <ul class="dropdown-menu">
                <?php

                  //$dir2 = "wrf_plots/*";
                  //$dates = glob( $dir2 );
                  //$orddates = array_reverse($dates);
		  $datesmod = glob( $model . '/*' );
		  $orddates = array_reverse( $datesmod );
                  foreach ( $orddates as $daten ):
                          echo '<li><a href="index.php?init='. substr($daten,-10) . '&product=' . $product . '&model=' . $model . '">'. substr($daten, -10) . '</a></li>';
                  endforeach;

                  ?>
		</ul>
</div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>


    <!-- Page Content -->
    <div id="maincontainer" class="container">
<div id="bodycontainer" style="clear: both;">

<div id="rolloverdiv">
<div id="rollovercenter">
<?php
   $inum = 0;
   foreach ($images as $img):
      $inum += 1;
       $ismatch = preg_match('/f[0-9]{3,4}/', $img, $matches);
       $fhour = (int)substr($matches[0],1,4);
      
       if ($inum == 1) {
           echo '<div class="rollover selected" id="'. $fhour .'">' . $fhour . '</div>';
       } else {
           echo '<div class="rollover" id="'. $fhour. '">' . $fhour . '</div>';
       };
   endforeach;
?>
<br style="clear:both;"/>
</div>
</div>
<div id="imagebox" style="position: relative; width: 100%">
<img id="mainimage" src="<?php echo $images[0]; ?>" style="display: block; margin: 0 auto; max-width:100%; max-height:100%;" alt='main image'/>

</div>



        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright &copy;  2017</p>
                </div>
            </div>
            <!-- /.row -->
        </footer>

    </div>
    <!-- /.container -->

    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>


    <!-- Script to Activate the Carousel -->
    <script>



</body>

</html>
