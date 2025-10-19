const fs = require('fs');
const path = require('path');

// Директория за рекурсивно търсене
const startDir = 'D:/Workspace/htdocs/it/erp/laravel/storage/app/public/uploads';

// Функция за рекурсивно намиране на всички .jpg файлове
function listJpgFiles(dir, fileList = []) {
  // Чети всички файлове и директории в текущата директория
  const files = fs.readdirSync(dir);

  files.forEach(file => {
    const fullPath = path.join(dir, file);
    const stat = fs.statSync(fullPath);

    // Ако е директория, викаме рекурсивно
    if (stat.isDirectory()) {
      listJpgFiles(fullPath, fileList);
    }
    // Ако е файл и е .jpg, го добавяме към масива
    else if (path.extname(file).toLowerCase() === '.jpg') {
      fileList.push(fullPath);
    }
  });

  return fileList;
}

// Стартирай процеса
const jpgFiles = listJpgFiles(startDir);
let sql = '';
jpgFiles.forEach(filePath => {
  const fileName = path.basename(filePath);
  const parentDir = path.basename(path.dirname(filePath));
  const grandParentDir = path.basename(path.dirname(path.dirname(filePath)));

  sql += `\nINSERT INTO \`uploads\` (\`groupType\`, \`groupId\`, \`name\`, \`size\`, \`hash\`, \`originalName\`, \`extension\`, \`mimeType\`) VALUES ('${grandParentDir}', '${parentDir}', '${fileName}', 1100000, 'hash', 'upload.jpg', 'jpg', 'image/jpeg');`;
});

sql += `\nWITH ranked AS ( SELECT id, ROW_NUMBER() OVER ( PARTITION BY groupType, groupId ORDER BY id ) - 1 AS newSortOrder FROM uploads ) UPDATE uploads t
JOIN ranked rp ON t.id = rp.id
SET t.sortOrder = rp.newSortOrder;`;

fs.writeFileSync(require.main.filename + '.sql', sql);
