<?php
/**
 * Created by PhpStorm.
 * User: linhnx
 * Date: 9/18/2017
 * Time: 11:13 PM
 */
$presetName = "";
$settingMapCmToLr = array(
    'ToneCurve',
    'ToneCurveRed',
    'ToneCurveGreen',
    'ToneCurveBlue',
    'ToneCurveName',
);
$settingMapLrToCm = array(
    'orientation',
    'EnableLensCorrections',
    'CropConstrainToWarp',
    'ChromaticAberrationB',
    'ChromaticAberrationR',
    'EnableEffects',
    'PostCropVignetteHighlightContrast',
);

function getXmpArray(&$xmp_raw)
{
    $xmp_arr = array();
    foreach (array(
                 //WhiteBalance
                 'WhiteBalance' => 'crs:WhiteBalance="([^"]*)"',
                 'Temperature' => 'crs:Temperature="([^"]*)"',
                 'Tint' => 'crs:Tint="([^"]*)"',
                 //Exposure
                 'Exposure2012' => 'crs:Exposure2012="([^"]*)"',
                 'Contrast2012' => 'crs:Contrast2012="([^"]*)"',
                 'Highlights2012' => 'crs:Highlights2012="([^"]*)"',
                 'Shadows2012' => 'crs:Shadows2012="([^"]*)"',
                 'Whites2012' => 'crs:Whites2012="([^"]*)"',
                 'Blacks2012' => 'crs:Blacks2012="([^"]*)"',
                 'Clarity2012' => 'crs:Clarity2012="([^"]*)"',
                 'Vibrance' => 'crs:Vibrance="([^"]*)"',
                 'Saturation' => 'crs:Saturation="([^"]*)"',
                 //ToneCurve
                 'ParametricShadows' => 'crs:ParametricShadows="([^"]*)"',
                 'ParametricDarks' => 'crs:ParametricDarks="([^"]*)"',
                 'ParametricLights' => 'crs:ParametricLights="([^"]*)"',
                 'ParametricHighlights' => 'crs:ParametricHighlights="([^"]*)"',
                 'ParametricShadowSplit' => 'crs:ParametricShadowSplit="([^"]*)"',
                 'ParametricMidtoneSplit' => 'crs:ParametricMidtoneSplit="([^"]*)"',
                 'ParametricHighlightSplit' => 'crs:ParametricHighlightSplit="([^"]*)"',
                 'ToneCurveName' => 'crs:ToneCurveName="([^"]*)"',
                 'ToneCurveName2012' => 'crs:ToneCurveName2012="([^"]*)"',
                 'ToneCurve' => '<crs:ToneCurve>\s*(.*?)\s*<\/crs:ToneCurve>',
                 'ToneCurveRed' => '<crs:ToneCurveRed>\s*(.*?)\s*<\/crs:ToneCurveRed>',
                 'ToneCurveGreen' => '<crs:ToneCurveGreen>\s*(.*?)\s*<\/crs:ToneCurveGreen>',
                 'ToneCurveBlue' => '<crs:ToneCurveBlue>\s*(.*?)\s*<\/crs:ToneCurveBlue>',
                 'ToneCurvePV2012' => '<crs:ToneCurvePV2012>\s*(.*?)\s*<\/crs:ToneCurvePV2012>',
                 'ToneCurvePV2012Red' => '<crs:ToneCurvePV2012Red>\s*(.*?)\s*<\/crs:ToneCurvePV2012Red>',
                 'ToneCurvePV2012Green' => '<crs:ToneCurvePV2012Green>\s*(.*?)\s*<\/crs:ToneCurvePV2012Green>',
                 'ToneCurvePV2012Blue' => '<crs:ToneCurvePV2012Blue>\s*(.*?)\s*<\/crs:ToneCurvePV2012Blue>',
                 //Detail
                 'Sharpness' => 'crs:Sharpness="([^"]*)"',
                 'SharpenRadius' => 'crs:SharpenRadius="([^"]*)"',
                 'SharpenDetail' => 'crs:SharpenDetail="([^"]*)"',
                 'SharpenEdgeMasking' => 'crs:SharpenEdgeMasking="([^"]*)"',
                 'LuminanceSmoothing' => 'crs:LuminanceSmoothing="([^"]*)"',
                 'LuminanceNoiseReductionDetail' => 'crs:LuminanceNoiseReductionDetail="([^"]*)"',
                 'LuminanceNoiseReductionContrast' => 'crs:LuminanceNoiseReductionContrast="([^"]*)"',
                 'ColorNoiseReduction' => 'crs:ColorNoiseReduction="([^"]*)"',
                 'ColorNoiseReductionDetail' => 'crs:ColorNoiseReductionDetail="([^"]*)"',
                 'ColorNoiseReductionSmoothness' => 'crs:ColorNoiseReductionSmoothness="([^"]*)"',
                 //HSL-Grayscale
                 'HueAdjustmentRed' => 'crs:HueAdjustmentRed="([^"]*)"',
                 'HueAdjustmentOrange' => 'crs:HueAdjustmentOrange="([^"]*)"',
                 'HueAdjustmentYellow' => 'crs:HueAdjustmentYellow="([^"]*)"',
                 'HueAdjustmentGreen' => 'crs:HueAdjustmentGreen="([^"]*)"',
                 'HueAdjustmentAqua' => 'crs:HueAdjustmentAqua="([^"]*)"',
                 'HueAdjustmentBlue' => 'crs:HueAdjustmentBlue="([^"]*)"',
                 'HueAdjustmentPurple' => 'crs:HueAdjustmentPurple="([^"]*)"',
                 'HueAdjustmentMagenta' => 'crs:HueAdjustmentMagenta="([^"]*)"',
                 'SaturationAdjustmentRed' => 'crs:SaturationAdjustmentRed="([^"]*)"',
                 'SaturationAdjustmentOrange' => 'crs:SaturationAdjustmentOrange="([^"]*)"',
                 'SaturationAdjustmentYellow' => 'crs:SaturationAdjustmentYellow="([^"]*)"',
                 'SaturationAdjustmentGreen' => 'crs:SaturationAdjustmentGreen="([^"]*)"',
                 'SaturationAdjustmentAqua' => 'crs:SaturationAdjustmentAqua="([^"]*)"',
                 'SaturationAdjustmentBlue' => 'crs:SaturationAdjustmentBlue="([^"]*)"',
                 'SaturationAdjustmentPurple' => 'crs:SaturationAdjustmentPurple="([^"]*)"',
                 'SaturationAdjustmentMagenta' => 'crs:SaturationAdjustmentMagenta="([^"]*)"',
                 'LuminanceAdjustmentRed' => 'crs:LuminanceAdjustmentRed="([^"]*)"',
                 'LuminanceAdjustmentOrange' => 'crs:LuminanceAdjustmentOrange="([^"]*)"',
                 'LuminanceAdjustmentYellow' => 'crs:LuminanceAdjustmentYellow="([^"]*)"',
                 'LuminanceAdjustmentGreen' => 'crs:LuminanceAdjustmentGreen="([^"]*)"',
                 'LuminanceAdjustmentAqua' => 'crs:LuminanceAdjustmentAqua="([^"]*)"',
                 'LuminanceAdjustmentBlue' => 'crs:LuminanceAdjustmentBlue="([^"]*)"',
                 'LuminanceAdjustmentPurple' => 'crs:LuminanceAdjustmentPurple="([^"]*)"',
                 'LuminanceAdjustmentMagenta' => 'crs:LuminanceAdjustmentMagenta="([^"]*)"',
                 'GrayMixerRed' => 'crs:GrayMixerRed="([^"]*)"',
                 'GrayMixerOrange' => 'crs:GrayMixerOrange="([^"]*)"',
                 'GrayMixerYellow' => 'crs:GrayMixerYellow="([^"]*)"',
                 'GrayMixerGreen' => 'crs:GrayMixerGreen="([^"]*)"',
                 'GrayMixerAqua' => 'crs:GrayMixerAqua="([^"]*)"',
                 'GrayMixerBlue' => 'crs:GrayMixerBlue="([^"]*)"',
                 'GrayMixerPurple' => 'crs:GrayMixerPurple="([^"]*)"',
                 'GrayMixerMagenta' => 'crs:GrayMixerMagenta="([^"]*)"',
                 //SplitToning
                 'ConvertToGrayscale' => 'crs:ConvertToGrayscale="([^"]*)"',
                 'SplitToningHighlightHue' => 'crs:SplitToningHighlightHue="([^"]*)"',
                 'SplitToningHighlightSaturation' => 'crs:SplitToningHighlightSaturation="([^"]*)"',
                 'SplitToningBalance' => 'crs:SplitToningBalance="([^"]*)"',
                 'SplitToningShadowHue' => 'crs:SplitToningShadowHue="([^"]*)"',
                 'SplitToningShadowSaturation' => 'crs:SplitToningShadowSaturation="([^"]*)"',
                 //Lens Correction -> Profile
                 'LensProfileEnable' => 'crs:LensProfileEnable="([^"]*)"',
                 'LensProfileSetup' => 'crs:LensProfileSetup="([^"]*)"',
                 'LensProfileFilename' => 'crs:LensProfileFilename="([^"]*)"',
                 'LensProfileName' => 'crs:LensProfileName="([^"]*)"',
                 'LensProfileDigest' => 'crs:LensProfileDigest="([^"]*)"',
                 'LensProfileDistortionScale' => 'crs:LensProfileDistortionScale="([^"]*)"',
                 'LensProfileVignettingScale' => 'crs:LensProfileVignettingScale="([^"]*)"',
                 'LensProfileChromaticAberrationScale' => 'crs:LensProfileChromaticAberrationScale="([^"]*)"',
                 //Lens Correction -> Color
                 'AutoLateralCA' => 'crs:AutoLateralCA="([^"]*)"',
                 'DefringePurpleAmount' => 'crs:DefringePurpleAmount="([^"]*)"',
                 'DefringePurpleHueLo' => 'crs:DefringePurpleHueLo="([^"]*)"',
                 'DefringePurpleHueHi' => 'crs:DefringePurpleHueHi="([^"]*)"',
                 'DefringeGreenAmount' => 'crs:DefringeGreenAmount="([^"]*)"',
                 'DefringeGreenHueLo' => 'crs:DefringeGreenHueLo="([^"]*)"',
                 'DefringeGreenHueHi' => 'crs:DefringeGreenHueHi="([^"]*)"',
                 //Lens Correction -> Manual
                 'LensManualDistortionAmount' => 'crs:LensManualDistortionAmount="([^"]*)"',
                 'PerspectiveHorizontal' => 'crs:PerspectiveHorizontal="([^"]*)"',
                 'PerspectiveVertical' => 'crs:PerspectiveVertical="([^"]*)"',
                 'PerspectiveRotate' => 'crs:PerspectiveRotate="([^"]*)"',
                 'PerspectiveScale' => 'crs:PerspectiveScale="([^"]*)"',
                 'VignetteAmount' => 'crs:VignetteAmount="([^"]*)"',
                 'VignetteMidpoint' => 'crs:VignetteMidpoint="([^"]*)"',
                 'PerspectiveAspect' => 'crs:PerspectiveAspect="([^"]*)"',
                 'PerspectiveUpright' => 'crs:PerspectiveUpright="([^"]*)"',
                 'PerspectiveX' => 'crs:PerspectiveX="([^"]*)"',
                 'PerspectiveY' => 'crs:PerspectiveY="([^"]*)"',
                 //Fx -> Grain
                 'Dehaze' => 'crs:Dehaze="([^"]*)"',
                 'GrainAmount' => 'crs:GrainAmount="([^"]*)"',
                 'GrainSize' => 'crs:GrainSize="([^"]*)"',
                 'GrainFrequency' => 'crs:GrainFrequency="([^"]*)"',
                 'PostCropVignetteAmount' => 'crs:PostCropVignetteAmount="([^"]*)"',
                 'PostCropVignetteMidpoint' => 'crs:PostCropVignetteMidpoint="([^"]*)"',
                 'PostCropVignetteRoundness' => 'crs:PostCropVignetteRoundness="([^"]*)"',
                 'PostCropVignetteFeather' => 'crs:PostCropVignetteFeather="([^"]*)"',
                 'GrainSeed' => 'crs:GrainSeed="([^"]*)"', //Post Crop Style

                 //Camera Calibration
                 'ShadowTint' => 'crs:ShadowTint="([^"]*)"',
                 'RedHue' => 'crs:RedHue="([^"]*)"',
                 'RedSaturation' => 'crs:RedSaturation="([^"]*)"',
                 'GreenHue' => 'crs:GreenHue="([^"]*)"',
                 'GreenSaturation' => 'crs:GreenSaturation="([^"]*)"',
                 'BlueHue' => 'crs:BlueHue="([^"]*)"',
                 'BlueSaturation' => 'crs:BlueSaturation="([^"]*)"',
                 'CameraProfile' => 'crs:CameraProfile="([^"]*)"',
                 'CameraProfileDigest' => 'crs:CameraProfileDigest="([^"]*)"',
                 //Process Version
                 'ProcessVersion' => 'crs:ProcessVersion="([^"]*)"', //????

             ) as $key => $regex) {


        $xmp_arr[$key] = preg_match("/" . $regex . "/is", $xmp_raw, $match) ? $match[1] : '';
        $xmp_arr[$key] = preg_match_all("/<rdf:li[^>]*>([^>]*)<\/rdf:li>/is", $xmp_arr[$key], $match) ? $match[1] : $xmp_arr[$key];

        if (is_null($xmp_arr[$key]) || $xmp_arr[$key] == "") {
            unset ($xmp_arr[$key]);
        } else if (!is_array($xmp_arr[$key])) {
            $xmp_arr[$key] = str_replace('+', '', $xmp_arr[$key]);
        }
    }

    return $xmp_arr;

}

