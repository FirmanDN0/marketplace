<?php
$pdo = new PDO('sqlite:C:/XI SIJA 2/pm 2/paymentgateway/backend/database/database.sqlite');
$stmt = $pdo->query('SELECT transaction_id, reference_id, status, amount, total_amount FROM transactions ORDER BY created_at DESC LIMIT 5');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
