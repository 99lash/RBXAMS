import fs from 'fs';
import path from 'path';
import { exec } from 'child_process';

const rootDir = path.dirname(import.meta.dirname);
const viewsDir = path.join(rootDir, 'src', 'Views');

let lastModified = {};
let rebuildTimeout = null;
let isRebuilding = false;
let pendingChanges = [];

function debounceRebuild() {
  // Clear existing timeout
  if (rebuildTimeout) {
    clearTimeout(rebuildTimeout);
  }

  // Set new timeout
  rebuildTimeout = setTimeout(() => {
    if (!isRebuilding && pendingChanges.length > 0) {
      rebuild();
    }
  }, 500); // Wait 500ms after last change
}

function rebuild() {
  if (isRebuilding) return;

  isRebuilding = true;
  const changedFiles = [...new Set(pendingChanges)]; // Remove duplicates

  console.log(`🔄 Rebuilding CSS... (${changedFiles.length} files changed)`);
  console.log('⏱️  Started at:', new Date().toLocaleTimeString());

  const startTime = Date.now();

  exec('npx @tailwindcss/cli -i public_html/css/app.css -o public_html/css/styles.css',
    { cwd: rootDir },
    (error, stdout, stderr) => {
      const endTime = Date.now();
      const duration = ((endTime - startTime) / 1000).toFixed(2);

      if (error) {
        console.error(`❌ Error: ${error}`);
      } else {
        console.log(`✅ CSS rebuilt! - Finished in ${duration}s`);
      }

      // Reset state
      isRebuilding = false;
      pendingChanges = [];

      // Check if there are new changes while we were rebuilding
      setTimeout(checkFiles, 100);
    });
}

function checkFiles() {
  if (isRebuilding) return; // Skip check if currently rebuilding

  try {
    const files = getAllPHPFiles(viewsDir);
    let hasChanges = false;

    files.forEach(file => {
      const stats = fs.statSync(file);
      const currentModified = stats.mtime.getTime();

      if (lastModified[file] && lastModified[file] !== currentModified) {
        console.log(`📝 File changed: ${path.relative(rootDir, file)}`);
        pendingChanges.push(file);
        hasChanges = true;
      }

      lastModified[file] = currentModified;
    });

    if (hasChanges) {
      debounceRebuild();
    }
  } catch (err) {
    console.error('Polling error:', err);
  }
}

function getAllPHPFiles(dir) {
  let files = [];
  const items = fs.readdirSync(dir);

  items.forEach(item => {
    const fullPath = path.join(dir, item);
    const stat = fs.statSync(fullPath);

    if (stat.isDirectory()) {
      files = files.concat(getAllPHPFiles(fullPath));
    } else if (item.endsWith('.php')) {
      files.push(fullPath);
    }
  });

  return files;
}

// Initial scan
console.log('🔍 Starting PHP file watcher...');
checkFiles();

// Poll every 800ms (reduced frequency)
setInterval(checkFiles, 800);

console.log('🚀 Watching for PHP file changes with debouncing...');