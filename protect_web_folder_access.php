 <?php

$phpV = phpversion();
$workingFile = pathinfo($_SERVER['PHP_SELF'], PATHINFO_BASENAME);
$workingPath = realpath($workingFile);

if (isset($_POST['username']) and isset($_POST['password'])) {

    /*-------------------- Début Html ------------------*/

    echo '    <!DOCTYPE html>

    <html lang="fr">';

    /*-------------------- Début Head ------------------*/

    echo '
    <head>

        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Crypt Folder Access | Loïc Drouet</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="css-secondary/normalize.css">
        <link rel="stylesheet" href="css-secondary/mystyle.css">
        
    </head>';
    
    /*-------------------- Fin Head ------------------*/
    
    echo '
    <body>
        <main>

        <header>
            <h1>Protection d\'un accès à un dossier web par cryptage<br />et génération des fichiers .htaccess et .htpasswd<br />(à partir de PHP 8.0.0, le sel n\'est plus optionnel, avant oui).</h1>
        </header>
 
        <section class="infos">
            <h2>Informations</h2>
                <h3>Version actuelle de PHP :</h3>
                    <p>' . $phpV . '</p>
                <h3>Chemin courant :</h3>
                    <p>' . $workingPath . '</p>';

    $processus_start_time = microtime(true);
    
    $username = $_POST['username'];

    //For generating Salt :
    //Here is a random way of generating salt
    $salt_start_time = microtime(true);
    $randomString = random_bytes(32);
    //Base 64 encode to ensure that some characters will not cause problems for crypt
    $salt = base64_encode($randomString);
    $salt_finish_time = microtime(true);
    $salt_time = round(($salt_finish_time - $salt_start_time)*1000000, 2);
    
    echo '
                <h3 style="color: Crimson;">Salt (en ' . $salt_time . ' microsecondes)</h3>
                    <p style="color: Crimson;">' . $salt . '</p>
                <h3>Les fichiers htaccess.txt et htpasswd.txt (cryptage n°6 : SHA512) ont été générés dans le répertoire où ce script est exécuté. Veuillez les renommer en .htaccess et .htpasswd pour qu\'ils prennent effet.</h3>
        </section>';    

    /*1.------------------ Standard DES ------------------*/

    //For Hashing CRYPT_STD_DES  :
    $hPsw1_start_time = microtime(true);
    
    // Comment // for time 
    $hashedPsw1 = crypt($_POST['password'], $salt);
    
    $hPsw1_finish_time = microtime(true);
    $hPsw1_time = round(($hPsw1_finish_time - $hPsw1_start_time)*1000000, 2);

    echo '
        <section class="crypt">
            <h2>Chiffrements</h2>
                <h3>1. DES standard (CRYPT_STD_DES)</h3>
                    <h4>Utilisateur</h4>
                        <p>' . $username . '</p>
                    <h4>Mot de passe crypté (en ' . $hPsw1_time . ' microsecondes)</h4>
                        <p>' . $hashedPsw1 . '</p>
                    <h4>Ligne à insérer dans le fichier htpasswd.txt</h4>
                        <p>' . $username . ':' . $hashedPsw1 . '</p>';

    /*2.------------------ Extended DES ------------------*/

    //For Hashing CRYPT_EXT_DES  :
    $hPsw2_start_time = microtime(true);
    $hashedPsw2 = crypt($_POST['password'], '_' . $salt);
    $hPsw2_finish_time = microtime(true);
    $hPsw2_time = round(($hPsw2_finish_time - $hPsw2_start_time)*1000000, 2);
    
    echo '      
                <h3>2. DES étendu (CRYPT_EXT_DES)</h3>
                    <h4>Utilisateur</h4>
                        <p>' . $username . '</p>
                    <h4>Mot de passe crypté (en ' . $hPsw2_time . ' microsecondes)</h4>
                        <p>' . $hashedPsw2 . '</p>
                    <h4>Ligne à insérer dans le fichier htpasswd.txt</h4>
                        <p>' . $username . ':' . $hashedPsw2 . '</p>';

    /*3.------------------ MD5 ------------------*/

    //For Hashing CRYPT_MD5 :
    $hPsw3_start_time = microtime(true);
    $hashedPsw3 = crypt($_POST['password'], '$1$' . $salt);
    $hPsw3_finish_time = microtime(true);
    $hPsw3_time = round(($hPsw3_finish_time - $hPsw3_start_time)*1000000, 2);

    echo '      
                <h3>3. MD5 (CRYPT_MD5)</h3>
                    <h4>Utilisateur</h4>
                        <p>' . $username . '</p>
                    <h4>Mot de passe crypté (en ' . $hPsw3_time . ' microsecondes)</h4>
                        <p>' . $hashedPsw3 . '</p>
                    <h4>Ligne à insérer dans le fichier htpasswd.txt</h4>
                        <p>' . $username . ':' . $hashedPsw3 . '</p>';

    /*4.------------------ BLOWFISH ------------------*/

    echo '      
                <h3>4. Blowfish</h3>';

    $blowfishParam2Digits = random_int(4, 31);
    if ($blowfishParam2Digits < 10) {
        $blowfishParam2Digits = '0' . $blowfishParam2Digits;
    }

    echo '
                    <p><em>blowfishParam2Digits doit être dans l\'intervalle [04-31] : ' . $blowfishParam2Digits . '</em></p>';

    //For Hashing  CRYPT_BLOWFISH $2a$ :
    $hPsw4a_start_time = microtime(true);
    $hashedPsw4a = crypt($_POST['password'], '$2a$' . $blowfishParam2Digits . '$' . $salt);
    $hPsw4a_finish_time = microtime(true);
    $hPsw4a_time = round(($hPsw4a_finish_time - $hPsw4a_start_time)*1000000, 2);

    echo '      
                <h3>4.a Blowfish (CRYPT_BLOWFISH) ($2a$)</h3>
                    <h4>Utilisateur</h4>
                        <p>' . $username . '</p>
                    <h4>Mot de passe crypté (en ' . $hPsw4a_time . ' microsecondes)</h4>
                        <p>' . $hashedPsw4a . '</p>
                    <h4>Ligne à insérer dans le fichier htpasswd.txt</h4>
                        <p>' . $username . ':' . $hashedPsw4a . '</p>';

    //For Hashing  CRYPT_BLOWFISH $2x$ :
    $hPsw4x_start_time = microtime(true);
    $hashedPsw4x = crypt($_POST['password'], '$2x$' . $blowfishParam2Digits . '$' . $salt);
    $hPsw4x_finish_time = microtime(true);
    $hPsw4x_time = round(($hPsw4x_finish_time - $hPsw4x_start_time)*1000000, 2);

    echo '      
                <h3>4.x Blowfish (CRYPT_BLOWFISH) ($2x$)</h3>
                    <h4>Utilisateur</h4>
                        <p>' . $username . '</p>
                    <h4>Mot de passe crypté (en ' . $hPsw4x_time . ' microsecondes)</h4>
                        <p>' . $hashedPsw4x . '</p>
                    <h4>Ligne à insérer dans le fichier htpasswd.txt</h4>
                        <p>' . $username . ':' . $hashedPsw4x . '</p>';


    //For Hashing  CRYPT_BLOWFISH $2y$ :
    $hPsw4y_start_time = microtime(true);
    $hashedPsw4y = crypt($_POST['password'], '$2y$' . $blowfishParam2Digits . '$' . $salt);
    $hPsw4y_finish_time = microtime(true);
    $hPsw4y_time = round(($hPsw4y_finish_time - $hPsw4y_start_time)*1000000, 2);

    echo '      
                <h3>4.y Blowfish (CRYPT_BLOWFISH) ($2y$)</h3>
                    <h4>Utilisateur</h4>
                        <p>' . $username . '</p>
                    <h4>Mot de passe crypté (en ' . $hPsw4y_time . ' microsecondes)</h4>
                        <p>' . $hashedPsw4y . '</p>
                    <h4>Ligne à insérer dans le fichier htpasswd.txt</h4>
                        <p>' . $username . ':' . $hashedPsw4y . '</p>';

    /*5.------------------ SHA256 ------------------*/

    //For Hashing CRYPT_SHA256 :
    $hPsw5_start_time = microtime(true);
    $hashedPsw5 = crypt($_POST['password'], '$5$' . $salt);
    $hPsw5_finish_time = microtime(true);
    $hPsw5_time = round(($hPsw5_finish_time - $hPsw5_start_time)*1000000, 2);

    echo '      
                <h3>5. SHA-256 (CRYPT_SHA256)</h3>
                    <h4>Utilisateur</h4>
                        <p>' . $username . '</p>
                    <h4>Mot de passe crypté (en ' . $hPsw5_time . ' microsecondes)</h4>
                        <p>' . $hashedPsw5 . '</p>
                    <h4>Ligne à insérer dans le fichier htpasswd.txt</h4>
                        <p>' . $username . ':' . $hashedPsw5 . '</p>';

    /*6.------------------ SHA512 ------------------*/

    //For Hashing CRYPT_SHA512 :
    $hPsw6_start_time = microtime(true);
    $hashedPsw6 = crypt($_POST['password'], '$6$' . $salt);
    $hPsw6_finish_time = microtime(true);
    $hPsw6_time = round(($hPsw6_finish_time - $hPsw6_start_time)*1000000, 2);

    echo '      
                <h3>6. SHA-512 (CRYPT_SHA512)</h3>
                    <h4>Utilisateur</h4>
                        <p>' . $username . '</p>
                    <h4>Mot de passe crypté (en ' . $hPsw6_time . ' microsecondes)</h4>
                        <p>' . $hashedPsw6 . '</p>
                    <h4>Ligne à insérer dans le fichier htpasswd.txt</h4>
                        <p>' . $username . ':' . $hashedPsw6 . '</p>
        </section>';

    //Generating File 1

    $GenFile1_start_time = microtime(true);

    $file1Name = "htaccess.txt";

    $file1AlertMessage = "WARNING - RESTRICTED AREA (NOT 51 BECAUSE NO UFO PRESENCE)";
    $file1DocumentRootPathWithWorkingFile = $workingPath;
    $file1DocumentRootPath = str_replace($workingFile, ".htpasswd", $file1DocumentRootPathWithWorkingFile);

    $file1TextLine1 = "AuthName \"$file1AlertMessage\"";
    $file1TextLine2 = "\nAuthType Basic";
    $file1TextLine3 = "\nAuthUserFile \"$file1DocumentRootPath\"";
    $file1TextLine4 = "\nRequire valid-user";

    $file1Text = $file1TextLine1 . $file1TextLine2 . $file1TextLine3 . $file1TextLine4;

    file_put_contents($file1Name, $file1Text);

    $GenFile1_finish_time = microtime(true);
    $GenFile1_time = round(($GenFile1_finish_time - $GenFile1_start_time)*1000000, 2);

    //Generating File 2

    $file2Name = "htpasswd.txt";

    $GenFile2_start_time = microtime(true);
    
    $file2Username = $username;
    $file2EncryptedPassword = $hashedPsw6;

    $file2TextLine1 =  $file2Username . ":" . $file2EncryptedPassword;

    $file2Text = $file2TextLine1;

    file_put_contents($file2Name, $file2Text);

    $GenFile2_finish_time = microtime(true);
    $GenFile2_time = round(($GenFile2_finish_time - $GenFile2_start_time)*1000000, 2);

    echo '
        <section class="genfile">
            <h2>Génération des fichiers</h2>
                <h3>1. Génération du fichier ' . $file1Name . '</h3>
                    <p>Fichier généré en ' . $GenFile1_time . ' microsecondes.</p>
                <h3>2 . Génération du fichier ' . $file2Name . '</h3>
                    <p>Fichier généré en ' . $GenFile2_time . ' microsecondes.</p>
        </section>';

    $processus_finish_time = microtime(true);

    //Times Calculation

    $t00 = $salt_time;
    $t01 = $hPsw1_time;
    $t02 = $hPsw2_time;
    $t03 = $hPsw3_time;
    $t04 = $hPsw4a_time;
    $t05 = $hPsw4x_time;
    $t06 = $hPsw4y_time;
    $t07 = $hPsw5_time;
    $t08 = $hPsw6_time;
    $t09 = $GenFile1_time;
    $t10 = $GenFile2_time;

    $t00ms = round(($salt_time / 1000), 2);
    $t01ms = round(($hPsw1_time / 1000), 2);
    $t02ms = round(($hPsw2_time / 1000), 2);
    $t03ms = round(($hPsw3_time / 1000), 2);
    $t04ms = round(($hPsw4a_time / 1000), 2);
    $t05ms = round(($hPsw4x_time / 1000), 2);
    $t06ms = round(($hPsw4y_time / 1000), 2);
    $t07ms = round(($hPsw5_time / 1000), 2);
    $t08ms = round(($hPsw6_time / 1000), 2);
    $t09ms = round(($GenFile1_time / 1000), 2);
    $t10ms = round(($GenFile2_time / 1000), 2);

    $t00s = round(($salt_time / 1000000), 2);
    $t01s = round(($hPsw1_time / 1000000), 2);
    $t02s = round(($hPsw2_time / 1000000), 2);
    $t03s = round(($hPsw3_time / 1000000), 2);
    $t04s = round(($hPsw4a_time / 1000000), 2);
    $t05s = round(($hPsw4x_time / 1000000), 2);
    $t06s = round(($hPsw4y_time / 1000000), 2);
    $t07s = round(($hPsw5_time / 1000000), 2);
    $t08s = round(($hPsw6_time / 1000000), 2);
    $t09s = round(($GenFile1_time / 1000000), 2);
    $t10s = round(($GenFile2_time / 1000000), 2);

    $t04Blowfish = $hPsw4a_time + $hPsw4x_time + $hPsw4y_time;
    $t04Blowfishms = round(($t04Blowfish / 1000), 2);
    $t04Blowfishs = round(($t04Blowfish / 1000000), 2);

    $totalProcessus = round(($processus_finish_time - $processus_start_time)*1000000, 2);;
    $totalActions = $t00+
                    $t01+
                    $t02+
                    $t03+
                    $t04+
                    $t05+
                    $t06+
                    $t07+
                    $t08+
                    $t09+
                    $t10;
                    
    $t11 = $totalProcessus;
    $t12 = $totalActions;

    $t11ms = round(($totalProcessus / 1000), 2);
    $t12ms = round(($totalActions / 1000), 2);

    $t11s = round(($totalProcessus / 1000000), 2);
    $t12s = round(($totalActions / 1000000), 2);

    //Times Table

        echo '
        <section class="timestable">
            <h2>Tableau des temps</h2>
                <table>
                    <tr>
                        <th>Numéro</th>
                        <th>Action</th>
                        <th>Temps en<br />microsecondes</th>
                        <th>Temps en<br />millisecondes</th>
                        <th>Temps en<br />secondes</th>
                    </tr>
                    <tr>
                        <td>00</td>
                        <td><b>Salt</b></td>
                        <td>' . $t00 . '</td>
                        <td>' . $t00ms . '</td>
                        <td>' . $t00s . '</td>
                    </tr>
                    <tr>
                        <td>01</td>
                        <td><b>DES standard</b><br />(CRYPT_STD_DES)</td>
                        <td>' . $t01 . '</td>
                        <td>' . $t01ms . '</td>
                        <td>' . $t01s . '</td>
                    </tr>
                    <tr>
                        <td>02</td>
                        <td><b>DES étendu</b><br />(CRYPT_EXT_DES)</td>
                        <td>' . $t02 . '</td>
                        <td>' . $t02ms . '</td>
                        <td>' . $t02s . '</td>
                    </tr>
                    <tr>
                        <td>03</td>
                        <td><b>MD5</b><br />(CRYPT_MD5)</td>
                        <td>' . $t03 . '</td>
                        <td>' . $t03ms . '</td>
                        <td>' . $t03s . '</td>
                    </tr>
                    <tr>
                        <td>04</td>
                        <td><b><em>Blowfish<br />(les trois)</em></b></td>
                        <td>' . $t04Blowfish . '</td>
                        <td>' . $t04Blowfishms . '</td>
                        <td>' . $t04Blowfishs . '</td>
                    </tr>
                    <tr>
                        <td>05</td>
                        <td><b>Blowfish</b><br />(CRYPT_BLOWFISH) <b>($2a$)</b></td>
                        <td>' . $t04 . '</td>
                        <td>' . $t04ms . '</td>
                        <td>' . $t04s . '</td>
                    </tr>
                    <tr>
                        <td>06</td>
                        <td><b>Blowfish</b><br />(CRYPT_BLOWFISH) <b>($2x$)</b></td>
                        <td>' . $t05 . '</td>
                        <td>' . $t05ms . '</td>
                        <td>' . $t05s . '</td>
                    </tr>
                    <tr>
                        <td>07</td>
                        <td><b>Blowfish</b><br />(CRYPT_BLOWFISH) <b>($2y$)</b></td>
                        <td>' . $t06 . '</td>
                        <td>' . $t06ms . '</td>
                        <td>' . $t06s . '</td>
                    </tr>
                    <tr>
                        <td>08</td>
                        <td><b>SHA-256</b><br />(CRYPT_SHA256)</td>
                        <td>' . $t07 . '</td>
                        <td>' . $t07ms . '</td>
                        <td>' . $t07s . '</td>
                    </tr>
                    <tr>
                        <td>09</td>
                        <td><b>SHA-512</b><br />(CRYPT_SHA512)</td>
                        <td>' . $t08 . '</td>
                        <td>' . $t08ms . '</td>
                        <td>' . $t08s . '</td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td>Génération du fichier 1<br /><b>htaccess.txt</b></td>
                        <td>' . $t09 . '</td>
                        <td>' . $t09ms . '</td>
                        <td>' . $t09s . '</td>
                    </tr>
                    <tr>
                        <td>11</td>
                        <td>Génération du fichier 2<br /><b>htpasswd.txt</b></td>
                        <td>' . $t10 . '</td>
                        <td>' . $t10ms . '</td>
                        <td>' . $t10s . '</td>
                    </tr>
                    <tr>
                        <td>12</td>
                        <td><b>Total<br />Processus</b></td>
                        <td>' . $t11 . '</td>
                        <td>' . $t11ms . '</td>
                        <td>' . $t11s . '</td>
                    </tr>
                    <tr>
                        <td>13</td>
                        <td><b>Total<br />des actions</b></td>
                        <td>' . $t12 . '</td>
                        <td>' . $t12ms . '</td>
                        <td>' . $t12s . '</td>
                    </tr>
                </table>
                
        </section>';

    echo '</body>';

    /*-------------------- Fin Html ------------------*/

    echo '</html>';

} else {

    $phpV = phpversion();
    $workingFile = pathinfo($_SERVER['PHP_SELF'], PATHINFO_BASENAME);
    $workingPath = realpath($workingFile);
}
?>