function xmpArrClean($xmp)
{
    global $settingMapCmToLr;
    foreach ($settingMapCmToLr as $key) {
        if (isset($xmp[$key])) {
            unset($xmp[$key]);
        }
    }

    return $xmp;
}

function lrArrClean($lrArr)
{
    global $settingMapLrToCm;
    foreach ($settingMapLrToCm as $key) {
        if (isset($lrArr[$key])) {
            unset($lrArr[$key]);
        }
    }

    return $lrArr;
}

function xmpToLr($xmp)
{
ksort($xmp);
global $presetName;
$lrTemplateStart =
's = {
    id = "1",
    internalName = "' . $presetName . ' Preset",
    title = "' . $presetName . ' Preset",
    type = "Develop",
    value = {
        settings = {' . "\n";
$lrTemplateEnd =
    "\t\t" . '},
        uuid = "1",
    },
    version = 0,
}';

$result = $lrTemplateStart;
foreach ($xmp as $key => $value) {
    if ($key === "ProcessVersion") {
        $result .= "\t\t\t" . $key . ' = "' . strtolower($value) . '",' . "\n";
    } else if (is_numeric($value) || $value === "False" || $value === "True") {
        $result .= "\t\t\t" . $key . ' = ' . strtolower($value) . ',' . "\n";
    } elseif (is_array($value)) {
        $result .= "\t\t\t" . $key . ' = {';
        foreach ($value as $k => $v) {
            $index = explode(', ', $v);
            $result .= "\n\t\t\t\t" . $index[0] . ',' . "\n\t\t\t\t" . $index[1] . ',';
        }

        $result .= "\n\t\t\t" . '},' . "\n";
    } else {
        $result .= "\t\t\t" . $key . ' = "' . $value . '",' . "\n";
    }
}
$result .= $lrTemplateEnd;

return $result;
}

