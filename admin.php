
<?php
require_once("db_connect.php");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Liste des rendez-vous</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { padding: 20px; font-family: Arial, sans-serif; }
        h2 { margin-bottom: 20px; }
        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px 12px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f6f6f6;
        }
    </style>
</head>
<body>
    <h2>📋 Liste des rendez-vous enregistrés</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Médecin</th>
            <th>Spécialité</th>
            <th>Jour</th>
            <th>Période</th>
            <th>Email du patient</th>
            <th>Date de réservation</th>
        </tr>
        <?php
        try {
            $stmt = $conn->query("SELECT * FROM rendezvous ORDER BY date_reservation DESC");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>{$row['nom_medecin']}</td>";
                echo "<td>{$row['specialite']}</td>";
                echo "<td>{$row['jour']}</td>";
                echo "<td>{$row['periode']}</td>";
                echo "<td>{$row['email_patient']}</td>";
                echo "<td>{$row['date_reservation']}</td>";
                echo "</tr>";
            }
        } catch (PDOException $e) {
            echo "<tr><td colspan='7'>Erreur : " . $e->getMessage() . "</td></tr>";
        }
        ?>
    </table>
</body>
</html>