<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Crypt Folder Access | Loïc Drouet</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="css-secondary/normalize.css">
    <link rel="stylesheet" href="css-secondary/mystyle.css">

</head>

<body>
    <main>

        <header>
            <h1>Protection d'un accès à un dossier web par cryptage<br />et génération des fichiers .htaccess et .htpasswd<br />(à partir de PHP 8.0.0, le sel n'est plus optionnel, avant oui).</h1>
        </header>

        <section class="infos">
            <h2>Informations</h2>
                <h3>Version actuelle de PHP :</h3>
                <p><?php echo $phpV ?></p>
                <h3>Chemin courant :</h3>
                <p><?php echo $workingPath ?></p>
        </section>

        <section class="ids">
            <form method="post">
                <fieldset>
                    <legend>Vos identifiants</legend>
                    <table>
                        <tr>
                            <td><label>Nom d'utilisateur</label></td>
                            <td><input type="text" name="username" placeholder="pseudo" /> </td>
                        </tr>
                        <tr>
                            <td><label>Mot de passe</label></td>
                            <td><input type="text" name="password" placeholder="mot de passe" /></td>
                        </tr>
                        <tr>
                            <td><input type="submit" value="Crypter"></td>
                        </tr>
                    </table>
                </fieldset>
            </form>
        </section>

        <section class="sources">
            <p>
                <ol>
                    <strong><em>Sources :</em></strong>
                    <li><em><a href="https://openclassrooms.com/fr/courses/918836-concevez-votre-site-web-avec-php-et-mysql/912352-exploitez-toute-la-puissance-des-fonctions-php" target="_blank">Les fonctions - Concevez votre site web avec PHP et MySQL - OpenClassrooms</a></em></li>
                    <li><em><a href="https://www.infomaniak.com/fr/support/faq/2076/proteger-un-dossier-de-votre-site-par-mot-de-passe-via-un-htaccess" target="_blank">Protéger un dossier de votre site par mot de passe via un .htaccess</a></em></li>
                    <li><em><a href="https://www.php.net/manual/fr/function.crypt.php" target="_blank">Php.net, section "Historique" :  <strong>PHP 8.0.0 :	Le salt n'est plus optionnel !!!</strong></a></em></li>
                    <li><em><a href="https://stackoverflow.com/questions/18305789/correctly-using-crypt-with-sha512-in-php" target="_blank">Stack Overflow : Correctly using crypt() with SHA512 in PHP</a></em></li>
                </ol>
            </p>
        </section>


    </main>

</body>

</html>