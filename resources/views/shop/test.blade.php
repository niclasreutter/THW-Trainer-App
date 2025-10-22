<!DOCTYPE html>
<html>
<head>
    <title>Shop Test</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f0f0f0; }
        .box { background: white; padding: 20px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Shop Test - Pure HTML</h1>
        <p>User: {{ $user->name }}</p>
        <p>Points: {{ $user->points }}</p>
        <p>Name Colors: {{ $nameColors->count() }}</p>
        <p>Effects: {{ $effects->count() }}</p>
        <p>Emojis: {{ $emojis->count() }}</p>
    </div>
</body>
</html>
