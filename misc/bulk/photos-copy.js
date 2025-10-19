const fs = require('fs');
const path = require('path');

const sourceDir = 'E:/Stock Photos - Small';

const targetDir = 'D:/Workspace/htdocs/it/erp/laravel/storage/app/public/uploads/products';
const TOTAL_DIRECTORIES = 2000;
const FILES_PER_DIRECTORY = 3;

// const targetDir = 'D:/Workspace/htdocs/it/erp/laravel/storage/app/public/uploads/categories';
// const TOTAL_DIRECTORIES = 61;
// const FILES_PER_DIRECTORY = 1;

// const targetDir = 'D:/Workspace/htdocs/it/erp/laravel/storage/app/public/uploads/manufacturers';
// const TOTAL_DIRECTORIES = 125;
// const FILES_PER_DIRECTORY = 1;

// Функция за връщане на произволни елементи от масив
function getRandomFiles(files, count) {
  const shuffled = [...files].sort(() => 0.5 - Math.random());
  return shuffled.slice(0, count);
}

// Функция за създаване на директория, ако не съществува
function ensureDirSync(dirPath) {
  if (!fs.existsSync(dirPath)) {
    fs.mkdirSync(dirPath, { recursive: true });
  }
}

// Основна функция
function distributeImages() {
  try {
    // Чети всички .jpg файлове от директорията със снимки
    const allImages = fs.readdirSync(sourceDir).filter(file => path.extname(file).toLowerCase() === '.jpg');

    if (allImages.length === 0) {
      console.log('Няма намерени .jpg файлове в сорс директорията.');
      return;
    }

    // За всяка директория от 1 до TOTAL_DIRECTORIES
    for (let i = 1; i <= TOTAL_DIRECTORIES; i++) {
      const currentDir = path.join(targetDir, i.toString());

      // Създай директорията, ако не съществува
      ensureDirSync(currentDir);

      // Чети файловете в текущата директория
      const filesInDir = fs.readdirSync(currentDir);
      const jpgFilesInDir = filesInDir.filter(file => path.extname(file).toLowerCase() === '.jpg');
      const filesNeeded = FILES_PER_DIRECTORY - jpgFilesInDir.length;

      // Ако директорията има по-малко от FILES_PER_DIRECTORY .jpg файла, копирай произволни
      if (filesNeeded > 0) {
        const randomImages = getRandomFiles(allImages, filesNeeded);

        // Копирай произволните файлове
        for (const image of randomImages) {
          const sourcePath = path.join(sourceDir, image);
          const destPath = path.join(currentDir, image);
          fs.copyFileSync(sourcePath, destPath);
          console.log(`Copied ${image} to ${currentDir}`);
        }
      } else {
        console.log(`Directory ${i} already has ${FILES_PER_DIRECTORY} or more .jpg files.`);
      }
    }

    console.log('Distribution complete.');
  } catch (err) {
    console.error('Error:', err);
  }
}

// Стартирай процеса
distributeImages();
