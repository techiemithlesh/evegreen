<?php
namespace App\Traits;

trait Formula{
    public function calculatePossibleProduction($request){

        $variables = [
            "RL"=>$request->length,
            "RW"=>$request->netWeight,
            "RS"=>$request->size,
            "GSM"=>$request->gsm,
            "X"=>"*",
            "*"=>"*",
            "x"=>"*",
            "/"=>"/",
            "+"=>"+",
            "-"=>"-",
            "L"=>$request->bagL,
            "W"=>$request->bagW,
            "G"=>$request->bagG??0,
        ];  

        $formula = $request->formula;
        foreach ($variables as $key => $value) {                                               
            $formula = str_replace($key, $value, $formula);                        
        }
        $result = round(eval(" return ".$formula." ;"));
        return collect([
            "result" => $result,
            "unit" => $result ." ".$request->bookingBagUnits,
            "variables"=>$variables,
            "formula"=>$request->formula,
        ]);
    }

    public function gsmVariation($roll){
        $oldGsm = $roll->gsm_variation;
        $roll->gsm_variation = (((($roll->net_weight * 39.37 * 1000) / $roll->size)/$roll->length)-$roll->gsm)/$roll->gsm;
    }

    public function getCylinderSize($request){

        $variables = [
            "RL"=>$request->length,
            "RW"=>$request->netWeight,
            "RS"=>$request->size,
            "GSM"=>$request->gsm,
            "X"=>"*",
            "*"=>"*",
            "x"=>"*",
            "/"=>"/",
            "+"=>"+",
            "-"=>"-",
            "L"=>$request->l,
            "W"=>$request->w,
            "G"=>$request->g??0,
        ];  

        $formula = $request->formula;
        foreach ($variables as $key => $value) {                                               
            $formula = str_replace($key, $value, $formula);                        
        }
        $result = round(eval(" return ".$formula." ;"));
        return collect([
            "result" => $result,
            "unit" => $result ." ".$request->bookingBagUnits,
            "variables"=>$variables,
            "formula"=>$request->formula,
        ]);
    }
}