# Bulk add

```sql
-- Products
UPDATE `products`
SET `name`             = CONCAT('name_', RAND()),
    `description`      = '<p>Lorem<strong> ipsum dolor</strong> sit <em>amet</em>, <u>consectetur </u>adipiscing elit. Fusce nec dignissim ligula. Morbi tristique nisi et velit facilisis, sed laoreet ante gravida. Quisque ac justo lacinia, consequat lectus dignissim, venenatis sapien. Proin tincidunt consequat risus, in fringilla nibh mollis in. Praesent dolor enim, egestas blandit tellus at, aliquet rhoncus dolor. Ut lacinia posuere nibh. Duis accumsan, dui in elementum volutpat, nunc ante tincidunt augue, vel hendrerit velit sapien at ante. Aliquam faucibus, tellus non fringilla consectetur, diam libero tempor quam, eget eleifend felis leo non sapien. Phasellus fermentum justo ac nulla gravida tempor. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere <strong>cubilia curae</strong>; Integer sagittis a elit sed dignissim. Aliquam erat volutpat. Donec lacinia, quam sed mattis venenatis, diam est euismod nibh, vel pretium diam sapien in sapien.</p>',
    `quantity`         = FLOOR(RAND() * (200 - 100 + 1)) + 100,
    `price`            = FLOOR(RAND() * (350 - 250 + 1)) + 250,
    `purchasePrice`    = FLOOR(RAND() * (150 - 150 + 1)) + 150,
    `sku`              = SUBSTRING(CONCAT('sku_', RAND()), 1, 20),
    `ean`              = SUBSTRING(CONCAT('ean_', RAND()), 1, 14),
    `mpn`       = CONCAT('pn_', RAND()),
    `weight`           = FLOOR(RAND() * (1000 - 100 + 1)) + 100,
    `weight`           = FLOOR(RAND() * (50 - 10 + 1)) + 10,
    `width`      = FLOOR(RAND() * (50 - 10 + 1)) + 10,
    `height`      = FLOOR(RAND() * (50 - 10 + 1)) + 10,
    `length`      = FLOOR(RAND() * (50 - 10 + 1)) + 10,
    `warrantyPeriod`   = FLOOR(RAND() * (60 - 12 + 1)) + 12,
    `onStock` = ROUND((RAND() * (1 - 0)) + 0),
    `isActive`         = 1;

-- Users
INSERT INTO `users` (`email`, `password`, `fullName`)
VALUES (CONCAT('test-', RAND(), '@test.com'), '****', CONCAT('name_', RAND()));
```

# Related products

```sql
INSERT IGNORE INTO productRelated (productId, relatedId) SELECT id, (SELECT id FROM products ORDER BY RAND() LIMIT 1) FROM products;
INSERT IGNORE INTO productRelated (productId, relatedId) SELECT id, (SELECT id FROM products ORDER BY RAND() LIMIT 1) FROM products;
INSERT IGNORE INTO productRelated (productId, relatedId) SELECT id, (SELECT id FROM products ORDER BY RAND() LIMIT 1) FROM products;
INSERT IGNORE INTO productRelated (productId, relatedId) SELECT id, (SELECT id FROM products ORDER BY RAND() LIMIT 1) FROM products;
INSERT IGNORE INTO productRelated (productId, relatedId) SELECT id, (SELECT id FROM products ORDER BY RAND() LIMIT 1) FROM products;
```

# Set specifics

```sql
-- Assign manufacturerId
UPDATE `products`
SET `manufacturerId` = (SELECT `id` FROM `manufacturers` ORDER BY RAND() LIMIT 1);

-- Categories
DELETE
FROM `categoriesProducts`;

INSERT INTO `categoriesProducts` (`categoryId`, `productId`)
  (SELECT (SELECT `id` FROM `categories` WHERE `parentId` IS NOT NULL ORDER BY RAND() LIMIT 1), `id`
FROM `products`);

INSERT INTO `categoriesProducts` (`categoryId`, `productId`)
  (SELECT `parentCat`.`id`,
          `cp`.`productId`
   FROM `categoriesProducts` AS `cp`
          JOIN `categories` AS `c` ON (`c`.`id` = `cp`.`categoryId`)
          JOIN `categories` AS `parentCat` ON (`c`.`parentId` = `parentCat`.`id`));

-- Product specification
DELETE
FROM `it-erp`.`productSpecifications`;
INSERT INTO `productSpecifications` (`productId`, `categoryId`, `specificationId`, `specificationValue`)
  (SELECT `cp`.`productId`,
          `c`.`id`,
          `cs`.`specificationId`,
          '1'
   FROM `categoriesProducts` AS `cp`
          JOIN `categories` AS `c` ON (`c`.`id` = `cp`.`categoryId`)
          JOIN `categoriesSpecifications` AS `cs` ON (`cs`.`categoryId` = `c`.`id`)
   WHERE `c`.`parentId` IS NOT NULL);

-- Customers
INSERT INTO `customers` (`email`, `password`, `firstName`, `lastName`, `isActive`)
VALUES (CONCAT('customer-', RAND(), '@test.com'), '****', CONCAT('First_', RAND()), CONCAT('Last_', RAND()), true);
```

# Addresses

```sql
INSERT INTO `customersAddresses` (`customerId`, `firstName`, `lastName`, `companyName`, `address`, `zipCode`, `countryId`, `city`, `notes`)
SELECT `id`, CONCAT('FirstName-', RAND()), CONCAT('LastName-', RAND()), CONCAT('CompanyName-', RAND()), CONCAT('Address-', RAND()), CONCAT('Zip-', SUBSTR(RAND() FROM 1 FOR 5)), (SELECT `id` FROM `countries` ORDER BY RAND() LIMIT 1), CONCAT('city-', RAND()), CONCAT('notes-', RAND())  FROM `customers`;
```