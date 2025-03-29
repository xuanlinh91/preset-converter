export default {
  async fetch(request, env, ctx) {
    if (request.method !== 'POST') {
      return new Response('Method not allowed', { status: 405 });
    }

    const formData = await request.formData();
    const file = formData.get('preset');
    
    if (!file) {
      return new Response('No file uploaded', { status: 400 });
    }

    const fileName = file.name;
    const presetType = fileName.split('.').pop().toLowerCase();
    const presetName = fileName.replace(`.${presetType}`, '');
    const fileContent = await file.text();

    let content;
    let outputFileName;

    if (presetType === 'xmp') {
      outputFileName = `${presetName}.lrtemplate`;
      const presetArr = getXmpArray(fileContent);
      const cleanPresetArr = xmpArrClean(presetArr);
      content = xmpToLr(cleanPresetArr, presetName);
    } else if (presetType === 'lrtemplate') {
      outputFileName = `${presetName}.xmp`;
      const presetArr = getLrArray(fileContent);
      const cleanPresetArr = lrArrClean(presetArr);
      content = lrToXmp(cleanPresetArr);
    } else {
      return new Response('Invalid file type', { status: 400 });
    }

    return new Response(content, {
      headers: {
        'Content-Type': 'application/octet-stream',
        'Content-Disposition': `attachment; filename="${outputFileName}"`,
      },
    });
  }
};

// Helper functions
const settingMapCmToLr = [
  'ToneCurve',
  'ToneCurveRed',
  'ToneCurveGreen',
  'ToneCurveBlue',
  'ToneCurveName',
];

const settingMapLrToCm = [
  'orientation',
  'EnableLensCorrections',
  'CropConstrainToWarp',
  'ChromaticAberrationB',
  'ChromaticAberrationR',
  'EnableEffects',
  'PostCropVignetteHighlightContrast',
];

function getXmpArray(xmpRaw) {
  const xmpArr = {};
  const settings = {
    // WhiteBalance
    WhiteBalance: 'crs:WhiteBalance="([^"]*)"',
    Temperature: 'crs:Temperature="([^"]*)"',
    Tint: 'crs:Tint="([^"]*)"',
    // Exposure
    Exposure2012: 'crs:Exposure2012="([^"]*)"',
    Contrast2012: 'crs:Contrast2012="([^"]*)"',
    // ... add all other settings from PHP version
  };

  for (const [key, regex] of Object.entries(settings)) {
    let match = xmpRaw.match(new RegExp(regex, 'is'));
    let value = match ? match[1] : '';

    // Handle RDF lists
    const rdfMatch = value.match(/<rdf:li[^>]*>([^>]*)<\/rdf:li>/gis);
    if (rdfMatch) {
      value = rdfMatch.map(item => {
        const m = item.match(/<rdf:li[^>]*>([^>]*)<\/rdf:li>/i);
        return m ? m[1] : '';
      });
    }

    if (value && value !== '') {
      if (!Array.isArray(value)) {
        value = value.replace(/\+/g, '');
      }
      xmpArr[key] = value;
    }
  }

  return xmpArr;
}

function xmpArrClean(xmp) {
  for (const key of settingMapCmToLr) {
    delete xmp[key];
  }
  return xmp;
}

function lrArrClean(lrArr) {
  for (const key of settingMapLrToCm) {
    delete lrArr[key];
  }
  return lrArr;
}

function xmpToLr(xmp, presetName) {
  const settings = Object.entries(xmp)
      .sort(([a], [b]) => a.localeCompare(b))
      .map(([key, value]) => {
        if (key === "ProcessVersion") {
          return `\t\t\t${key} = "${value.toLowerCase()}",`;
        } else if (Array.isArray(value)) {
          const points = value.map(v => {
            const [x, y] = v.split(', ');
            return `\n\t\t\t\t${x},\n\t\t\t\t${y},`;
          }).join('');
          return `\t\t\t${key} = {${points}\n\t\t\t},`;
        } else {
          // Check if value is numeric or boolean
          if (value === "True" || value === "False") {
            return `\t\t\t${key} = ${value.toLowerCase()},`;
          } else if (!isNaN(parseFloat(value)) && value !== "") {
            // Preserve original decimal places by checking if the original value had them
            const hasDecimal = value.includes('.');
            const numValue = parseFloat(value);
            if (hasDecimal) {
              // Get the number of decimal places from the original string
              const decimalPlaces = value.split('.')[1]?.length || 0;
              return `\t\t\t${key} = ${numValue.toFixed(decimalPlaces)},`;
            }
            return `\t\t\t${key} = ${numValue},`;
          } else {
            return `\t\t\t${key} = "${value}",`;
          }
        }
      })
      .join('\n');

  return `s = {
    id = "1",
    internalName = "${presetName} Preset",
    title = "${presetName} Preset",
    type = "Develop",
    value = {
        settings = {
${settings}
        },
        uuid = "1",
    },
    version = 0,
}`;
}

