<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: compte.php");
    exit();
}

include('db_connect.php');

$message = "";
$cv_data = ['nom' => '', 'specialite' => '', 'bureau' => '', 'diplomes' => '', 'experiences' => '', 'competences' => '', 'langues' => ''];
$selected_medecin = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action']) && $_POST['action'] === "creer_medecin") {
        try {
            $stmt = $conn->prepare("INSERT INTO medecins (nom, specialite, email, mot_de_passe, cv_xml_path) VALUES (?, ?, ?, ?, '')");
            $stmt->execute([$_POST['nouveau_nom'], $_POST['nouvelle_specialite'], $_POST['nouvel_email'], $_POST['nouveau_mdp']]);
            $message = "Médecin créé avec succès.";
            header("Location: gestion_cv.php");
            exit();
        } catch (PDOException $e) {
            $message = "Erreur lors de la création : " . $e->getMessage();
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === "supprimer_medecin") {
        $stmt = $conn->prepare("DELETE FROM medecins WHERE id = ?");
        $stmt->execute([$_POST['medecin_id']]);
        $message = "Médecin supprimé.";
        header("Location: gestion_cv.php");
        exit();
    }

    if (isset($_POST['medecin_id']) && !isset($_POST['save_cv'])) {
        $stmt = $conn->prepare("SELECT * FROM medecins WHERE id = ?");
        $stmt->execute([$_POST['medecin_id']]);
        $selected_medecin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($selected_medecin && file_exists($selected_medecin['cv_xml_path'])) {
            $xml = simplexml_load_file($selected_medecin['cv_xml_path']);
            foreach ($cv_data as $key => $_) {
                $cv_data[$key] = (string)$xml->{$key};
            }
        }
    }

    if (isset($_POST['save_cv'])) {
        $cv_data = [
            'diplomes' => $_POST['cv_diplomes'],
            'experiences' => $_POST['cv_experiences'],
            'competences' => $_POST['cv_competences'],
            'langues' => $_POST['cv_langues']
        ];

        $cv_path = $_POST['cv_path'];
        $xml = new SimpleXMLElement('<cv/>');
        foreach ($cv_data as $key => $value) {
            $xml->addChild($key, htmlspecialchars($value));
        }
        $xml->asXML($cv_path);
        $message = "CV mis à jour avec succès.";
    }
}

$medecins = $conn->query("SELECT * FROM medecins")->fetchAll(PDO::FETCH_ASSOC);
$creneaux_labo = $conn->query("SELECT * FROM creneaux_labo ORDER BY id_service, jour_semaine, horaire")->fetchAll(PDO::FETCH_ASSOC);

// Gestion des créneaux labo
if (isset($_POST['action_creneau_labo'])) {
    if ($_POST['action_creneau_labo'] === "liberer") {
        $stmt = $conn->prepare("UPDATE creneaux_labo SET disponible = 1 WHERE id = ?");
        $stmt->execute([$_POST['creneau_id']]);
        $message = "Créneau labo libéré.";
    } elseif ($_POST['action_creneau_labo'] === "reserver") {
        $stmt = $conn->prepare("UPDATE creneaux_labo SET disponible = 0 WHERE id = ?");
        $stmt->execute([$_POST['creneau_id']]);
        $message = "Créneau labo réservé.";
    }
}

// Gestion des créneaux médecin
if (isset($_POST['action_creneau_medecin'])) {
    if ($_POST['action_creneau_medecin'] === "liberer") {
        $stmt = $conn->prepare("UPDATE creneaux_medecin SET disponible = 1 WHERE id = ?");
        $stmt->execute([$_POST['creneau_id']]);
        $message = "Créneau médecin libéré.";
    } elseif ($_POST['action_creneau_medecin'] === "reserver") {
        $stmt = $conn->prepare("UPDATE creneaux_medecin SET disponible = 0 WHERE id = ?");
        $stmt->execute([$_POST['creneau_id']]);
        $message = "Créneau médecin réservé.";
    }
}

$creneaux_med = $conn->query("
    SELECT cm.*, m.nom AS nom_medecin, m.specialite
    FROM creneaux_medecin cm
    JOIN medecins m ON cm.id_medecin = m.id
    ORDER BY m.nom, cm.jour_semaine, cm.horaire
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion des CV Médecins</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px 30px;
            background: #f9fbfc;
            color: #2e3e4e;
        }
        h2, h3 {
            color: #2a4d8f;
            margin-bottom: 10px;
        }
        form {
            margin-bottom: 25px;
            background: #fff;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
        }
        input[type="text"], input[type="email"], input[type="password"], select {
            padding: 8px;
            margin: 5px 10px 10px 0;
            border: 1px solid #bbb;
            border-radius: 5px;
            width: 220px;
            font-size: 14px;
        }
        input[type="submit"] {
            background-color: #1e73be;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #155d8b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-bottom: 30px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
        }
        th, td {
            border: 1px solid #d1d9e6;
            padding: 10px;
            text-align: center;
            font-size: 14px;
        }
        th {
            background-color: #1e73be;
            color: white;
            font-weight: 600;
        }
        td form input[type="submit"] {
            width: 100%;
            padding: 6px 10px;
            font-weight: 600;
            border-radius: 5px;
            border: none;
            background-color: #28a745;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        td form input[type="submit"]:hover {
            background-color: #1e7e34;
        }
        label {
            font-weight: 600;
            display: inline-block;
            margin-right: 5px;
            margin-top: 5px;
        }
        .message {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            max-width: 600px;
        }
    </style>
