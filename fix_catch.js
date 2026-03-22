const fs = require('fs');
const glob = require('glob'); // Not installed, I'll just use manual recursive FS search
const path = require('path');

function walk(dir) {
    let results = [];
    const list = fs.readdirSync(dir);
    list.forEach(function(file) {
        file = dir + '/' + file;
        const stat = fs.statSync(file);
        if (stat && stat.isDirectory() && !file.includes('node_modules') && !file.includes('vendor')) { 
            results = results.concat(walk(file));
        } else if (file.endsWith('.js') || file.endsWith('.vue')) {
            results.push(file);
        }
    });
    return results;
}

const files = walk('./resources/js');

files.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    let original = content;
    
    // Fix empty catch blocks
    content = content.replace(/catch\s*\(([^)]+)\)\s*\{\s*\}/g, "catch ($1) {\n  console.warn($1);\n}");
    
    // Fix pure empty catch blocks catch {} 
    content = content.replace(/catch\s*\{\s*\}/g, "catch (e) {\n  console.warn(e);\n}");
    
    // Fix other random empty blocks if safe to guess. For now, empty catch is the primary issue.
    // Also fix cases like "const { foo } = bar;" if foo is unused? We can't safely regex that without Babel.
    
    if (original !== content) {
        fs.writeFileSync(file, content, 'utf8');
        console.log('Fixed empty catch blocks in:', file);
    }
});
