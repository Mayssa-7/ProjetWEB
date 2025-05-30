
<?php
include('db_connect.php');

if (!isset($_GET['id'])) {
    echo "Aucun médecin sélectionné.";
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM medecins WHERE id = ?");
$stmt->execute([$id]);
$medecin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medecin) {
    echo "Médecin introuvable.";
    exit();
}

$cv_path = $medecin['cv_xml_path'];

if (!file_exists($cv_path)) {
    echo "CV non disponible.";
    exit();
}

$xml = simplexml_load_file($cv_path);
?>

<!DOCTYPE html>
<html>
<head>
    <title>CV de <?= htmlspecialchars($xml->nom) ?></title>
</head>
<body>
    <h2>CV de <?= htmlspecialchars($xml->nom) ?></h2>
    <p><strong>Spécialité :</strong> <?= htmlspecialchars($xml->specialite) ?></p>
    <p><strong>Bureau :</strong> <?= htmlspecialchars($xml->bureau) ?></p>
    <p><strong>Diplômes :</strong> <?= htmlspecialchars($xml->diplomes) ?></p>
    <p><strong>Expériences :</strong> <?= htmlspecialchars($xml->experiences) ?></p>
    <p><strong>Compétences :</strong> <?= htmlspecialchars($xml->competences) ?></p>
    <p><strong>Langues :</strong> <?= htmlspecialchars($xml->langues) ?></p>
</body>
</html>