</head>
<body>

<h2>Gestion des CV des Médecins</h2>
<?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<h3>Créer un nouveau médecin</h3>
<form method="POST">
    <input type="hidden" name="action" value="creer_medecin">
    <label>Nom :</label>
    <input type="text" name="nouveau_nom" required>
    <label>Spécialité :</label>
    <input type="text" name="nouvelle_specialite" required>
    <label>Email :</label>
    <input type="email" name="nouvel_email" required>
    <label>Mot de passe :</label>
    <input type="text" name="nouveau_mdp" required>
    <input type="submit" value="Créer le médecin">
</form>

<h3>Supprimer un médecin</h3>
<form method="POST" onsubmit="return confirm('Confirmer la suppression du médecin sélectionné ?');">
    <input type="hidden" name="action" value="supprimer_medecin">
    <select name="medecin_id" required>
        <?php foreach ($medecins as $med): ?>
            <option value="<?= $med['id'] ?>"><?= htmlspecialchars($med['nom']) ?> (<?= htmlspecialchars($med['specialite']) ?>)</option>
        <?php endforeach; ?>
    </select>
    <input type="submit" value="Supprimer le médecin">
</form>

<h3>Éditer le CV d’un médecin</h3>
<form method="POST">
    <label for="medecin_id">Choisir un médecin :</label>
    <select name="medecin_id" onchange="this.form.submit()" required>
        <option value="">-- Sélectionner --</option>
        <?php foreach ($medecins as $med): ?>
            <option value="<?= $med['id'] ?>" <?= ($selected_medecin && $med['id'] == $selected_medecin['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($med['nom']) ?> (<?= htmlspecialchars($med['specialite']) ?>)
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php if ($selected_medecin): ?>
    <form method="POST">
        <input type="hidden" name="cv_path" value="<?= htmlspecialchars($selected_medecin['cv_xml_path']) ?>">
        <input type="hidden" name="medecin_id" value="<?= $selected_medecin['id'] ?>">
        <input type="hidden" name="save_cv" value="1">
        <label>Diplômes :</label>
        <input type="text" name="cv_diplomes" value="<?= htmlspecialchars($cv_data['diplomes']) ?>">
        <label>Expériences :</label>
        <input type="text" name="cv_experiences" value="<?= htmlspecialchars($cv_data['experiences']) ?>">
        <label>Compétences :</label>
        <input type="text" name="cv_competences" value="<?= htmlspecialchars($cv_data['competences']) ?>">
        <label>Langues :</label>
        <input type="text" name="cv_langues" value="<?= htmlspecialchars($cv_data['langues']) ?>">
        <input type="submit" value="Enregistrer le CV">
    </form>
<?php endif; ?>

<h3>Gestion des créneaux des médecins</h3>
<table>
<tr><th>Médecin</th><th>Spécialité</th><th>Jour</th><th>Horaire</th><th>Statut</th><th>Action</th></tr>
<?php foreach ($creneaux_med as $c): ?>
<tr>
    <td><?= htmlspecialchars($c['nom_medecin']) ?></td>
    <td><?= htmlspecialchars($c['specialite']) ?></td>
    <td><?= htmlspecialchars($c['jour_semaine']) ?></td>
    <td><?= htmlspecialchars($c['horaire']) ?></td>
    <td><?= $c['disponible'] ? 'Libre' : 'Pris' ?></td>
    <td>
        <form method="POST" style="margin:0;">
            <input type="hidden" name="creneau_id" value="<?= $c['id'] ?>">
            <?php if ($c['disponible']): ?>
                <input type="hidden" name="action_creneau_medecin" value="reserver">
                <input type="submit" value="Réserver">
            <?php else: ?>
                <input type="hidden" name="action_creneau_medecin" value="liberer">
                <input type="submit" value="Libérer">
            <?php endif; ?>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>
<h3>Gestion des créneaux du laboratoire</h3>
<table>
<tr><th>Examen</th><th>Jour</th><th>Horaire</th><th>Statut</th><th>Action</th></tr>
<?php foreach ($creneaux_labo as $c): ?>
<tr>
    <td><?= htmlspecialchars($c['nom_examen']) ?></td>  <!-- Ici on affiche le nom complet -->
    <td><?= htmlspecialchars($c['jour_semaine']) ?></td>
    <td><?= htmlspecialchars($c['horaire']) ?></td>
    <td><?= $c['disponible'] ? 'Libre' : 'Pris' ?></td>
    <td>
        <form method="POST" style="margin:0;">
            <input type="hidden" name="creneau_id" value="<?= $c['id'] ?>">
            <?php if ($c['disponible']): ?>
                <input type="hidden" name="action_creneau_labo" value="reserver">
                <input type="submit" value="Réserver" style="background-color:#28a745; color:white; border:none; padding:6px 12px; border-radius:6px; cursor:pointer;">
            <?php else: ?>
                <input type="hidden" name="action_creneau_labo" value="liberer">
                <input type="submit" value="Libérer" style="background-color:#28a745; color:white; border:none; padding:6px 12px; border-radius:6px; cursor:pointer;">
            <?php endif; ?>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>


</body>
</html>
