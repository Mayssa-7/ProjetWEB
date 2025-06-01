<?php
session_start();
require_once("db_connect.php");

// Vérifier si client connecté
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'client') {
    header("Location: compte.php");
    exit();
}

$email = $_SESSION['email'] ?? null;
if (!$email) {
    echo "Erreur : Email non défini.";
    exit();
}

// Récupérer l'id client
$stmt = $conn->prepare("SELECT id FROM clients WHERE email = ?");
$stmt->execute([$email]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$client) {
    echo "Client introuvable.";
    exit();
}
$id_client = $client['id'];

// Traitement annulation de rendez-vous
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['annuler_id'])) {
    $id_rdv = $_POST['annuler_id'];
    // Suppression simple du rendez-vous (ou tu peux faire une mise à jour état)
    $del = $conn->prepare("DELETE FROM rdv_medecin WHERE id = ? AND id_client = ?");
    $del->execute([$id_rdv, $id_client]);
    $message = "Rendez-vous annulé avec succès.";
}

// Récupérer les rendez-vous du client
$sql = "SELECT r.id AS rdv_id, c.jour_semaine, c.horaire, m.nom AS medecin_nom, m.specialite
        FROM rdv_medecin r
        JOIN creneaux_medecin c ON r.id_creneau = c.id
        JOIN medecins m ON c.id_medecin = m.id
        WHERE r.id_client = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id_client]);
$rdvs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Mes Rendez-vous - Medicare</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f7f9fc;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #3498db;
            color: white;
        }
        .btn {
            padding: 5px 10px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .message {
            margin-top: 20px;
            padding: 10px;
            background-color: #2ecc71;
            color: white;
            border-radius: 5px;
            width: fit-content;
        }
        
<div style="border:1px solid black; padding:10px; text-align:center; font-family: Arial, sans-serif; margin-bottom:20px;">
  <span style="color:red; font-weight:bold;">Medicare:</span>
  <span style="color:blue;"> Services Médicaux</span>
  <br>
  <a href="accueil.php" style="padding:5px 15px; margin:0 5px; background:#a5afcb; border-radius:8px; text-decoration:none; color:black;">Accueil</a>
  <a href="toutparcourir.php" style="padding:5px 15px; margin:0 5px; background:#a5afcb; border-radius:8px; text-decoration:none; color:black;">Tout Parcourir</a>
  <a href="recherche.php" style="padding:5px 15px; margin:0 5px; background:#a5afcb; border-radius:8px; text-decoration:none; color:black;">Recherche</a>
  <a href="rendezvous.php" style="padding:5px 15px; margin:0 5px; background:#a5afcb; border-radius:8px; text-decoration:none; color:black;">Rendez-vous</a>
  <a href="compte.php" style="padding:5px 15px; margin:0 5px; background:#a5afcb; border-radius:8px; text-decoration:none; color:black;">Votre Compte</a>
</div>

    </style>
</head>
<body>

<div style="border:1px solid black; padding:10px; text-align:center; font-family: Arial, sans-serif; margin-bottom:20px;">
  <span style="color:red; font-weight:bold;">Medicare:</span>
  <span style="color:blue;"> Services Médicaux</span>
  <br>
  <a href="accueil.php" style="padding:5px 15px; margin:0 5px; background:#a5afcb; border-radius:8px; text-decoration:none; color:black;">Accueil</a>
  <a href="toutparcourir.php" style="padding:5px 15px; margin:0 5px; background:#a5afcb; border-radius:8px; text-decoration:none; color:black;">Tout Parcourir</a>
  <a href="recherche.php" style="padding:5px 15px; margin:0 5px; background:#a5afcb; border-radius:8px; text-decoration:none; color:black;">Recherche</a>
  <a href="rendezvous.php" style="padding:5px 15px; margin:0 5px; background:#a5afcb; border-radius:8px; text-decoration:none; color:black;">Rendez-vous</a>
  <a href="compte.php" style="padding:5px 15px; margin:0 5px; background:#a5afcb; border-radius:8px; text-decoration:none; color:black;">Votre Compte</a>
</div>


<h1>Mes Rendez-vous</h1>

<?php if (isset($message)) { echo "<div class='message'>$message</div>"; } ?>

<?php if (count($rdvs) == 0): ?>
<p>Vous n'avez aucun rendez-vous pour le moment.</p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>Médecin</th>
            <th>Spécialité</th>
            <th>Jour</th>
            <th>Créneau</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($rdvs as $rdv): ?>
        <tr>
            <td><?= htmlspecialchars($rdv['medecin_nom']) ?></td>
            <td><?= htmlspecialchars($rdv['specialite']) ?></td>
            <td><?= htmlspecialchars($rdv['jour_semaine']) ?></td>
            <td><?= htmlspecialchars($rdv['horaire']) ?></td>
            <td>
                <form method="POST" onsubmit="return confirm('Voulez-vous vraiment annuler ce rendez-vous ?');">
                    <input type="hidden" name="annuler_id" value="<?= $rdv['rdv_id'] ?>" />
                    <button type="submit" class="btn">Annuler</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

</body>
</html>
