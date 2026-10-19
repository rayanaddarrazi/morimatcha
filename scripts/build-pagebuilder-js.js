/**
 * Build script to combine all pagebuilder JavaScript files
 * 
 * @package morimatcha
 * @version 1.0.0
 * @author Kay Jilesen
 */

const fs = require('fs');
const path = require('path');

const templateDir = path.join(__dirname, '..', 'templates', 'pagebuilder');

// Elk blok met een eigen {blok}.js wordt automatisch meegenomen.
const pagebuilderBlocks = fs.readdirSync(templateDir, { withFileTypes: true })
    .filter(entry => entry.isDirectory())
    .map(entry => entry.name)
    .sort();
const outputFile = path.join(__dirname, '..', 'assets', 'js', 'pagebuilder.js');

// Header comment
let output = `/**
 * Pagebuilder Blocks JavaScript
 * Auto-generated file - Do not edit manually
 * 
 * @package morimatcha
 * @version 1.0.0
 * @author Kay Jilesen
 * 
 * This file combines all pagebuilder block JavaScript files.
 * Generated: ${new Date().toISOString()}
 */

(function() {
    'use strict';

`;

// Combine all block JavaScript files
pagebuilderBlocks.forEach(block => {
    const jsFile = path.join(templateDir, block, `${block}.js`);
    
    if (fs.existsSync(jsFile)) {
        let content = fs.readFileSync(jsFile, 'utf8');
        
        // Remove header comment
        content = content.replace(/^\/\*\*[\s\S]*?\*\/\s*/, '');
        
        // Extract content from IIFE if present
        // Match: (function() { ... })();
        const iifePattern = /^\s*\(function\(\)\s*\{([\s\S]*)\}\s*\)\s*\(\)\s*;?\s*$/;
        const iifeMatch = content.trim().match(iifePattern);
        
        if (iifeMatch && iifeMatch[1]) {
            let blockContent = iifeMatch[1].trim();
            
            // Remove 'use strict' if it's at the start (we already have it in the main IIFE)
            blockContent = blockContent.replace(/^['"]use strict['"];\s*/i, '');
            
            // Properly indent the content
            blockContent = blockContent.split('\n').map(line => {
                // Don't indent empty lines
                if (line.trim() === '') {
                    return '';
                }
                // Add 4 spaces indentation
                return '    ' + line;
            }).join('\n');
            
            output += `    // ${block} block\n`;
            output += blockContent;
            output += '\n\n';
        } else {
            // If no IIFE wrapper, just use the content with proper indentation
            let blockContent = content.trim();
            
            // Properly indent the content
            blockContent = blockContent.split('\n').map(line => {
                if (line.trim() === '') {
                    return '';
                }
                return '    ' + line;
            }).join('\n');
            
            output += `    // ${block} block\n`;
            output += blockContent;
            output += '\n\n';
        }
    }
});

// Close the main IIFE
output += `})();
`;

// Write the combined file
fs.writeFileSync(outputFile, output, 'utf8');
console.log(`✓ Built pagebuilder.js (${pagebuilderBlocks.length} blocks combined)`);

