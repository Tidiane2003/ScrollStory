<?php
/********************************************************************
 * contact.php – ScrollStory
 * ---------------------------------------------------------------
 * Reçoit les données POST du formulaire de contact :
 *  name, email, destination (facultatif), message
 *  → Valide les champs
 *  → Envoie un e‑mail à l’adresse configurée
 *  → Renvoie { success: bool, message: string } en JSON
 ********************************************************************/

header('Content-Type: application/json; charset=utf-8');

// 1) Méthode HTTP autorisée ?
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée. Utilisez POST.'
    ]);
    exit;
}

// 2) Récupération & nettoyage des données
$name        = trim(htmlspecialchars($_POST['name']        ?? ''));
$email       = trim(filter_var($_POST['email']             ?? '', FILTER_SANITIZE_EMAIL));
$destination = trim(htmlspecialchars($_POST['destination'] ?? ''));
$message     = trim(htmlspecialchars($_POST['message']     ?? ''));

// 3) Validation minimale
if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Veuillez saisir un nom, un e‑mail valide et un message.'
    ]);
    exit;
}

// 4) Préparation de l’e‑mail
$to      = 'mbowtidiane013@gmail.com';   // 🔧 Ton adresse de réception
$subject = 'Message ScrollStory – ' . ($destination ?: 'Sans destination précisée');
$body    = "Nom         : $name\n"
         . "E‑mail      : $email\n"
         . "Destination : $destination\n\n"
         . "Message :\n$message\n";
$headers = "From: $email\r\n"
         . "Reply-To: $email\r\n"
         . "X-Mailer: PHP/" . phpversion();

// 5) Envoi
if (mail($to, $subject, $body, $headers)) {
    echo json_encode([
        'success' => true,
        'message' => 'Message envoyé avec succès !'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de l’envoi du message. Réessayez plus tard.'
    ]);
}
