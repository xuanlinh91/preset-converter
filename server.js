import express from 'express';
import path from 'path';
import multer from 'multer';
import fs from 'fs';
import { fileURLToPath } from 'url';
import { dirname } from 'path';
import { getXmpArray, xmpArrClean, xmpToLr, getLrArray, lrArrClean, lrToXmp } from './workers/converter.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const app = express();
const port = 3000;

// Configure multer for file upload
const upload = multer({ dest: 'tmp/' });

// Serve static files
app.use('/assets', express.static(path.join(__dirname, 'assets')));
app.use('/img', express.static(path.join(__dirname, 'img')));
app.use('/fonts', express.static(path.join(__dirname, 'fonts')));

// Serve index.html for the root route
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'index.html'));
});

// Mock endpoint for counter data
app.get('/counter', (req, res) => {
    // Mock data for testing
    res.json([100, 500, 2000, 10000]); // [today, week, month, total]
});

// Handle file upload and conversion
app.post('/converter', upload.single('preset'), (req, res) => {
    try {
        console.log('\n=== Starting Conversion Process ===');
        console.log('Received file details:', {
            originalname: req.file?.originalname,
            mimetype: req.file?.mimetype,
            size: req.file?.size,
            path: req.file?.path
        });

        if (!req.file) {
            console.error('No file uploaded');
            throw new Error('No file uploaded');
        }

        // Read the uploaded file
        const fileContent = fs.readFileSync(req.file.path, 'utf8');
        console.log('\n=== File Content Analysis ===');
        console.log('File content length:', fileContent.length);
        console.log('First 200 chars of content:', fileContent.substring(0, 200));
        console.log('Content type check:', {
            containsXMP: fileContent.includes('x:xmpmeta'),
            containsLRTemplate: fileContent.includes('s = {')
        });

        // Determine the file type and extension
        const originalName = req.file.originalname;
        const fileExtension = path.extname(originalName).toLowerCase();
        const presetType = fileExtension.replace('.', '');
        const presetName = originalName.replace(/\.[^/.]+$/, '');

        console.log('\n=== File Type Detection ===');
        console.log('Original filename:', originalName);
        console.log('File extension:', fileExtension);
        console.log('Preset type:', presetType);
        console.log('Preset name:', presetName);

        let convertedContent;
        let outputExtension;
        
        if (presetType === 'xmp') {
            console.log('Detected XMP file - converting to LRTemplate');
            outputExtension = '.lrtemplate';
            const presetArr = getXmpArray(fileContent);
            console.log('Parsed XMP array:', JSON.stringify(presetArr, null, 2));
            const cleanPresetArr = xmpArrClean(presetArr);
            console.log('Cleaned XMP array:', JSON.stringify(cleanPresetArr, null, 2));
            convertedContent = xmpToLr(cleanPresetArr, presetName);
        } else if (presetType === 'lrtemplate') {
            console.log('Detected LRTemplate file - converting to XMP');
            outputExtension = '.xmp';
            const presetArr = getLrArray(fileContent);
            console.log('Parsed LRTemplate array:', JSON.stringify(presetArr, null, 2));
            const cleanPresetArr = lrArrClean(presetArr);
            console.log('Cleaned LRTemplate array:', JSON.stringify(cleanPresetArr, null, 2));
            convertedContent = lrToXmp(cleanPresetArr);
        } else {
            console.error('Invalid preset type:', presetType);
            throw new Error(`Invalid preset type: ${presetType}. Expected 'xmp' or 'lrtemplate'`);
        }

        // Create output filename
        const outputFilename = presetName + outputExtension;
        // console.log('\n=== Output File Details ===');
        // console.log('Output filename:', outputFilename);
        // console.log('Output extension:', outputExtension);
        // console.log('Converted content length:', convertedContent.length);
        // console.log('Converted content:', convertedContent);

        // Send the converted file
        console.log('\n=== Processing Response ===');
        console.log('Sending converted file...');

        // Write the converted content to a temporary file
        const tempOutputPath = path.join(__dirname, 'tmp', outputFilename);
        console.log('Temporary output path:', tempOutputPath);
        console.log('Preparing to send file:', {
            tempOutputPath,
            outputFilename
        });
        fs.writeFileSync(tempOutputPath, convertedContent);
        res.setHeader('Content-Type', 'application/zip');
        res.setHeader('Content-Disposition', `attachment; filename="${outputFilename}"`);
        res.download(tempOutputPath, outputFilename, (err) => {
            if (err) {
                console.error('Error sending file:', err);
            } else {
                console.log('File sent successfully');
            }
            // Clean up both temporary files
            try {
                fs.unlinkSync(req.file.path);
                fs.unlinkSync(tempOutputPath);
                console.log('Temporary files cleaned up');
            } catch (cleanupErr) {
                console.error('Error cleaning up temporary files:', cleanupErr);
            }
        });
    } catch (error) {
        console.error('\n=== Conversion Error ===');
        console.error('Error details:', error);
        console.error('Stack trace:', error.stack);
        res.status(500).json({ error: error.message });
    }
});

app.listen(port, () => {
    console.log(`Server running at http://localhost:${port}`);
}); 