function lrToXmp(lrSettings) {
  const xmpStart =
    '<x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="Adobe XMP Core 5.6-c011 79.156380, 2014/05/21-23:38:37        ">\n' +
    ' <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">\n' +
    '  <rdf:Description rdf:about=""\n' +
    '    xmlns:crs="http://ns.adobe.com/camera-raw-settings/1.0/"';

  const xmpEnd = '</rdf:Description>\n </rdf:RDF>\n</x:xmpmeta>';

  let result = xmpStart + '\n';
  
  // Add regular settings
  for (const [key, value] of Object.entries(lrSettings)) {
    if (!Array.isArray(value)) {
      result += `\tcrs:${key}="${String(value).replace(/"/g, '')}"` + '\n';
    }
  }

  result += '\t' + 'crs:HasSettings="True">' + '\n';

  // Handle tone curves if they exist
  if (lrSettings['ToneCurvePV2012'] && lrSettings['ToneCurvePV2012'].length > 0) {
    // Add ToneCurve
    result += '\t<crs:ToneCurve>\n\t <rdf:Seq>\n';
    for (let i = 0; i < lrSettings['ToneCurvePV2012'].length; i += 2) {
      result += `\t  <rdf:li>${lrSettings['ToneCurvePV2012'][i]}, ${lrSettings['ToneCurvePV2012'][i + 1] || 0}</rdf:li>\n`;
    }
    result += '\t </rdf:Seq>\n\t</crs:ToneCurve>\n';

    // Add ToneCurveRed
    result += '\t<crs:ToneCurveRed>\n\t <rdf:Seq>\n';
    for (let i = 0; i < lrSettings['ToneCurvePV2012Red'].length; i += 2) {
      result += `\t  <rdf:li>${lrSettings['ToneCurvePV2012Red'][i]}, ${lrSettings['ToneCurvePV2012Red'][i + 1] || 0}</rdf:li>\n`;
    }
    result += '\t </rdf:Seq>\n\t</crs:ToneCurveRed>\n';

    // Add ToneCurveGreen
    result += '\t<crs:ToneCurveGreen>\n\t <rdf:Seq>\n';
    for (let i = 0; i < lrSettings['ToneCurvePV2012Green'].length; i += 2) {
      result += `\t  <rdf:li>${lrSettings['ToneCurvePV2012Green'][i]}, ${lrSettings['ToneCurvePV2012Green'][i + 1] || 0}</rdf:li>\n`;
    }
    result += '\t </rdf:Seq>\n\t</crs:ToneCurveGreen>\n';

    // Add ToneCurveBlue
    result += '\t<crs:ToneCurveBlue>\n\t <rdf:Seq>\n';
    for (let i = 0; i < lrSettings['ToneCurvePV2012Blue'].length; i += 2) {
      result += `\t  <rdf:li>${lrSettings['ToneCurvePV2012Blue'][i]}, ${lrSettings['ToneCurvePV2012Blue'][i + 1] || 0}</rdf:li>\n`;
    }
    result += '\t </rdf:Seq>\n\t</crs:ToneCurveBlue>\n';

    // Add PV2012 versions
    result += '\t<crs:ToneCurvePV2012>\n\t <rdf:Seq>\n';
    for (let i = 0; i < lrSettings['ToneCurvePV2012'].length; i += 2) {
      result += `\t  <rdf:li>${lrSettings['ToneCurvePV2012'][i]}, ${lrSettings['ToneCurvePV2012'][i + 1] || 0}</rdf:li>\n`;
    }
    result += '\t </rdf:Seq>\n\t</crs:ToneCurvePV2012>\n';

    result += '\t<crs:ToneCurvePV2012Red>\n\t <rdf:Seq>\n';
    for (let i = 0; i < lrSettings['ToneCurvePV2012Red'].length; i += 2) {
      result += `\t  <rdf:li>${lrSettings['ToneCurvePV2012Red'][i]}, ${lrSettings['ToneCurvePV2012Red'][i + 1] || 0}</rdf:li>\n`;
    }
    result += '\t </rdf:Seq>\n\t</crs:ToneCurvePV2012Red>\n';

    result += '\t<crs:ToneCurvePV2012Green>\n\t <rdf:Seq>\n';
    for (let i = 0; i < lrSettings['ToneCurvePV2012Green'].length; i += 2) {
      result += `\t  <rdf:li>${lrSettings['ToneCurvePV2012Green'][i]}, ${lrSettings['ToneCurvePV2012Green'][i + 1] || 0}</rdf:li>\n`;
    }
    result += '\t </rdf:Seq>\n\t</crs:ToneCurvePV2012Green>\n';

    result += '\t<crs:ToneCurvePV2012Blue>\n\t <rdf:Seq>\n';
    for (let i = 0; i < lrSettings['ToneCurvePV2012Blue'].length; i += 2) {
      result += `\t  <rdf:li>${lrSettings['ToneCurvePV2012Blue'][i]}, ${lrSettings['ToneCurvePV2012Blue'][i + 1] || 0}</rdf:li>\n`;
    }
    result += '\t </rdf:Seq>\n\t</crs:ToneCurvePV2012Blue>\n';
  }

  result += xmpEnd;
  return result;
}

function getLrArray(lrContent) {
  const settingsMatch = lrContent.match(/settings = {([\n\s]*)([^>]*)},([\n\s]*)uuid/);
  const settings = settingsMatch ? settingsMatch[2] : '';
  
  const toneCurveTypes = [
    'ToneCurvePV2012',
    'ToneCurvePV2012Blue',
    'ToneCurvePV2012Green',
    'ToneCurvePV2012Red'
  ];

  const toneCurve = {};
  for (const type of toneCurveTypes) {
    const match = settings.match(new RegExp(`${type} = {([0-9\\s\\n\\,]*)},`));
    if (match) {
      toneCurve[type] = match[1].trim().split(',').map(v => v.trim()).filter(v => v);
    }
  }

  const lrSettings = {};
  const settingsArray = settings.split(',');
  
  for (const setting of settingsArray) {
    const [key, value] = setting.trim().split(' = ');
    if (key && value) {
      lrSettings[key.trim()] = value.trim();
    }
  }

  return { ...lrSettings, ...toneCurve };
}