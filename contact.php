<?php
/********************************************************************
 * contact.php – ScrollStory (version optimisée)
 * ---------------------------------------------------------------
 * Reçoit les données POST du formulaire de contact :
 *   - name
 *   - email
 *   - destination (facultatif)
 *   - message
 *
 * Vérifie et nettoie les champs, puis envoie un e-mail
 * en renvoyant un JSON clair : { success: bool, message: string }
 ********************************************************************/

header('Content-Type: application/json; charset=utf-8');

// --- 1️⃣ Vérification de la méthode HTTP ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée. Utilisez POST.'
    ]);
    exit;
}

// --- 2️⃣ Récupération & nettoyage sécurisé ---
function clean_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

$name        = clean_input($_POST['name']        ?? '');
$email       = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$destination = clean_input($_POST['destination'] ?? '');
$message     = clean_input($_POST['message']     ?? '');

// --- 3️⃣ Validation des champs ---
if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Veuillez saisir un nom, un e-mail valide et un message.'
    ]);
    exit;
}

// --- 4️⃣ Configuration de l’e-mail ---
$to = 'mbowtidiane013@gmail.com'; // 🔧 Adresse de réception
$subject = '📩 Message ScrollStory – ' . ($destination ?: 'Sans destination précisée');

$body = <<<EOT
Vous avez reçu un nouveau message via ScrollStory :

👤 Nom : $name
✉️ E-mail : $email
🎯 Destination : $destination

💬 Message :
$message
EOT;

// Entêtes e-mail sécurisées
$headers = [
    'From' => 'no-reply@scrollstory.com',
    'Reply-To' => $email,
    'Content-Type' => 'text/plain; charset=UTF-8',
    'X-Mailer' => 'PHP/' . phpversion()
];

// --- 5️⃣ Envoi du mail ---
$headers_string = '';
foreach ($headers as $key => $value) {
    $headers_string .= "$key: $value\r\n";
}

if (mail($to, $subject, $body, $headers_string)) {
    echo json_encode([
        'success' => true,
        'message' => '✅ Message envoyé avec succès. Merci de nous avoir contactés !'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '❌ Une erreur est survenue lors de l’envoi. Réessayez plus tard.'
    ]);
}
