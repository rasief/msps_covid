<?php
	require_once('fpdf.php');
	
	class PDF_HTML {
		var $B = 0;
		var $I = 0;
		var $U = 0;
		var $tag = 0;
		var $HREF = '';
		var $ALIGN = '';
		var $TEXT_COLOR;
		
		function WriteHTML($html, $pdf) {
			//HTML parser
			$html = str_replace("\n", '', $html);
			$html = str_replace(chr(10), "", $html);
			$html = str_replace(chr(13), "", $html);
			$a = preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
			foreach ($a as $i => $e) {
				if ($i % 2 == 0) {
					//Text
					if ($this->HREF)
						$this->PutLink($this->HREF, $e, $pdf);
					elseif ($this->ALIGN == 'center')
						$pdf->Cell(0, 3, $e, 0, 1, 'C');
					else
						$pdf->Write(3, $e);
				} else {
					//Tag
					if ($e{0} == '/')
						$this->CloseTag(strtoupper(substr($e, 1)), $pdf);
					else {
						//Extract properties
						@$a2 = split(' ', $e);
						$tag = strtoupper(array_shift($a2));
						$prop = array();
						foreach ($a2 as $v)
							if (@ereg('^([^=]*)=["\']?([^"\']*)["\']?$', $v, $a3))
								$prop[strtoupper($a3[1])] = $a3[2];
						$this->OpenTag($tag, $prop, $pdf);
					}
				}
			}
		}
		
		function OpenTag($tag, $prop, $pdf) {
			//Opening tag
			if ($tag == "STRONG") {
				$tag = "B";
			}
			if ($tag == 'B' or $tag == 'I' or $tag == 'U') {
				$this->SetStyle($tag, true, $pdf);
			}
			if ($tag == 'A')
				$this->HREF = $prop['HREF'];
			if ($tag == 'BR')
				$pdf->Ln(3);
			if ($tag == 'P')
				@$this->ALIGN = $prop['ALIGN'];
			if ($tag == 'HR') {
				if ( $prop['WIDTH'] != '')
					$Width = $prop['WIDTH'];
				else
					$Width = $pdf->w - $pdf->lMargin - $pdf->rMargin;
				$pdf->Ln(1);
				$x = $pdf->GetX();
				$y = $pdf->GetY();
				$pdf->SetLineWidth(0.4);
				$pdf->Line($x, $y, $x+$Width, $y);
				$pdf->SetLineWidth(0.2);
				$pdf->Ln(1);
			}
			if ($tag == 'SPAN') {
				if (isset($prop['STYLE']) && strtoupper(substr($prop['STYLE'], 0, 5)) == 'COLOR') {
					@$color_aux = split(':', $prop['STYLE']);
					$color_aux = $color_aux[1];
					$r_aux = 0;
					$g_aux = 0;
					$b_aux = 0;
					@$r_aux = hexdec(substr($color_aux, 1, 2));
					@$g_aux = hexdec(substr($color_aux, 3, 2));
					@$b_aux = hexdec(substr($color_aux, 5, 2));
					
					$pdf->SetTextColor($r_aux, $g_aux, $b_aux);
				}
			}
		}
		
		function CloseTag($tag, $pdf) {
			//Closing tag
			if ($tag == "STRONG") {
				$tag = "B";
			}
			if ($tag == 'B' or $tag == 'I' or $tag == 'U') {
				$this->SetStyle($tag, false, $pdf);
			}
			if ($tag == 'A')
				$this->HREF = '';
			if ($tag == 'P') {
				$this->ALIGN = '';
				$pdf->Ln(3);
			}
			if ($tag == 'SPAN') {
				$pdf->SetTextColor(0);
			}
		}
		
		function SetStyle($tag, $enable, $pdf) {
			//Modify style and select corresponding font
			$this->$tag += ($enable ? 1 : -1);
			$style = '';
			foreach (array('B', 'I', 'U') as $s) {
				if ($this->$s > 0) {
					$style .= $s;
				}
			}
			$pdf->SetFont('', $style);
		}
		
		function PutLink($URL, $txt, $pdf) {
			//Put a hyperlink
			$pdf->SetTextColor(0, 0, 255);
			$this->SetStyle('U', true, $pdf);
			$pdf->Write(3, $txt, $URL);
			$this->SetStyle('U', false, $pdf);
			$pdf->SetTextColor(0);
		}
	}
?>
