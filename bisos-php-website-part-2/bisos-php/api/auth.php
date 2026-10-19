<?php
// api/auth.php

function handleLogin($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';

    if (empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and password required']);
        return;
    }

    // For the demo account specifically
    if ($email === 'demo@bisos.co.za' && $password === 'Demo@123') {
        echo json_encode([
            'status' => 'success',
            'token' => 'demo-token-12345', // In production, use JWT
            'user' => [
                'name' => 'John Doe',
                'email' => 'demo@bisos.co.za',
                'role' => 'Manager'
            ]
        ]);
        return;
    }

    // Real DB check (for users registered via app)
    $stmt = $pdo->prepare("SELECT user_id, first_name, last_name, password_hash FROM User WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        echo json_encode([
            'status' => 'success',
            'token' => base64_encode($user['user_id'] . ':' . time()), // Simple token
            'user' => [
                'id' => $user['user_id'],
                'name' => $user['first_name'] . ' ' . $user['last_name'],
                'email' => $email
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
    }
}