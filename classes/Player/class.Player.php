<?php

class Player {

	private $_confi, $_options;

	function __construct($options) {
	
		$this->_options = $options;
		$this->setConf();

	}

	public function render($container) {
	
		$buffer = "<script type=\"text/javascript\">\n";
		$buffer .= "var player = \$f(\"$container\", \"".REL_FLOWPLAYER."/flowplayer.commercial-3.2.7.swf\", $this->_conf).ipad();\n";
		$buffer .= "</script>";
		
		return $buffer;

	}

	public function setConf() {

		$address = "http://".$_SERVER['SERVER_NAME'];
		$address .= preg_match("#/#", $_SERVER['PHP_SELF'])
				? substr($_SERVER['PHP_SELF'],0,strrpos($_SERVER['PHP_SELF'],'/'))
				: $_SERVER['PHP_SELF'];
	
		/*
		 * All
		 */
		$this->_conf = "{
			key: '\$e6e022cb180d557846d',
			logo: {
				display: 'none'
			},
			autoPlay:true,
			autoBuffering:false,";

		/*
		 * OnDemand
		 */
		if($this->_options['viewType']=='ondemand') {
			$this->_conf .= "
				plugins: { 

					controls: {
						timeColor: '#99ff00',
		   				backgroundColor: '#333333',
		   				buttonColor: '#666666'
	  				},
					rtmp: {   

	      					// use latest RTMP plugin release 
						url: 'flowplayer.rtmp-3.2.3.swf',             

     						netConnectionUrl: 'rtmp://".Configuration::getValue('streamingAddress')."'   
      					},
					content: {
						url: 'flowplayer.content-3.2.0.swf',
     							display: 'none',
     							bottom: 26,
     							width: '99%',
     							height: '30',
     							opacity: '0',
     							background: '#000000',
     							borderRadius: '0',
     							border: '0px solid #000000',
     							backgroundGradient: [0.2,0]
		 			}   
				},";

			if(!$this->_options['initVideo'] && Configuration::getValue('onDemandSwf') && preg_match("#\.swf$#", Configuration::getValue('onDemandSwf'))) {
				$url = "url:'$address/".REL_UP_SWF."/".Configuration::getValue('onDemandSwf')."',";
			}		
			elseif(!$this->_options['initVideo'] && Configuration::getValue('onDemandSwf') && preg_match("#\.(jpg|png)$#", Configuration::getValue('onDemandSwf'))) {
				$url = '';
				$this->_conf .= "canvas: { backgroundImage:'url(".Configuration::getValue('onDemandSwf').")'},";
			}
			elseif($this->_options['initVideo']) {
				$video = new Video($this->_options['initVideo'], TBL_VIDEO);
				$url = "url:'".$video->ml('name')."', provider: 'rtmp'".($video->name_html5 
					? ", ipadUrl:'http://".Configuration::getValue('httpAddress')."/".$video->ml('name_html5')."'" 
					: "").","; 
				// update views
				VideoInterface::updateView($video->id);
			}
			$this->_conf .= "			
					clip: {
						$url
						onLastSecond: function() {
		      					if(this.getPlugin('content').display=='block') {
			      					this.getPlugin('content').animate({opacity: 0.1}, 2000);
			      					setTimeout(function() { this.getPlugin('content').css({'display':'none'})}.bind(this), 1000);
		      					}
	      					}

					}";

		}
		/*
		 * OnAir
		 */
		elseif($this->_options['viewType']=='onair') {
			$this->_conf .= "
				play: {opacity: 0},
				plugins: { 
     			
					controls: {
						play:false,
						scrubber:false,
						timeColor: '#99ff00',
						backgroundColor: '#333333',
						buttonColor: '#666666'
					},
    		
					rtmp: {   
           	
						// use latest RTMP plugin release 
						url: 'flowplayer.rtmp-3.2.3.swf',             
             
						netConnectionUrl: 'rtmp://".Configuration::getValue('streamingAddress')."'   
					}  
				},";

			if(Configuration::getValue('onAirSwf') && preg_match("#\.(jpg|png)$#", Configuration::getValue('onAirSwf'))) {
				date_default_timezone_set("Europe/Rome");
				$client_time = timeToSeconds(date("H:i:s"));
				$date = date("Y-m-d");
				$s = new Schedule($date);
				$item = $s->getOnAirItem($client_time);
				if(!$item) $this->_conf .= "canvas: { backgroundImage:'url(".Configuration::getValue('onAirSwf').")'},";
			}
    		
			$this->_conf .= "			
	       			clip: {
				
					onFinish: function() {
						if(player.getClip().url!='$address/".REL_UP_SWF."/".Configuration::getValue('onAirSwf')."') {
							document.getElementById('scheduleList').getChildren('li')[0].dispose();
						}
					}
								
				}";
		}
		/*
		 * Live
		 */
		elseif($this->_options['viewType']=='live') { 
			$this->_conf .= "
				plugins: { 
     			
					controls: {
						    play:false,
						    scrubber:false,
						    timeColor: '#99ff00',
						    backgroundColor: '#333333',
						    buttonColor: '#666666'
					},
		    
			    		rtmp: {   
           
						// use latest RTMP plugin release 
						url: 'flowplayer.rtmp-3.2.3.swf',             
        					netConnectionUrl: '".Configuration::getValue('live_stream_url')."'   
			    		}
				},
				clip: {
					live: true,
					url:'".Configuration::getValue('stream_name')."', 
					ipadUrl: '".Configuration::getValue('live_stream_mobile_url')."',
					provider:'rtmp',
					autoPlay: true,
					scaling:'scale'
				},
				canvas: {
					backgroundImage:'url(".Configuration::getValue('splashLive').")'
				}";
		}
		/*
		 * All
		 */
		$this->_conf .= "}";
	}

}

?>
