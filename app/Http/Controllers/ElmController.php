<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use MathPHP\LinearAlgebra\Matrix;
use MathPHP\LinearAlgebra\MatrixFactory;
use MathPHP\LinearAlgebra\Vector;

class ElmController extends Controller
{
    public function index()
    {
    	$dataInput = Excel::load('excel/data_input.xlsx')->get()->toArray();

    	// $w[0][0] = 0.88;
    	// $w[0][1] = 0.12;
    	// $w[0][2] = 0.23;
    	// $w[0][3] = 0.43;
    	// $w[0][4] = 0.25;
    	// $w[0][5] = 0.38;
    	// $w[0][6] = 0.72;

    	// $w[1][0] = 0.6;
    	// $w[1][1] = 0.15;
    	// $w[1][2] = 0.75;
    	// $w[1][3] = 0.49;
    	// $w[1][4] = 0.01;
    	// $w[1][5] = 0.52;
    	// $w[1][6] = 0.14;

    	// $w[2][0] = 0.36;
    	// $w[2][1] = 0.81;
    	// $w[2][2] = 0.32;
    	// $w[2][3] = 0.45;
    	// $w[2][4] = 0.09;
    	// $w[2][5] = 0.28;
    	// $w[2][6] = 0.05;

    	// $w[3][0] = 0.05;
    	// $w[3][1] = 0.06;
    	// $w[3][2] = 0.91;
    	// $w[3][3] = 0.08;
    	// $w[3][4] = 0.35;
    	// $w[3][5] = 0.08;
    	// $w[3][6] = 0.12;

    	// $w[4][0] = 0.32;
    	// $w[4][1] = 0.04;
    	// $w[4][2] = 0.15;
    	// $w[4][3] = 0.12;
    	// $w[4][4] = 0.24;
    	// $w[4][5] = 0.72;
    	// $w[4][6] = 0.84;

    	// $w[5][0] = 0.12;
    	// $w[5][1] = 0.45;
    	// $w[5][2] = 0.67;
    	// $w[5][3] = 0.06;
    	// $w[5][4] = 0.81;
    	// $w[5][5] = 0.88;
    	// $w[5][6] = 0.21;

    	// $w[6][0] = 0.12;
    	// $w[6][1] = 0.45;
    	// $w[6][2] = 0.67;
    	// $w[6][3] = 0.06;
    	// $w[6][4] = 0.81;
    	// $w[6][5] = 0.88;
    	// $w[6][6] = 0.21;
    	$max = 0;
		foreach ($dataInput as $val)
		{
			foreach($val as $key=>$val1)
			{
				if ($val1 > $max)
			 	{
		        	$max = $val1;
		    	}
			}   	
		}

		$min = $dataInput[0][0];
		foreach ($dataInput as $val)
		{
			foreach($val as $key=>$val1)
			{
				if ($val1 < $min)
			 	{
		        	$min = $val1;
		    	}
			}   	
		}

    	// Normalisasi
    	$normalisasi = [];
    	for ($i=0; $i < sizeof($dataInput); $i++) { 
    		for ($j=0; $j < 8; $j++) { 
    			$normalisasi[$i][$j] = (0.8*($dataInput[$i][$j]-$min)/($max-$min))+0.1;
    		}
    	}
    	
    	// Bias
    	// $b[0] = 0.24;
    	// $b[1] = 0.08;
    	// $b[2] = 0.11;
    	// $b[3] = 0.76;
    	// $b[4] = 0.41;
    	// $b[5] = 0.65;
    	// $b[6] = 0.32;
    	$mse = 1;
    	$count = 0;
    	$epsilon = 0.01;
    	for($iterasi = 0;$iterasi < 1000;$iterasi++) {
    		for ($i=0; $i < 7; $i++) { 
	    		$b[$i] = mt_rand() / mt_getrandmax();
	    	}

	    	for ($pola=0; $pola < sizeof($dataInput); $pola++) { 
		    	if ($pola == 0) {
		    		$w = [];
			    	for ($i=0; $i < 7; $i++) { 
			    		for ($j=0; $j < 7; $j++) { 
			    			$w[$i][$j] = mt_rand() / mt_getrandmax();
			    		}
			    	}
		    	}
		    	// g1(x1)
		    	for ($i=0; $i < 7; $i++) { 
		    		for ($j=0; $j < 7; $j++) { 
		    			$g[$i][$j] = ($w[$i][$j]*$normalisasi[$pola][$j])+$b[$i];
		    		}
		    		$sum[$i] = array_sum($g[$i]);
		    	}
		    	// g(x)
		    	$gx = [];
		    	for ($i=0; $i < 7; $i++) { 
		    		$gx[$i] = 1/(1+exp(-$sum[$i]));
		    	}
		    	// h
				for ($i=0; $i < 7; $i++) { 
					for ($j=0; $j < 7; $j++) { 
						$h[$i][$j] = $g[$i][$j]; 
					}
				}
				// t
				for ($i=0; $i < 7; $i++) { 
					$t[$i] = $normalisasi[$i][7];
				}
				${'t'.$pola} = new Vector($t);
				${'h'.$pola} = new Matrix($h);
				${'h'.$pola} = ${'h'.$pola}->transpose();
				${'h'.$pola} = ${'h'.$pola}->inverse();
				${'beta'.$pola} = ${'h'.$pola}->multiply(${'t'.$pola})->asVectors();
				${'beta'.$pola} = array_sum(${'beta'.$pola}[0]->getVector());
				$gx = 1/(1+(exp(-${'beta'.$pola})));
				$error[$pola] = abs($normalisasi[$pola][7]-$gx);
	    	}
	    	$sumError = 0;
	    	for ($i=0; $i < sizeof($error); $i++) { 
	    		$sumError = $sumError+pow($error[$i], 2);
	    	}

	    	for ($i=0; $i < 7; $i++) { 
	    		for ($j=0; $j < 7; $j++) { 
	    			$w[$i][$j] = $w[$i][$j]+($normalisasi[count($dataInput)-1][$j]*$error[count($dataInput)-1]);
	    		}
	    	}
	    	$mse = (1/sizeof($dataInput))*$sumError;
	    	$saveMse[$count] = $mse;
	    	$count++;
    	}
    	dd($mse,$saveMse);
    }
}