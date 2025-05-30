<?php
// accueil.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Accueil - Medicare</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
      background-color: #f7f9fc;
    }
    h1 {
      margin-top: 0;
    }
    footer {
      background-color: #f8f9fa;
      padding: 20px 0;
      margin-top: 50px;
      font-size: 0.9em;
      text-align: center;
      color: #555;
    }
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


  <main class="container">
    <div style="text-align:center; margin-bottom: 50px;">
      <h1>Bienvenue chez Medicare</h1>
      <p>Votre plateforme de services médicaux en ligne, accessible et fiable.</p>
    </div>

    <!-- Évènement de la semaine -->
    <section style="margin-bottom: 50px;">
      <h2>Évènement de la semaine</h2>
      <p>Nous organisons une porte ouverte Medicare ce samedi 3 juin de 9h à 17h. Venez rencontrer nos spécialistes, découvrir nos services et profiter de conseils personnalisés.</p>
    </section>

    <!-- Carrousel des spécialistes -->
    <section style="margin-bottom: 50px;">
      <h2>Nos spécialistes</h2>
      <div style="display:flex; justify-content:center; gap:30px;">
        <div style="text-align:center;">
          <img src="images/specialiste1.jpg" alt="Dr. Alice Martin" style="width:150px; height:150px; border-radius: 50%;" />
          <div>Dr. Alice Martin</div>
          <div>Cardiologie</div>
        </div>
        <div style="text-align:center;">
          <img src="images/specialiste2.jpg" alt="Dr. Bruno Leroy" style="width:150px; height:150px; border-radius: 50%;" />
          <div>Dr. Bruno Leroy</div>
          <div>Dermatologie</div>
        </div>
        <div style="text-align:center;">
          <img src="images/specialiste3.jpg" alt="Dr. Clara Dupont" style="width:150px; height:150px; border-radius: 50%;" />
          <div>Dr. Clara Dupont</div>
          <div>Neurologie</div>
        </div>
      </div>
    </section>

  </main>

  <footer>
    <p><strong>Contact Medicare</strong></p>
    <p>Email : <a href="mailto:contact@medicare.fr">contact@medicare.fr</a> | Téléphone : 01 23 45 67 89</p>
    <p>Adresse : 123 Rue de la Santé, 75000 Paris, France</p>
    <div style="height:300px;">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.9997983015933!2d2.2922926156742453!3d48.85837007928748!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66fdd3b574521%3A0xc870db91d5be8f41!2sTour%20Eiffel!5e0!3m2!1sfr!2sfr!4v1617773185119!5m2!1sfr!2sfr"
        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
  </footer>
</body>
</html>
