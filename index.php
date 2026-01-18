<!DOCTYPE html>
<html lang="el">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Portal | Αρχική</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; overflow-x: hidden; background-color: #f9f9f9; }

        /* HEADER */
        header {
            background-color: #800000;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
            z-index: 1001;
            position: sticky;
            top: 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }

        .menu-btn {
            background: none;
            border: 2px solid white;
            color: white;
            padding: 5px 15px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            margin-right: 15px;
            border-radius: 4px;
            transition: 0.3s;
        }
        .menu-btn:hover { background: white; color: #800000; }

        .left-header { display: flex; align-items: center; }
        .logo { font-size: 28px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; }
        .login-btn { border: 2px solid white; color: white; text-decoration: none; padding: 8px 25px; font-weight: bold; border-radius: 4px; transition: 0.3s; }
        .login-btn:hover { background: white; color: #800000; }

        /* SIDEBAR */
        .sidebar {
            background-color: #333; /* Πιο σκούρο για αντίθεση */
            color: white;
            width: 280px;
            position: fixed;
            top: 70px;
            left: -280px;
            height: 100vh;
            transition: 0.4s;
            z-index: 1000;
            padding: 30px 20px;
        }
        .sidebar.active { left: 0; }
        .sidebar h3 { border-bottom: 2px solid #800000; padding-bottom: 10px; margin-bottom: 20px; }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { padding: 12px 0; }
        .sidebar ul li a { color: #ccc; text-decoration: none; font-size: 17px; transition: 0.3s; }
        .sidebar ul li a:hover { color: white; padding-left: 10px; }

        /* MAIN CONTENT */
        main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            display: grid;
            grid-template-columns: 1fr 1fr; /* Χωρισμός σε 2 στήλες */
            gap: 40px;
        }

        .text-area h2 { color: #800000; font-size: 32px; margin-bottom: 20px; }
        .text-area p { line-height: 1.8; color: #444; font-size: 17px; margin-bottom: 15px; }

        /* MEDIA AREA (Map & Image) */
        .media-area { display: flex; flex-direction: column; gap: 20px; }
        
        .map-box { 
            border: 1px solid #ddd; 
            height: 250px; /* Πιο μικρό map */
            border-radius: 8px; 
            overflow: hidden; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .map-box iframe { width: 100%; height: 100%; border: 0; }

        .campus-img {
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            border: 5px solid white; /* Σαν κορνίζα */
        }

        .overlay {
            display: none;
            position: fixed;
            top: 70px;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        .overlay.active { display: block; }

        /* Responsive για κινητά */
        @media (max-width: 850px) {
            main { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <header>
        <div class="left-header">
            <button class="menu-btn" onclick="toggleMenu()">☰ MENU</button>
            <div class="logo">college</div>
        </div>
        <a href="login.php" class="login-btn">LOGIN</a>
    </header>

    <div class="overlay" id="overlay" onclick="toggleMenu()"></div>

    <aside class="sidebar" id="sidebar">
        <h3>Πλοήγηση</h3>
        <ul>
            <li><a href="index.php">🏠 Αρχική</a></li>
            <li><a href="#">📢 Ανακοινώσεις</a></li>
            <li><a href="#">📅 Πρόγραμμα</a></li>
            <li><a href="#">📍 Τοποθεσία</a></li>
            <li><a href="#">📞 Επικοινωνία</a></li>
        </ul>
    </aside>

    <main id="main">
        <div class="text-area">
            <h2>Η Φιλοσοφία μας</h2>
            <p>Στο <b>College Mpamphs</b>, πιστεύουμε ότι η εκπαίδευση είναι το θεμέλιο για ένα δημιουργικό μέλλον. Στόχος μας είναι να γεφυρώσουμε το χάσμα μεταξύ τεχνολογίας και μάθησης, προσφέροντας στους φοιτητές τα απαραίτητα ψηφιακά εργαλεία.</p>
            
            <p>Επιδιώκουμε τη δημιουργία ενός δυναμικού περιβάλλοντος όπου η γνώση διαμοιράζεται άμεσα και με ασφάλεια. Μέσα από την πλατφόρμα μας, κάθε φοιτητής έχει πρόσβαση σε εκπαιδευτικό υλικό, ενώ οι καθηγητές διαχειρίζονται τα μαθήματά τους με διαφάνεια.</p>
            
            <div style="margin-top: 30px; padding: 20px; background: #800000; color: white; border-radius: 8px;">
                <h4 style="margin: 0 0 10px 0;">📍 Πού θα μας βρείτε;</h4>
                <p style="margin: 0; font-size: 14px; color: #ffcccc;">Το Campus μας βρίσκεται σε μια προνομιακή τοποθεσία στη Λάρισα, προσφέροντας σύγχρονες εγκαταστάσεις και ένα ιδανικό περιβάλλον για σπουδές.</p>
            </div>
        </div>

        <div class="media-area">
            <div class="map-box">
                <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d1537.4167249779337!2d22.448401750995345!3d39.58590383577075!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sel!2sgr!4v1768752953607!5m2!1sel!2sgr"
                    width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>

            <img src="campus-larissa-3.jpg" alt="College Campus" class="campus-img">
        </div>
    </main>

    <script>
        function toggleMenu() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('overlay').classList.toggle('active');
        }
    </script>

</body>
</html>
