<!DOCTYPE html>
<html>

<head>
    <title>Ingredient Low Stock Notification</title>
</head>

<body>

    <h1>Low Stock Notification</h1>

    <p>Dear Merchant,</p>

    <p>The stock of the ingredient {{ $ingredient->name }} has fallen below 50%.</p>

    <p>Current Stock Level: {{ $ingredient->available_stock }} kg</p>
    <p>Original Stock Level: {{ $ingredient->stock }} kg</p>

    <p>Please take necessary action to replenish your stock.</p>

    <p>Thank you for your attention.</p>

    <p>Sincerely,</p>
    <p>Your Store Team</p>

</body>

</html>
