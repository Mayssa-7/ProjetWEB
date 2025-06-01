
<?php
require_once("db_connect.php");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medecin_id = $_POST['medecin_id'] ?? '';
    $nom_medecin = $_POST['nom_medecin'] ?? '';
    $specialite = $_POST['specialite'] ?? '';
    $jour = $_POST['jour'] ?? '';
    $periode = $_POST['periode'] ?? '';
    $email_patient = $_POST['email'] ?? '';

    if (!$medecin_id || !$nom_medecin || !$specialite || !$jour || !$periode || !$email_patient) {
        echo json_encode(["success" => false, "message" => "Champs manquants"]);
        exit;
    }

    try {
        $stmt = $conn->prepare("INSERT INTO rendezvous (medecin_id, nom_medecin, specialite, jour, periode, email_patient) 
                                VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$medecin_id, $nom_medecin, $specialite, $jour, $periode, $email_patient]);
        echo json_encode(["success" => true, "message" => "Rendez-vous enregistré."]);
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Erreur SQL : " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Méthode non autorisée"]);
}
?>
