<?php 
	class Utilities{

		public function getColors($color) {
			list($R, $G, $B, $simpleRgb) = $this->__getRbg($color);
			$hex = $this->fromRGB($R, $G, $B);
			$opposite = $this->colorInverse($hex);
			return array('opposite_color' => $opposite, 'hex_color' => $hex, 'rgb_color' => $simpleRgb);
		}

		public function fromRGB($R, $G, $B){
			$R =dechex($R);
			if(strlen($R) < 2)
			$R = '0'.$R;

			$G =dechex($G);
			if(strlen($G) < 2)
			$G = '0'.$G;

			$B =dechex($B);
			if(strlen($B) < 2)
			$B = '0'.$B;
			return '#' . $R . $G . $B;
		}

		private function __getRbg($color = null) {
			$simpleRgb = str_replace(array('background-color: ',';'), '', $color);
			$rgb = str_replace(array('rgb', ' ', 'background-color: ',';', '(', ')'), '', $color);
			$rgbList = explode(",", $rgb);
			$rgbList[] = $simpleRgb; 
			return $rgbList;
		}

		public function colorInverse($hexColor = null){
		    $color = str_replace('#', '', $hexColor);
		    if (strlen($color) != 6){ return '000000'; }
		    $rgb = '';
		    for ($x = 0; $x < 3; $x++){
		        $c = 255 - hexdec(substr($color,(2*$x),2));
		        $c = ($c < 0) ? 0 : dechex($c);
		        $rgb .= (strlen($c) < 2) ? '0'.$c : $c;
		    }
		    return '#'.$rgb;
		}

	}

 ?>