function lrToXmp($lrSettings)
{
$xmpStart =
'<x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="Adobe XMP Core 5.6-c011 79.156380, 2014/05/21-23:38:37        ">
 <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
  <rdf:Description rdf:about=""
    xmlns:crs="http://ns.adobe.com/camera-raw-settings/1.0/"';

$xmpEnd = '</rdf:Description>
 </rdf:RDF>
</x:xmpmeta>';

$result = $xmpStart."\n";
foreach ($lrSettings as $key => $value) {
    if (!is_array($value)) {
        $result .= "\tcrs:" . $key . '="' . str_replace('"', '', $value) . '"' . "\n";
    }
}

    $result .= "\t" . 'crs:HasSettings="True">' . "\n";
    if (isset($lrSettings['ToneCurvePV2012']) && $lrSettings['ToneCurvePV2012'][0] != '') {
        $result .= "\t" . '<crs:ToneCurve>
     <rdf:Seq>' . "\n";
        foreach ($lrSettings['ToneCurvePV2012'] as $key => $value) {
            if ($key % 2 == 0) {
                $result .= "\t  <rdf:li>";
                $result .= $value . ', ' . (isset($lrSettings['ToneCurvePV2012'][$key + 1]) ? $lrSettings['ToneCurvePV2012'][$key + 1] : 0);
                $result .= "</rdf:li>\n";
            }
        }

        $result .= "\t " . '</rdf:Seq>
    </crs:ToneCurve>' . "\n";
        $result .= "\t" . '<crs:ToneCurveRed>
     <rdf:Seq>' . "\n";
        foreach ($lrSettings['ToneCurvePV2012Red'] as $key => $value) {
            if ($key % 2 == 0) {
                $result .= "\t <rdf:li>";
                $result .= $value . ', ' . (isset($lrSettings['ToneCurvePV2012Red'][$key]) ? $lrSettings['ToneCurvePV2012Red'][$key + 1] : 0);
                $result .= "</rdf:li>\n";
            }
        }

        $result .= "\t " . '</rdf:Seq>
    </crs:ToneCurveRed>' . "\n";
        $result .= "\t" . '<crs:ToneCurveGreen>
     <rdf:Seq>' . "\n";
        foreach ($lrSettings['ToneCurvePV2012Green'] as $key => $value) {
            if ($key % 2 == 0) {
                $result .= "\t  <rdf:li>";
                $result .= $value . ', ' . (isset($lrSettings['ToneCurvePV2012Green'][$key]) ? $lrSettings['ToneCurvePV2012Green'][$key + 1] : 0);
                $result .= "</rdf:li>\n";
            }
        }

        $result .= "\t " . '</rdf:Seq>
    </crs:ToneCurveGreen>' . "\n";
        $result .= "\t" . '<crs:ToneCurveBlue>
     <rdf:Seq>' . "\n";
        foreach ($lrSettings['ToneCurvePV2012Blue'] as $key => $value) {
            if ($key % 2 == 0) {
                $result .= "\t  <rdf:li>";
                $result .= $value . ', ' . (isset($lrSettings['ToneCurvePV2012Blue'][$key]) ? $lrSettings['ToneCurvePV2012Blue'][$key + 1] : 0);
                $result .= "</rdf:li>\n";
            }
        }

        $result .= "\t " . '</rdf:Seq>
    </crs:ToneCurveBlue>';

        $result .= "\n\t" . '<crs:ToneCurvePV2012>
     <rdf:Seq>' . "\n";
        foreach ($lrSettings['ToneCurvePV2012'] as $key => $value) {
            if ($key % 2 == 0) {
                $result .= "\t  <rdf:li>";
                $result .= $value . ', ' . (isset($lrSettings['ToneCurvePV2012'][$key + 1]) ? $lrSettings['ToneCurvePV2012'][$key + 1] : 0);
                $result .= "</rdf:li>\n";
            }
        }

        $result .= "\t" . '</rdf:Seq>
    </crs:ToneCurvePV2012>' . "\n";
        $result .= "\t" . '<crs:ToneCurvePV2012Red>
     <rdf:Seq>' . "\n";
        foreach ($lrSettings['ToneCurvePV2012Red'] as $key => $value) {
            if ($key % 2 == 0) {
                $result .= "\t  <rdf:li>";
                $result .= $value . ', ' . (isset($lrSettings['ToneCurvePV2012Red'][$key]) ? $lrSettings['ToneCurvePV2012Red'][$key + 1] : 0);
                $result .= "</rdf:li>\n";
            }
        }

        $result .= "\t" . '</rdf:Seq>
    </crs:ToneCurvePV2012Red>' . "\n";
        $result .= "\t" . '<crs:ToneCurvePV2012Green>
     <rdf:Seq>' . "\n";
        foreach ($lrSettings['ToneCurvePV2012Green'] as $key => $value) {
            if ($key % 2 == 0) {
                $result .= "\t  <rdf:li>";
                $result .= $value . ', ' . (isset($lrSettings['ToneCurvePV2012Green'][$key]) ? $lrSettings['ToneCurvePV2012Green'][$key + 1] : 0);
                $result .= "</rdf:li>\n";
            }
        }

        $result .= "\t" . '</rdf:Seq>
    </crs:ToneCurvePV2012Green>' . "\n";
        $result .= "\t" . '<crs:ToneCurvePV2012Blue>
     <rdf:Seq>' . "\n";
        foreach ($lrSettings['ToneCurvePV2012Blue'] as $key => $value) {
            if ($key % 2 == 0) {
                $result .= "\t  <rdf:li>";
                $result .= $value . ', ' . (isset($lrSettings['ToneCurvePV2012Blue'][$key]) ? $lrSettings['ToneCurvePV2012Blue'][$key + 1] : 0);
                $result .= "</rdf:li>\n";
            }
        }

        $result .= "\t" . '</rdf:Seq>
    </crs:ToneCurvePV2012Blue>' . "\n";
    }

$result .= $xmpEnd;

return $result;
}

