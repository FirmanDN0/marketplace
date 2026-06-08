<?php
$pdo = new PDO('sqlite:C:/XI SIJA 2/pm 2/paymentgateway/backend/database/database.sqlite');
$stmt = $pdo->query("SELECT webhook_url FROM merchants LIMIT 1");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