function getLrArray($lrContent)
{
    $settings = preg_match("/settings = {([\n\s]*)([^>]*)},([\n\s]*)uuid/", $lrContent, $match) ? $match[2] : '';
    $toneCurve = array();
    foreach (array(
        'ToneCurvePV2012' => 'ToneCurvePV2012 = {([0-9\s\n\,]*)},',
        'ToneCurvePV2012Blue' => 'ToneCurvePV2012Blue = {([0-9\s\n\,]*)},',
        'ToneCurvePV2012Green' => 'ToneCurvePV2012Green = {([0-9\s\n\,]*)},',
        'ToneCurvePV2012Red' => 'ToneCurvePV2012Red = {([0-9\s\n\,]*)},',
             ) as $key => $regex) {
        $toneCurve[$key] = preg_match("/" . $regex . "/is", $settings, $toneCurveMatch) ? $toneCurveMatch[1] : '';
    }

    $settings = explode(',', $settings);
    $lrSettings = array();

    foreach ($settings as $key => $value) {
        $settings[$key] = trim($value);
        $splitter = explode(' = ', $value);
        if (count($splitter) > 1) {
            $newKey = trim($splitter[0]);
            $newVal = $splitter[1];
            $lrSettings[$newKey] = $newVal;
        }
    }

    if ($toneCurve) {
        foreach ($toneCurve as $key => $value) {
            $toneCurve[$key] = explode(',', trim($value));
            foreach ($toneCurve[$key] as $k => $v) {
                $toneCurve[$key][$k] = trim($v);
                if ($v == '') {
                    unset($toneCurve[$key][$k]);
                }
            }

            $lrSettings[$key] = $toneCurve[$key];
        }

    }

    return $lrSettings;

}
    if (isset($_FILES['preset'])) {
        // foreach ($_FILES['preset']['tmp_name'] as $key => $file) {    
            $file_name = $_FILES['preset']['name'];
            $presetName = explode('.', $file_name);
            $presetType = pathinfo($file_name, PATHINFO_EXTENSION);
            $presetName = str_replace("." . $presetType, "", $file_name);
            $fileContent = file_get_contents($_FILES['preset']['tmp_name']);

        if ($presetType == 'xmp') {
                $presetName .= ".lrtemplate";
                $presetArr = getXmpArray($fileContent);
                $presetArr = xmpArrClean($presetArr);
                $content =  xmpToLr($presetArr);

            } elseif ($presetType == 'lrtemplate') {
                $presetName .= ".xmp";
                $presetArr = getLrArray($fileContent);
                $presetArr = lrArrClean($presetArr);
                $content = lrToXmp($presetArr);
            }
        // }

        header_remove();
        header('Content-disposition: attachment; filename="'. $presetName .'"');
        header('Content-type: application/zip');
        echo($content);
    }
?>